<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KasirController extends Controller
{
    // =========================================================
    //  API ANTRIAN — JSON untuk polling realtime di JS
    //  GET /kasir/api/antrian
    // =========================================================
    public function apiAntrian()
    {
        $antrian = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', ['PENDING', 'COOKING', 'READY'])
            ->orderBy('orders.created_at', 'asc')
            ->select(
                'orders.id',
                'orders.order_code',
                'orders.status',
                'orders.order_type',
                'orders.is_open_bill',
                'orders.subtotal',
                'orders.tax',
                'orders.total',
                'orders.created_at',
                'tables.table_number'
            )
            ->get();

        $antrian->transform(function ($order) {
            // Filter type
            if ($order->is_open_bill) {
                $order->filter_type = 'openbill';
            } elseif (str_contains(strtolower($order->order_type ?? ''), 'take')) {
                $order->filter_type = 'takeaway';
            } else {
                $order->filter_type = 'dine';
            }

            // Items
            $order->items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select(
                    'menus.name as menu_name',
                    'orders_item.quantity',
                    'orders_item.note',
                    'orders_item.price'
                )
                ->get();

            return $order;
        });

        // Counts per filter
        $counts = [
            'all'      => $antrian->count(),
            'pending'  => $antrian->where('status', 'PENDING')->count(),
            'dine'     => $antrian->where('filter_type', 'dine')->count(),
            'takeaway' => $antrian->where('filter_type', 'takeaway')->count(),
            'openbill' => $antrian->where('filter_type', 'openbill')->count(),
        ];

        return response()->json([
            'antrian' => $antrian->values(),
            'counts'  => $counts,
        ]);
    }

    // =========================================================
    //  HALAMAN POS — Tampilkan menu + antrian aktif dari DB
    // =========================================================
    public function pos()
    {
        // Ambil semua kategori beserta menu-nya (yang statusnya aktif)
        $categories = Category::with(['menus' => function ($q) {
            $q->where('is_available', 1)->orderBy('name');
        }])->get();

        // Ambil antrian aktif: order yang belum selesai/dibatalkan
        $antrian = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', ['PENDING', 'COOKING', 'READY'])
            ->orderBy('orders.created_at', 'asc')
            ->select(
                'orders.id',
                'orders.order_code',
                'orders.status',
                'orders.order_type',
                'orders.source',
                'orders.is_open_bill',
                'orders.total',
                'orders.created_at',
                'tables.table_number'
            )
            ->get();

        // Hitung waktu relatif tiap antrian (e.g. "Baru saja", "2 menit lalu")
        $antrian->transform(function ($order) {
            $order->time_label = $this->relativeTime($order->created_at);

            // Tentukan warna kartu berdasarkan lama tunggu
            $minutes = Carbon::parse($order->created_at)->diffInMinutes(now());
            if ($minutes <= 10)      $order->queue_color = 'queue-green';
            elseif ($minutes <= 20)  $order->queue_color = 'queue-yellow';
            else                     $order->queue_color = 'queue-red';

            // Tentukan tipe untuk filter tab
            if ($order->is_open_bill) {
                $order->filter_type = 'openbill';
            } elseif (str_contains(strtolower($order->order_type ?? ''), 'take')) {
                $order->filter_type = 'takeaway';
            } else {
                $order->filter_type = 'dine';
            }

            return $order;
        });

        // Ambil item tiap order untuk popup detail antrian
        foreach ($antrian as $order) {
            $order->items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select('orders_item.*', 'menus.name as menu_name', 'menus.image')
                ->get();
        }

        // Hitung badge count untuk tiap tab antrian
        $antrianCount = [
            'all'      => $antrian->count(),
            'pending'  => $antrian->where('status', 'PENDING')->count(),
            'dine'     => $antrian->where('filter_type', 'dine')->count(),
            'takeaway' => $antrian->where('filter_type', 'takeaway')->count(),
            'openbill' => $antrian->where('filter_type', 'openbill')->count(),
        ];

        // Ambil daftar meja untuk dropdown pilih meja
        $tables = DB::table('tables')->orderBy('table_number')->get();

        return view('kasir.pos', compact('categories', 'antrian', 'antrianCount', 'tables'));
    }

    // =========================================================
    //  CHECKOUT KASIR — Kasir input order manual via POS
    // =========================================================
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'table_id'       => 'nullable|integer',
            'order_type'     => 'required|in:dine_in,take_away',
            'payment_method' => 'required|in:cash,qris',
            'items'          => 'required|array|min:1',
            'cash_received'  => 'nullable|numeric|min:0',
        ]);

        $kasirId   = auth()->id();
        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-KSR';

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'order_code'    => $orderCode,
                'customer_name' => $request->customer_name,
                'table_id'      => $request->table_id,
                'order_type'    => $request->order_type === 'dine_in' ? 'dine in' : 'take away',
                'source'        => 'kasir',
                'status'        => 'PENDING',
                'is_open_bill'  => 0,
                'subtotal'      => 0,
                'tax'           => 0,
                'total'         => 0,
                'created_by'    => $kasirId,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $subtotal = 0;

            foreach ($request->items as $item) {
                $menu = DB::table('menus')->where('id', $item['id'])->first();
                if (!$menu) continue;

                $itemSubtotal = $menu->price * $item['qty'];
                $subtotal    += $itemSubtotal;

                // Gabungkan level pedas + catatan menjadi satu note
                $noteParts = [];
                if (isset($item['level']) && $item['level'] !== null) {
                    $noteParts[] = 'Level ' . $item['level'];
                }
                if (!empty($item['catatan'])) {
                    $noteParts[] = $item['catatan'];
                }
                $note = implode(' | ', $noteParts) ?: null;

                DB::table('orders_item')->insert([
                    'order_id'   => $orderId,
                    'menu_id'    => $menu->id,
                    'quantity'   => $item['qty'],
                    'price'      => $menu->price,
                    'note'       => $note,
                    'status'     => 'PENDING',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $tax   = $subtotal * 0.10;
            $total = $subtotal + $tax;

            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotal,
                'tax'      => $tax,
                'total'    => $total,
            ]);

            DB::commit();

            $kembalian = 0;
            if ($request->payment_method === 'cash' && $request->cash_received) {
                $kembalian = $request->cash_received - $total;
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Pesanan berhasil dibuat!',
                'order_id'   => $orderId,
                'order_code' => $orderCode,
                'total'      => $total,
                'tax'        => $tax,
                'subtotal'   => $subtotal,
                'kembalian'  => $kembalian,
                'order_type' => $request->order_type,
                'table_id'   => $request->table_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================
    //  SELESAIKAN ORDER — Kasir tandai order sebagai COMPLETED
    // =========================================================
    public function selesaikan(Request $request, $orderId)
    {
        $updated = DB::table('orders')
            ->where('id', $orderId)
            ->whereIn('status', ['PENDING', 'COOKING', 'READY'])
            ->update([
                'status'     => 'COMPLETED',
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Order ditandai selesai.']);
        }

        return response()->json(['success' => false, 'message' => 'Order tidak ditemukan atau sudah selesai.'], 404);
    }

    // =========================================================
    //  BATALKAN ORDER — Kasir batalkan order dengan alasan
    // =========================================================
    public function batalkan(Request $request, $orderId)
    {
        $request->validate(['alasan' => 'nullable|string|max:255']);

        $updated = DB::table('orders')
            ->where('id', $orderId)
            ->whereIn('status', ['PENDING', 'COOKING', 'READY'])
            ->update([
                'status'     => 'CANCELLED',
                'notes'      => $request->alasan ?? 'Dibatalkan oleh kasir',
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Order berhasil dibatalkan.']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal membatalkan order.'], 404);
    }

    // =========================================================
    //  MANAJEMEN MENU — Tampilkan semua menu + kategori
    // =========================================================
    public function manajemenMenu()
    {
        $categories = Category::orderBy('name')->get();

        $menus = Menu::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return view('kasir.manajemenMenu', compact('categories', 'menus'));
    }

    // =========================================================
    //  TOGGLE STATUS MENU — Aktif / Nonaktif via AJAX
    // =========================================================
    public function toggleMenu(Request $request, $menuId)
    {
        $menu = Menu::findOrFail($menuId);
        $menu->is_available = !$menu->is_available;
        $menu->save();

        return response()->json([
            'success'      => true,
            'is_available' => $menu->is_available,
        ]);
    }

    // =========================================================
    //  DETAIL PENJUALAN — Riwayat semua transaksi
    // =========================================================
    public function penjualan(Request $request)
    {
        $query = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->select(
                'orders.id',
                'orders.order_code',
                'orders.customer_name',
                'orders.order_type',
                'orders.source',
                'orders.status',
                'orders.subtotal',
                'orders.tax',
                'orders.total',
                'orders.created_at',
                'tables.table_number'
            )
            ->orderBy('orders.created_at', 'desc');

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('orders.order_code', 'like', "%$q%")
                   ->orWhere('orders.customer_name', 'like', "%$q%");
            });
        }

        $orders = $query->paginate(15);

        // Tarik item tiap order untuk popup detail
        foreach ($orders as $order) {
            $order->items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select('orders_item.*', 'menus.name as menu_name')
                ->get();
        }

        return view('kasir.penjualan', compact('orders'));
    }

    // =========================================================
    //  HELPER — Waktu relatif (Baru saja, X menit lalu, dst.)
    // =========================================================
    private function relativeTime($timestamp)
    {
        $minutes = Carbon::parse($timestamp)->diffInMinutes(now());

        if ($minutes < 1)  return 'Baru saja';
        if ($minutes < 60) return $minutes . ' menit lalu';

        $hours = floor($minutes / 60);
        return $hours . ' jam lalu';
    }
}