<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Auth;
use DB;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        dd('CREATE TERPANGGIL');

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $request->total,
            'payment_status' => 'pending'
        ]);

        $orderId = 'ORDER-'.$order->id.'-'.time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $order->total
            ],

            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email
            ]
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $order->update([
            'transaction_id' => $orderId,
            'snap_token' => $snapToken
        ]);

        return response()->json([
            'snap_token' => $snapToken
        ]);
    }

    public function callback(Request $request)
    {
        $notification = new Notification();
        $transactionStatus =
            $notification->transaction_status;
        $orderId =
            $notification->order_id;
        $order = Order::where(
            'transaction_id',
            $orderId
        )->first();
        if (!$order) {
            return response()->json([
                'error' => true
            ]);
        }
        if (
            $transactionStatus == 'settlement'
            ||
            $transactionStatus == 'capture'
        ) {
            $order->update([
                'payment_status' => 'paid'
            ]);
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function payNow(Request $request)
    {
        try {

            // =========================
            // DEBUG REQUEST
            // =========================
            if (!$request->has('items')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Items tidak ditemukan'
                ], 400);
            }

            // =========================
            // HITUNG TOTAL
            // =========================
            $subtotal = 0;

            foreach ($request->items as $item) {

                $menu = Menu::findOrFail($item['id']);

                $subtotal += $menu->price * $item['qty'];
            }

            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            // =========================
            // BUAT ORDER
            // =========================
            $order = Order::create([
                'order_code'     => 'ORD' . time(),
                'customer_name'  => Auth::user()->name,
                'order_type'     => 'self order',
                'source'         => 'qr',
                'status'         => 'pending',
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $total,
                'created_by'     => Auth::id()
            ]);

            // =========================
            // SIMPAN ITEM
            // =========================
            foreach ($request->items as $item) {

                $menu = Menu::findOrFail($item['id']);

                DB::table('orders_item')->insert([
                    'order_id'   => $order->id,
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['qty'],
                    'price'      => $menu->price,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // =========================
            // MIDTRANS CONFIG
            // =========================
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // =========================
            // GENERATE TOKEN
            // =========================
            $orderId = 'ORDER-' . $order->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int)$total
                ],

                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            // =========================
            // UPDATE ORDER
            // =========================
            $order->update([
                'transaction_id' => $orderId,
                'snap_token' => $snapToken
            ]);

            // =========================
            // RETURN TOKEN
            // =========================
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);

        }
    }
}
