<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function initializeTable($number)
    {
        Cookie::queue('tskuy_table_number', $number, 300);
        return redirect()->route('pelanggan.orders');
    }

    public function index()
    {
        $tableNumber = Cookie::get('tskuy_table_number');
        $moods = Mood::all();
        $categories = Category::with('menus.moods')->get();

        return view('pelanggan.ordersPelanggan', compact('categories', 'moods', 'tableNumber'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:open_bill,pay_now',
            'items' => 'required|array',
        ]);

        $tableNumber = Cookie::get('tskuy_table_number') ?? '4';
        $user        = auth()->user();

        $tableRow = DB::table('tables')->where('table_number', $tableNumber)->first();
        $tableId  = $tableRow ? $tableRow->id : null;

        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // 1. Simpan Order (Status PENDING)
            $orderId = DB::table('orders')->insertGetId([
                'order_code'    => $orderCode,
                'customer_name' => $user->name ?? 'Pelanggan',
                'table_id'      => $tableId,
                'order_type'    => 'self order',
                'source'        => 'qr',
                'status'        => 'PENDING',
                'eating_option' => 'dine in',
                'is_open_bill'  => $request->type === 'open_bill' ? 1 : 0,
                'subtotal'      => 0,
                'tax'           => 0,
                'total'         => 0,
                'created_by'    => $user ? $user->id : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $subtotalSum = 0;

            // 2. Simpan Item
            foreach ($request->items as $item) {
                $menu = DB::table('menus')->where('id', $item['id'])->first();
                if ($menu) {
                    $itemPrice    = $menu->price;
                    $itemSubtotal = $itemPrice * $item['qty'];
                    $subtotalSum += $itemSubtotal;

                    DB::table('orders_item')->insert([
                        'order_id'   => $orderId,
                        'menu_id'    => $menu->id,
                        'quantity'   => $item['qty'],
                        'price'      => $itemPrice,
                        'note'       => $item['catatan'] ?? null,
                        'status'     => 'PENDING',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Kalkulasi Pajak & Total
            $taxAmount   = $subtotalSum * 0.10;
            $totalAmount = $subtotalSum + $taxAmount;

            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotalSum,
                'tax'      => $taxAmount,
                'total'    => $totalAmount,
            ]);

            // 4. Catat Payment PENDING (Wajib QRIS)
            DB::table('payments')->insert([
                'order_id'         => $orderId,
                'reference_number' => $orderCode,
                'payment_method'   => 'qris',
                'total_amount'     => $totalAmount,
                'paid_amount'      => 0,
                'status'           => 'PENDING',
                'paid_at'          => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // 5. Pengkondisian Alur
            if ($request->type === 'pay_now') {
                DB::commit();
                return response()->json([
                    'success'      => true,
                    'redirect_url' => route('pelanggan.qris', ['orderCode' => $orderCode])
                ]);
            } else {
                // Open Bill: langsung masuk dapur
                DB::table('orders')->where('id', $orderId)->update(['status' => 'COOKING']);
                DB::commit();
                return response()->json([
                    'success'      => true,
                    'message'      => 'Pesanan Open Bill berhasil dikirim ke dapur!',
                    'redirect_url' => route('pelanggan.riwayat')
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses database: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    // OPEN BILL: Redirect ke menu dengan membawa orderId
    // ─────────────────────────────────────────────────────────
    public function continueOrder($orderId)
    {
        $order = DB::table('orders')->where('id', $orderId)->first();
        if (!$order || $order->status !== 'COOKING') {
            return redirect()->route('pelanggan.riwayat');
        }

        session(['active_order_id' => $orderId]);
        return redirect()->route('pelanggan.orders');
    }

    // ─────────────────────────────────────────────────────────
    // OPEN BILL: Tambah item ke order yang sudah ada
    // ─────────────────────────────────────────────────────────
    public function addMoreItems(Request $request, $orderId)
    {
        $subtotalBaru = 0;

        foreach ($request->items as $item) {
            $menu = DB::table('menus')->where('id', $item['id'])->first();
            if ($menu) {
                DB::table('orders_item')->insert([
                    'order_id'   => $orderId,
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['qty'],
                    'price'      => $menu->price,
                    'status'     => 'PENDING',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $subtotalBaru += $menu->price * $item['qty'];
            }
        }

        // Recalculate total order
        $order       = DB::table('orders')->where('id', $orderId)->first();
        $newSubtotal = $order->subtotal + $subtotalBaru;
        $newTax      = $newSubtotal * 0.10;
        $newTotal    = $newSubtotal + $newTax;

        DB::table('orders')->where('id', $orderId)->update([
            'subtotal'   => $newSubtotal,
            'tax'        => $newTax,
            'total'      => $newTotal,
            'updated_at' => now(),
        ]);

        // Update juga payment record supaya total_amount sinkron
        DB::table('payments')->where('order_id', $orderId)->update([
            'total_amount' => $newTotal,
            'updated_at'   => now(),
        ]);

        session()->forget('active_order_id');

        return response()->json([
            'success'      => true,
            'redirect_url' => route('pelanggan.riwayat')
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // OPEN BILL: Selesaikan tagihan → redirect ke QRIS bayar lunas
    // ─────────────────────────────────────────────────────────
    public function finishBill($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();

        if (!$order || !in_array($order->status, ['COOKING', 'PENDING'])) {
            return redirect()->route('pelanggan.riwayat')
                ->with('error', 'Pesanan tidak valid atau sudah diselesaikan.');
        }

        // Pastikan payment record ada & total sudah sinkron
        $payment = DB::table('payments')->where('order_id', $order->id)->first();
        if (!$payment) {
            DB::table('payments')->insert([
                'order_id'         => $order->id,
                'reference_number' => $order->order_code,
                'payment_method'   => 'qris',
                'total_amount'     => $order->total,
                'paid_amount'      => 0,
                'status'           => 'PENDING',
                'paid_at'          => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } else {
            DB::table('payments')->where('order_id', $order->id)->update([
                'total_amount' => $order->total,
                'status'       => 'PENDING',
                'updated_at'   => now(),
            ]);
        }

        return redirect()->route('pelanggan.qris', ['orderCode' => $orderCode]);
    }

    // ─────────────────────────────────────────────────────────
    // TAMPILAN HALAMAN QRIS
    // (Support: pay_now baru & open_bill yang mau bayar lunas)
    // ─────────────────────────────────────────────────────────
    public function showQris($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();

        if (!$order || !in_array($order->status, ['PENDING', 'COOKING'])) {
            return redirect()->route('pelanggan.riwayat')
                ->with('error', 'Pesanan tidak valid atau sudah diproses.');
        }

        return view('pelanggan.qris', compact('order'));
    }

    // ─────────────────────────────────────────────────────────
    // SIMULASI / KONFIRMASI PEMBAYARAN QRIS
    // ─────────────────────────────────────────────────────────
    public function simulatePay($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // Update payment jadi PAID
            DB::table('payments')->where('order_id', $order->id)->update([
                'status'      => 'PAID',
                'paid_amount' => $order->total,
                'paid_at'     => now(),
                'updated_at'  => now(),
            ]);

            // Update status order jadi COMPLETED
            DB::table('orders')->where('id', $order->id)->update([
                'status'     => 'COMPLETED',
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('pelanggan.payment.success', $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────
    // TAMPILAN STRUK SUKSES
    // ─────────────────────────────────────────────────────────
    public function paymentSuccess($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();

        // Hanya tampil jika sudah COMPLETED
        if (!$order || $order->status !== 'COMPLETED') {
            return redirect()->route('pelanggan.orders');
        }

        return view('pelanggan.payment-success', compact('order'));
    }
}