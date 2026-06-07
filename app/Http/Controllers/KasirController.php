<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
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
                'orders.eating_option',
                'orders.is_open_bill',
                'orders.subtotal',
                'orders.tax',
                'orders.total',
                'orders.created_at',
                'tables.table_number'
            )
            ->get();

        $antrian->transform(function ($order) {
            // Filter type — pakai eating_option buat dine/takeaway
            if ($order->is_open_bill) {
                $order->filter_type = 'openbill';
            } elseif (str_contains(strtolower($order->eating_option ?? ''), 'take')) {
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
                'orders.eating_option',
                'orders.source',
                'orders.is_open_bill',
                'orders.total',
                'orders.created_at',
                'tables.table_number'
            )
            ->get();

        // Hitung waktu relatif tiap antrian
        $antrian->transform(function ($order) {
            $order->time_label = $this->relativeTime($order->created_at);

            $minutes = Carbon::parse($order->created_at)->diffInMinutes(now());
            if ($minutes <= 10)      $order->queue_color = 'queue-green';
            elseif ($minutes <= 20)  $order->queue_color = 'queue-yellow';
            else                     $order->queue_color = 'queue-red';

            // Tentukan tipe untuk filter tab — pakai eating_option
            if ($order->is_open_bill) {
                $order->filter_type = 'openbill';
            } elseif (str_contains(strtolower($order->eating_option ?? ''), 'take')) {
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
    public function posCheckout(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'payment_method' => 'required|in:cash,qris',
            'items'          => 'required|array|min:1',
            'cash_received'  => 'nullable|numeric|min:0',
        ]);

        $kasirId   = auth()->id();
        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-KSR';

        DB::beginTransaction();
        try {
            // Hitung subtotal dari harga DB (bukan dari JS, lebih aman)
            $subtotal = 0;
            $menuCache = [];
            foreach ($request->items as $item) {
                $menu = Menu::find($item['id']);
                if ($menu) {
                    $subtotal += $menu->price * $item['qty'];
                    $menuCache[$menu->id] = $menu;
                }
            }
            $tax   = $subtotal * 0.10;
            $total = $subtotal + $tax;

            $isCash = $request->payment_method === 'cash';

            // Insert ke tabel orders
            // order_type = 'cashier' (source dari kasir)
            // eating_option = 'take away' (kasir selalu take away sesuai keputusan)
            $orderId = DB::table('orders')->insertGetId([
                'order_code'     => $orderCode,
                'customer_name'  => $request->customer_name,
                'table_id'       => null,
                'order_type'     => 'cashier',
                'eating_option'  => 'take away',
                'source'         => 'kasir',
                'status'         => $isCash ? 'COOKING' : 'PENDING',
                'is_open_bill'   => 0,
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $total,
                'payment_status' => $isCash ? 'paid' : 'pending',
                'created_by'     => $kasirId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Insert orders_item
            foreach ($request->items as $item) {
                $menu = $menuCache[$item['id']] ?? Menu::find($item['id']);
                if (!$menu) continue;

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

            // Insert payments
            DB::table('payments')->insert([
                'order_id'         => $orderId,
                'reference_number' => $orderCode,
                'payment_method'   => $request->payment_method,
                'total_amount'     => $total,
                'paid_amount'      => $isCash ? ($request->cash_received ?? $total) : 0,
                'status'           => $isCash ? 'PAID' : 'PENDING',
                'paid_at'          => $isCash ? now() : null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::commit();

            // Cash → struk di POS | QRIS → halaman scan
            $redirectUrl = $isCash
                ? route('kasir.payment.success', ['orderCode' => $orderCode])
                : route('kasir.qris', ['orderCode' => $orderCode]);

            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
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
    //  TAMPILAN HALAMAN QRIS (kasir)
    // =========================================================
    public function showQris($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();

        if (!$order || !in_array($order->status, ['PENDING', 'COOKING'])) {
            return redirect()->route('kasir.pos')
                ->with('error', 'Pesanan tidak valid atau sudah diproses.');
        }

        return view('kasir.qris', compact('order'));
    }

    // =========================================================
    //  SIMULASI / KONFIRMASI PEMBAYARAN QRIS (kasir)
    // =========================================================
    public function simulatePay($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            DB::table('payments')->where('order_id', $order->id)->update([
                'status'      => 'PAID',
                'paid_amount' => $order->total,
                'paid_at'     => now(),
                'updated_at'  => now(),
            ]);

            DB::table('orders')->where('id', $order->id)->update([
                'status'         => 'COOKING',
                'payment_status' => 'paid',
                'updated_at'     => now(),
            ]);

            DB::commit();
            return redirect()->route('kasir.payment.success', $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  STRUK SUKSES (kasir) — tampil setelah cash atau QRIS paid
    // =========================================================
    public function paymentSuccess($orderCode)
    {
        $order = DB::table('orders')->where('order_code', $orderCode)->first();

        if (!$order || $order->payment_status !== 'paid') {
            return redirect()->route('kasir.pos')
                ->with('error', 'Struk tidak tersedia.');
        }

        $items = DB::table('orders_item')
            ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
            ->where('orders_item.order_id', $order->id)
            ->select('menus.name as menu_name', 'orders_item.quantity', 'orders_item.price', 'orders_item.note')
            ->get();

        $payment = DB::table('payments')->where('order_id', $order->id)->first();

        return view('kasir.payment-success', compact('order', 'items', 'payment'));
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
    public function penjualan()
    {
        $transactions = Order::with([
                'items.menu',
                'payments',
                'table',
            ])
            ->latest()
            ->paginate(10);

        return view('kasir.penjualan', compact('transactions'));
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