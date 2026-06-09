<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use App\Models\Order;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;

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

    // public function checkout(Request $request)
    // {
    //     $request->validate([
    //         'type'  => 'required|in:open_bill,pay_now',
    //         'items' => 'required|array',
    //     ]);

    //     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     Config::$isProduction = false;
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     $tableNumber = $request->table_number ?? Cookie::get('tskuy_table_number') ?? '4';
    //     $user        = auth()->user();

    //     $tableRow = DB::table('tables')->where('table_number', $tableNumber)->first();
    //     $tableId  = $tableRow ? $tableRow->id : null;

    //     $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT);

    //     DB::beginTransaction();
    //     try {
    //         $subtotal = 0;
    //         foreach ($request->items as $item) {
    //             $menu = Menu::findOrFail($item['id']);
    //             $subtotal += $menu->price * $item['qty'];
    //         }
    //         $tax = $subtotal * 0.10;
    //         $total = $subtotal + $tax;

    //         // 1. Simpan Order
    //         $orderId = DB::table('orders')->insertGetId([
    //             'order_code'    => $orderCode,
    //             'customer_name' => $user->name ?? 'Pelanggan',
    //             'table_id'      => $tableId,
    //             'order_type'    => 'self order',
    //             'source'        => 'qr',
    //             'status'        => ($request->type === 'pay_now') ? 'PENDING' : 'COOKING',
    //             'eating_option' => $request->eating_option ?? 'dine in',
    //             'is_open_bill'  => $request->type === 'open_bill' ? 1 : 0,
    //             'subtotal'      => $subtotal,
    //             'tax'           => $tax,
    //             'total'         => $total,
    //             'payment_status'=> 'pending',
    //             'created_by'    => $user ? $user->id : null,
    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ]);

    //         // 2. Simpan Item
    //         foreach ($request->items as $item) {
    //             $menu = Menu::find($item['id']);
    //             DB::table('orders_item')->insert([
    //                 'order_id'   => $orderId,
    //                 'menu_id'    => $menu->id,
    //                 'quantity'   => $item['qty'],
    //                 'price'      => $menu->price,
    //                 'note'       => $item['catatan'] ?? null,
    //                 'status'     => 'PENDING',
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }

    //         // 3. Catat Payment Awal
    //         DB::table('payments')->insert([
    //             'order_id'         => $orderId,
    //             'reference_number' => $orderCode,
    //             'payment_method'   => 'qris',
    //             'total_amount'     => $total,
    //             'paid_amount'      => 0,
    //             'status'           => 'PENDING',
    //             'created_at'       => now(),
    //             'updated_at'       => now(),
    //         ]);

    //         // 4. Jika Pay Now, Bikin Snap Token Midtrans
    //         if ($request->type === 'pay_now') {
    //             $transactionId = 'ORDER-' . $orderId . '-' . time();
    //             $params = [
    //                 'transaction_details' => ['order_id' => $transactionId, 'gross_amount' => (int)$total],
    //                 'customer_details'    => ['first_name' => $user->name, 'email' => $user->email]
    //             ];
    //             $snapToken = Snap::getSnapToken($params);
                
    //             DB::table('orders')->where('id', $orderId)->update([
    //                 'transaction_id' => $transactionId, 'snap_token' => $snapToken
    //             ]);
    //             DB::commit();
                
    //             return response()->json([
    //                 'success' => true, 'snap_token' => $snapToken, 'order_code' => $orderCode
    //             ]);
    //         }

    //         // Jika Open Bill, langsung sukses masuk Dapur
    //         DB::commit();
    //         return response()->json([
    //             'success' => true, 'message' => 'Pesanan dikirim ke dapur!', 'redirect_url' => route('pelanggan.riwayat')
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    public function checkout(Request $request)
    {
        $request->validate([
            'type'  => 'required|in:open_bill,pay_now',
            'items' => 'required|array',
        ]);

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $tableNumber = $request->table_number ?? Cookie::get('tskuy_table_number') ?? '4';
        $user        = auth()->user();

        $tableRow = DB::table('tables')->where('table_number', $tableNumber)->first();
        $tableId  = $tableRow ? $tableRow->id : null;

        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            
            // ─── VALIDASI STOK SERVER-SIDE (PENCEGAHAN UTAMA) ───
            foreach ($request->items as $item) {
                $menu = Menu::lockForUpdate()->findOrFail($item['id']);                
                if ($menu->stock < $item['qty']) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Maaf, stok untuk menu '{$menu->name}' tidak mencukupi! Sisa stok saat ini: {$menu->stock} porsi."
                    ], 422); // Status 422 Unprocessable Entity
                }
                
                $subtotal += $menu->price * $item['qty'];
            }
            
            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            // 1. Simpan Order
            $orderId = DB::table('orders')->insertGetId([
                'order_code'    => $orderCode,
                'customer_name' => $user->name ?? 'Pelanggan',
                'table_id'      => $tableId,
                'order_type'    => 'self order',
                'source'        => 'qr',
                'status'        => ($request->type === 'pay_now') ? 'PENDING' : 'COOKING',
                'eating_option' => $request->eating_option ?? 'dine in',
                'is_open_bill'  => $request->type === 'open_bill' ? 1 : 0,
                'subtotal'      => $subtotal,
                'tax'           => $tax,
                'total'         => $total,
                'payment_status'=> 'pending',
                'created_by'    => $user ? $user->id : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 2. Simpan Item & Potong Stok
            foreach ($request->items as $item) {
                $menu = Menu::find($item['id']);
                
                // BERUBAH: Mengurangi stok menu secara real-time di DB
                $menu->decrement('stock', $item['qty']);

                DB::table('orders_item')->insert([
                    'order_id'   => $orderId,
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['qty'],
                    'price'      => $menu->price,
                    'note'       => $item['catatan'] ?? null,
                    'status'     => 'PENDING',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Catat Payment Awal
            DB::table('payments')->insert([
                'order_id'         => $orderId,
                'reference_number' => $orderCode,
                'payment_method'   => 'qris',
                'total_amount'     => $total,
                'paid_amount'      => 0,
                'status'           => 'PENDING',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // // 4. Jika Pay Now, Bikin Snap Token Midtrans
            // if ($request->type === 'pay_now') {
            //     $transactionId = 'ORDER-' . $orderId . '-' . time();
            //     $params = [
            //         'transaction_details' => ['order_id' => $transactionId, 'gross_amount' => (int)$total],
            //         'customer_details'    => ['first_name' => $user->name, 'email' => $user->email]
            //     ];
            //     $snapToken = Snap::getSnapToken($params);
                
            //     DB::table('orders')->where('id', $orderId)->update([
            //         'transaction_id' => $transactionId, 'snap_token' => $snapToken
            //     ]);
            //     DB::commit();
                
            //     return response()->json([
            //         'success' => true, 'snap_token' => $snapToken, 'order_code' => $orderCode
            //     ]);
            // }

            // Jika Open Bill, langsung sukses masuk Dapur
            DB::commit();
            // return response()->json([
            //     'success' => true, 'message' => 'Pesanan dikirim ke dapur!', 'redirect_url' => route('pelanggan.riwayat')
            // ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        // ─────────────────────────────────────────────────────────
        // TAHAP 2: PROSES LUAR TRANSAKSI (HUBUNGI API MIDTRANS)
        // ─────────────────────────────────────────────────────────
        if ($request->type === 'pay_now') {
            try {
                $transactionId = 'ORDER-' . $orderId . '-' . time();
                $params = [
                    'transaction_details' => ['order_id' => $transactionId, 'gross_amount' => (int)$total],
                    'customer_details'    => ['first_name' => $user->name, 'email' => $user->email]
                ];
                
                // Pemanggilan API eksternal dilakukan dengan aman tanpa mengunci database row lagi
                $snapToken = Snap::getSnapToken($params);
                
                // Update token hasil dari Midtrans ke baris order yang sudah di-commit tadi
                DB::table('orders')->where('id', $orderId)->update([
                    'transaction_id' => $transactionId, 
                    'snap_token'     => $snapToken
                ]);
                
                return response()->json([
                    'success' => true, 
                    'snap_token' => $snapToken, 
                    'order_code' => $orderCode
                ]);
                
            } catch (\Exception $e) {
                // Jika koneksi Midtrans bermasalah, kirim respon error ke pelanggan
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()
                ], 500);
            }
        }

        // Jika Open Bill, langsung sukses arahkan ke riwayat dapur
        return response()->json([
            'success' => true, 
            'message' => 'Pesanan dikirim ke dapur!', 
            'redirect_url' => route('pelanggan.riwayat')
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // WEBHOOK MIDTRANS CALLBACK (Dipanggil oleh Ngrok otomatis)
    // ─────────────────────────────────────────────────────────
    // public function midtransCallback(Request $request)
    // {
    //     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        
    //     try {
    //         $notification = new Notification();
    //         $order = DB::table('orders')->where('transaction_id', $notification->order_id)->first();

    //         if ($order && in_array($notification->transaction_status, ['settlement', 'capture'])) {
    //             DB::table('payments')->where('order_id', $order->id)->update([
    //                 'status' => 'PAID', 'paid_amount' => $order->total, 'paid_at' => now()
    //             ]);
    //             DB::table('orders')->where('id', $order->id)->update([
    //                 'status' => $order->is_open_bill ? 'COMPLETED' : 'COOKING',
    //                 'payment_status' => 'paid'
    //             ]);
    //         }
    //         return response()->json(['success' => true]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function midtransCallback(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        
        try {
            $notification = new Notification();
            $order = DB::table('orders')->where('transaction_id', $notification->order_id)->first();

            if ($order) {
                $statusMidtrans = $notification->transaction_status;

                // KONDISI A: Pembayaran Berhasil
                if (in_array($statusMidtrans, ['settlement', 'capture'])) {
                    DB::table('payments')->where('order_id', $order->id)->update([
                        'status' => 'PAID', 'paid_amount' => $order->total, 'paid_at' => now()
                    ]);
                    DB::table('orders')->where('id', $order->id)->update([
                        'status' => $order->is_open_bill ? 'COMPLETED' : 'COOKING',
                        'payment_status' => 'paid'
                    ]);
                } 
                // KONDISI B: Pembayaran Gagal / Expired / Dibatalkan Pelanggan
                // KITA KEMBALIKAN STOK BARANG YANG TADI SUDAH TERPOTONG
                elseif (in_array($statusMidtrans, ['expire', 'cancel', 'deny'])) {
                    if ($order->status === 'PENDING') {
                        $orderItems = DB::table('orders_item')->where('order_id', $order->id)->get();
                        
                        foreach ($orderItems as $item) {
                            Menu::where('id', $item->menu_id)->increment('stock', $item->quantity);
                        }

                        DB::table('orders')->where('id', $order->id)->update([
                            'status' => 'CANCELLED',
                            'payment_status' => 'failed'
                        ]);
                        DB::table('payments')->where('order_id', $order->id)->update([
                            'status' => 'FAILED'
                        ]);
                    }
                }
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    // SISA KODE LU (JANGAN DIUBAH)
    // ─────────────────────────────────────────────────────────
    public function continueOrder($orderId) {
        $order = DB::table('orders')->where('id', $orderId)->first();
        if(!$order || $order->payment_status === 'paid') return redirect()->route('pelanggan.riwayat');
        session(['active_order_id' => $orderId]);
        return redirect()->route('pelanggan.orders');
    }

    // public function addMoreItems(Request $request, $orderId) {
    //     $subtotalBaru = 0;
    //     foreach ($request->items as $item) {
    //         $menu = DB::table('menus')->where('id', $item['id'])->first();
    //         DB::table('orders_item')->insert([
    //             'order_id' => $orderId, 'menu_id' => $menu->id, 'quantity' => $item['qty'],
    //             'price' => $menu->price, 'status' => 'PENDING', 'created_at' => now()
    //         ]);
    //         $subtotalBaru += ($menu->price * $item['qty']);
    //     }
    //     $order = DB::table('orders')->where('id', $orderId)->first();
    //     $totalBaru = $order->subtotal + $subtotalBaru;
    //     $taxBaru = $totalBaru * 0.10;
    //     DB::table('orders')->where('id', $orderId)->update([
    //         'subtotal' => $totalBaru, 'tax' => $taxBaru, 'total' => $totalBaru + $taxBaru,
    //         'status' => 'PENDING', 'updated_at' => now()
    //     ]);
    //     session()->forget('active_order_id');
    //     return response()->json(['success' => true, 'redirect_url' => route('pelanggan.riwayat')]);
    // }

    public function addMoreItems(Request $request, $orderId) {
        $request->validate([
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $subtotalBaru = 0;

            // 1. Validasi Stok Item Tambahan terlebih dahulu
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['id']);
                if ($menu->stock < $item['qty']) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok untuk '{$menu->name}' tidak mencukupi untuk tambahan pesanan ini. Sisa: {$menu->stock} porsi."
                    ], 422);
                }
                $subtotalBaru += ($menu->price * $item['qty']);
            }

            // 2. Simpan Item & Potong Stok jika validasi lolos
            foreach ($request->items as $item) {
                $menu = Menu::find($item['id']);
                
                // Kurangi stok menu tambahan
                $menu->decrement('stock', $item['qty']);

                DB::table('orders_item')->insert([
                    'order_id'   => $orderId, 
                    'menu_id'    => $menu->id, 
                    'quantity'   => $item['qty'],
                    'price'      => $menu->price, 
                    'note'       => $item['catatan'] ?? null,
                    'status'     => 'PENDING', 
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $order = DB::table('orders')->where('id', $orderId)->first();
            $totalBaru = $order->subtotal + $subtotalBaru;
            $taxBaru = $totalBaru * 0.10;

            DB::table('orders')->where('id', $orderId)->update([
                'subtotal'   => $totalBaru, 
                'tax'        => $taxBaru, 
                'total'      => $totalBaru + $taxBaru,
                'status'     => 'COOKING', // Otomatis balik masak lagi di dapur karena ada menu baru masuk
                'updated_at' => now()
            ]);

            DB::commit();
            session()->forget('active_order_id');
            return response()->json(['success' => true, 'redirect_url' => route('pelanggan.riwayat')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function finishBill($orderCode) {
        return redirect()->route('pelanggan.qris', ['orderCode' => $orderCode]);
    }

    public function showQris($orderCode) {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order || !in_array($order->status, ['PENDING', 'COOKING'])) return redirect()->route('pelanggan.riwayat');
        return view('pelanggan.qris', compact('order'));
    }

    public function simulatePay($orderCode) {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order) return redirect()->back();
        DB::table('payments')->where('order_id', $order->id)->update(['status' => 'PAID', 'paid_amount' => $order->total, 'paid_at' => now()]);
        DB::table('orders')->where('id', $order->id)->update(['status' => $order->is_open_bill ? 'COMPLETED' : 'COOKING']);
        return redirect()->route('pelanggan.payment.success', $orderCode);
    }

    public function paymentSuccess($orderCode) {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order || $order->status !== 'COMPLETED') return redirect()->route('pelanggan.orders');
        return view('pelanggan.payment-success', compact('order'));
    }
}