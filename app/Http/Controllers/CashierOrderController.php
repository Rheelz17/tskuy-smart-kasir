<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CashierOrderController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout(Request $request)
    {
        $cartItems = $request->input('items');
        if (empty($cartItems)) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }

        // 1. Hitung Ulang Total Harga (Demi Keamanan Data)
        $subtotal = 0;
        $itemDetails = [];

        foreach ($cartItems as $item) {
            $itemPrice = (int) $item['price'];
            $itemQty = (int) $item['qty'];
            $subtotal += $itemPrice * $itemQty;

            $itemDetails[] = [
                'id' => $item['id'],
                'price' => $itemPrice,
                'quantity' => $itemQty,
                'name' => substr($item['name'], 0, 50), // Batasi panjang nama karakter
            ];
        }

        $tax = (int) round($subtotal * 0.1);
        $totalTagihan = $subtotal + $tax;

        // Tambahkan komponen pajak ke detail item Midtrans
        $itemDetails[] = [
            'id' => 'TAX-10',
            'price' => $tax,
            'quantity' => 1,
            'name' => 'Tax PPN (10%)'
        ];

        // 2. Simpan Data ke Database (Gunakan Transaction DB agar aman)
        DB::beginTransaction();
        try {
            // Sesuaikan properti field di bawah ini dengan struktur tabel buatan kelompok Anda
            $order = Order::create([
                'table_id' => null, // Kasir memesankan langsung di meja POS kasir
                'user_id' => auth()->id(), // ID Kasir yang melayani
                'total_price' => $totalTagihan,
                'status' => 'pending', 
                'type' => 'dine in', // Bisa dibuat dinamis jika ada pilihan tipe order
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_name' => $item['name'], // Sesuaikan nama kolom tabel kelompok Anda
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'note' => $item['note'] ?? null,
                ]);
            }

            // Buat ID Transaksi Unik untuk Midtrans
            $transactionCode = 'TSKUY-' . time() . '-' . $order->id;

            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionCode,
                'payment_method' => 'qris',
                'amount' => $totalTagihan,
                'status' => 'pending',
            ]);

            // 3. Susun Payload untuk Dikirim ke Midtrans
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $transactionCode,
                    'gross_amount' => $totalTagihan,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => 'Pelanggan Warkop',
                    'email' => 'customer@tskuy.com',
                ]
            ];

            // 4. Minta Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($midtransParams);

            DB::commit();

            // Kembalikan token ke Frontend JavaScript
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi Pengubah Status Otomatis saat Pelanggan Selesai Scan QRIS & Bayar
    public function handleNotification(Request $request)
    {
        $serverKey = env('SB-Mid-server-bvCLYkn6O3LlgVFRfwXt3Pji');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        // Validasi kecocokan signature key keamanan dari Midtrans
        if ($hashed === $request->signature_key) {
            $transactionStatus = $request->transaction_status;
            $orderIdInCode = explode('-', $request->order_id)[2] ?? null;

            if ($orderIdInCode) {
                $order = Order::find($orderIdInCode);
                $payment = Payment::where('order_id', $orderIdInCode)->first();

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    if ($order) $order->update(['status' => 'success']);
                    if ($payment) $payment->update(['status' => 'success']);
                    
                    // TODO: Memicu Event Pusher di sini agar Frontend Kasir auto-update layar sukses!
                }
            }
        }
        return response()->json(['status' => 'OK']);
    }

}
