<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KasirPenjualanController extends Controller
{
    public function index(Request $request)
    {
        // ── 1. QUERY UTAMA ────────────────────────────────────────────
        // Eager load semua relasi yang dibutuhkan tabel & popup struk.
        // 'items.menu' → nama menu di baris tabel & detail struk
        // 'table'      → nomor meja di kolom "No Meja"
        $query = Order::with(['items.menu', 'table'])->latest();

        // ── 2. FITUR PENCARIAN ────────────────────────────────────────
        // Server-side search berdasarkan kode order atau nama pelanggan.
        // Ini dipakai form GET di view kasir/penjualan.blade.php.
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('customer_name', 'LIKE', "%{$keyword}%");
            });
        }

        // ── 3. PAGINASI ───────────────────────────────────────────────
        // withQueryString() mempertahankan parameter ?q=... saat ganti halaman.
        $orders = $query->paginate(10)->withQueryString();

        // ── 4. MAPPED ORDERS UNTUK JAVASCRIPT ────────────────────────
        // Data bersih ini di-pass ke window.penjualanData di blade.
        // PENTING: mapping dilakukan SETELAH paginate dengan getCollection()
        // agar tidak ada error kurung siku @json() di dalam Blade.
        $mappedOrders = $orders->getCollection()->map(function ($order) {

            // Format waktu: "02 Apr 2026 - 14:20 WIB"
            $waktu = $order->created_at
                ? $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y - H:i') . ' WIB'
                : '-';

            // Nomor meja (ambil dari relasi table, fallback Take Away)
            $tableLabel = $order->table
                ? 'Meja ' . str_pad($order->table->table_number, 2, '0', STR_PAD_LEFT)
                : 'Take Away';

            // Map setiap item pesanan menjadi array sederhana
            $items = $order->items->map(function ($item) {
                return [
                    'nama'  => $item->menu->name ?? 'Menu Dihapus',
                    'qty'   => (int) $item->quantity,
                    'price' => (int) $item->price,
                    'note'  => $item->note ?? '',
                ];
            })->values()->toArray();

            return [
                'id'         => $order->id,
                'order_code' => $order->order_code,
                'customer'   => $order->customer_name ?? '-',
                'waktu'      => $waktu,
                'table'      => $tableLabel,
                'order_type' => strtoupper($order->eating_option ?? $order->order_type ?? 'DINE IN'),
                'status'     => strtoupper($order->status),
                'subtotal'   => (int) $order->subtotal,
                'tax'        => (int) $order->tax,
                'total'      => (int) $order->total,
                'items'      => $items,
            ];
        })->values()->toArray();

        return view('kasir.penjualan', compact('orders', 'mappedOrders'));
    }
}