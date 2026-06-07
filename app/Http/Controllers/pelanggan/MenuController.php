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
            'type' => 'required|in:open_bill,pay_now',
            'items' => 'required|array',
        ]);

        $tableNumber = Cookie::get('tskuy_table_number') ?? '4';
        $user = auth()->user();

        // 1. Ambil ID Meja
        $tableRow = DB::table('tables')->where('table_number', $tableNumber)->first();
        $tableId = $tableRow ? $tableRow->id : null; 

        // 2. Generate Kode Order Unik
        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // 3. Insert ke tabel `orders`
            $orderId = DB::table('orders')->insertGetId([
                'order_code'    => $orderCode,
                'customer_name' => $user->name ?? 'Pelanggan',
                'table_id'      => $tableId,
                'order_type'    => 'self order', // Sesuai enum
                'source'        => 'qr',         // Sesuai enum
                'status'        => 'PENDING',
                'eating_option' => 'dine in',    // Sesuai default DB
                'is_open_bill'  => $request->type === 'open_bill' ? 1 : 0,
                'subtotal'      => 0, 
                'tax'           => 0, 
                'total'         => 0, 
                'created_by'    => $user ? $user->id : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $subtotalSum = 0;

            // 4. Insert ke tabel `orders_item`
            foreach ($request->items as $item) {
                $menu = DB::table('menus')->where('id', $item['id'])->first();
                if ($menu) {
                    $itemPrice = $menu->price;
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

            // 5. Kalkulasi Pajak & Total
            $taxAmount = $subtotalSum * 0.10;
            $totalAmount = $subtotalSum + $taxAmount;

            // 6. Update Total di tabel `orders`
            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotalSum,
                'tax'      => $taxAmount,
                'total'    => $totalAmount,
            ]);

            // 🔥 PLAN B: SIMULASI PEMBAYARAN TANPA MIDTRANS 🔥
            if ($request->type === 'pay_now') {
                // JIKA BAYAR LANGSUNG: Catat sebagai QRIS & status PAID (Lunas)
                DB::table('payments')->insert([
                    'order_id'         => $orderId,
                    'reference_number' => $orderCode,
                    'payment_method'   => 'qris', 
                    'total_amount'     => $totalAmount, 
                    'paid_amount'      => $totalAmount, 
                    'status'           => 'PAID', 
                    'paid_at'          => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

            } else {
                // JIKA OPEN BILL: Catat sebagai QRIS tapi status PENDING (Bayar nanti)
                DB::table('payments')->insert([
                    'order_id'         => $orderId,
                    'reference_number' => $orderCode,
                    'payment_method'   => 'qris', 
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => 0, // Duit belum masuk
                    'status'           => 'PENDING', // Status gantung
                    'paid_at'          => null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // 7. APAPUN METODENYA, LEMPAR KE DAPUR (Ubah status Order jadi COOKING)
            DB::table('orders')->where('id', $orderId)->update(['status' => 'COOKING']);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => $request->type === 'pay_now' 
                                    ? 'Pembayaran Simulasi Berhasil! Pesanan langsung diproses di dapur.' 
                                    : 'Pesanan Open Bill Berhasil dikirim ke dapur!',
                'redirect_url' => route('pelanggan.riwayat') 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses database: ' . $e->getMessage()
            ], 500);
        }
    }
}