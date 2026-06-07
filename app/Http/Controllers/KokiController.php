<?php

namespace App\Http\Controllers\koki;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KokiController extends Controller
{
    public function index()
    {
        $rawOrders = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->select('orders.*', 'tables.table_number')
            ->whereIn('orders.status', ['PENDING', 'COOKING'])
            ->where(function($query) {
                // LOGIKA: Sembunyikan "Pay Now" yang belum lunas
                $query->where('orders.is_open_bill', 1)
                      ->orWhere(function($q) {
                          $q->where('orders.is_open_bill', 0)
                            ->where('orders.payment_status', 'paid');
                      });
            })
            ->orderBy('orders.created_at', 'asc')
            ->get();

        $orders = [];
        foreach ($rawOrders as $o) {
            $items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $o->id)
                ->select('orders_item.*', 'menus.name as nama')
                ->get();

            $orders[] = [
                'id'             => $o->id,
                'order_code'     => $o->order_code,
                'tipe_pesanan'   => ucwords($o->eating_option),
                'nama_pelanggan' => $o->customer_name ?? 'Pelanggan',
                'nomor_meja'     => $o->table_number,
                'status'         => strtolower($o->status),
                'created_at'     => $o->created_at,
                'detail_pesanan' => $items->map(function($i) {
                    return [
                        'item_id' => $i->id,
                        'nama'    => $i->nama,
                        'qty'     => $i->quantity,
                        'catatan' => $i->note
                    ];
                })->toArray()
            ];
        }

        $orders = collect($orders);
        return view('koki.index', compact('orders'));
    }

    public function apiOrders()
    {
        $baseQuery = DB::table('orders')->where(function($query) {
            $query->where('is_open_bill', 1)
                  ->orWhere(function($q) {
                      $q->where('is_open_bill', 0)->where('payment_status', 'paid');
                  });
        });

        $counts = [
            'semua'     => (clone $baseQuery)->whereIn('status', ['PENDING', 'COOKING'])->count(),
            'pending'   => (clone $baseQuery)->where('status', 'PENDING')->count(),
            'cooking'   => (clone $baseQuery)->where('status', 'COOKING')->count(),
            'completed' => DB::table('orders')->where('status', 'COMPLETED')->whereDate('updated_at', today())->count(),
        ];
        return response()->json(['success' => true, 'counts' => $counts]);
    }

    public function mulaiMasak($id)
    {
        DB::table('orders')->where('id', $id)->update(['status' => 'COOKING', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function selesaikan($id)
    {
        DB::table('orders')->where('id', $id)->update(['status' => 'COMPLETED', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function batalkan($id)
    {
        DB::table('orders')->where('id', $id)->update(['status' => 'CANCELLED', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }
}