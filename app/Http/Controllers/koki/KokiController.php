<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * KokiController
 * ---------------------------------------------------------------
 * Mengelola halaman dapur (kitchen display) untuk role koki.
 *
 * Alur status pesanan:
 *   PENDING  →  COOKING  →  READY  (→ COMPLETED oleh kasir)
 *
 * Alur status item:
 *   PENDING  →  COOKING  →  READY  (atau REJECTED)
 * ---------------------------------------------------------------
 */
class KokiController extends Controller
{
    // ============================================================
    // GET /koki — Halaman utama antrian dapur
    // ============================================================
    public function index()
    {
        /*
         * Ambil semua order yang masih aktif:
         *   - PENDING  : baru masuk, belum mulai dimasak
         *   - COOKING  : sedang diproses koki
         *   - READY    : sudah selesai, menunggu konfirmasi kasir
         *
         * Kita sertakan juga COMPLETED agar koki bisa lihat riwayat
         * hari ini (bisa difilter di frontend).
         */
        $rawOrders = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', ['PENDING', 'COOKING', 'READY'])
            // Tambahkan COMPLETED untuk riwayat hari ini
            ->orWhere(function ($q) {
                $q->where('orders.status', 'COMPLETED')
                  ->whereDate('orders.updated_at', today());
            })
            ->orderByRaw("FIELD(orders.status, 'PENDING','COOKING','READY','COMPLETED')")
            ->orderBy('orders.created_at', 'asc')
            ->select('orders.*', 'tables.table_number')
            ->get();

        // Transformasi ke format yang dibutuhkan view
        $orders = $rawOrders->map(function ($order) {

            // Ambil semua item pesanan beserta nama menu
            $items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select(
                    'orders_item.id as item_id',
                    'menus.name as nama',
                    'orders_item.quantity as qty',
                    'orders_item.note as catatan',
                    'orders_item.status as item_status',
                    'orders_item.rejected_reason'
                )
                ->get()
                ->toArray();

            /*
             * Pemetaan tipe pesanan:
             *   order_type = 'self order'  → Dine In (scan QR meja)
             *   order_type = 'cashier'     → Bawa Pulang / order kasir
             *
             * Sesuaikan logika ini dengan kebutuhan bisnis Anda.
             */
            $tipePesanan = ($order->order_type === 'self order') ? 'Dine In' : 'Take Away';

            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'tipe_pesanan'   => $tipePesanan,
                'source'         => $order->source,         // 'qr' / 'kasir'
                'nama_pelanggan' => $order->customer_name ?? 'Pelanggan',
                'nomor_meja'     => $order->table_number,
                'detail_pesanan' => $items,
                'status'         => strtolower($order->status), // 'pending'|'cooking'|'ready'|'completed'
                'is_open_bill'   => (bool) $order->is_open_bill,
                'subtotal'       => $order->subtotal,
                'created_at'     => $order->created_at,
            ];
        });

        return view('koki.index', compact('orders'));
    }

    // ============================================================
    // POST /koki/{id}/mulai-masak — Ubah status PENDING → COOKING
    // ============================================================
    public function mulaiMasak(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Validasi transisi status
            if ($order->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => "Pesanan #{$id} tidak berstatus PENDING (status saat ini: {$order->status}).",
                ], 422);
            }

            DB::beginTransaction();

            // Update status order utama
            $order->update([
                'status'     => 'COOKING',
                'updated_at' => now(),
            ]);

            // Update semua item yang masih PENDING → COOKING
            DB::table('orders_item')
                ->where('order_id', $id)
                ->where('status', 'PENDING')
                ->update(['status' => 'COOKING', 'updated_at' => now()]);

            // Catat log
            DB::table('order_logs')->insert([
                'order_id'   => $id,
                'action'     => 'UPDATE',
                'description'=> 'Koki mulai memasak pesanan.',
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "Pesanan #{$id} mulai dimasak!",
                'new_status' => 'COOKING',
                'id'         => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // POST /koki/{id}/selesaikan — Ubah status → READY
    // (bisa dari PENDING atau COOKING)
    // ============================================================
    public function selesaikan(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Hanya boleh dari PENDING atau COOKING
            if (!in_array($order->status, ['PENDING', 'COOKING'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Pesanan #{$id} tidak bisa diselesaikan (status: {$order->status}).",
                ], 422);
            }

            DB::beginTransaction();

            // Update order utama ke READY
            $order->update([
                'status'     => 'READY',
                'updated_at' => now(),
            ]);

            // Update semua item yang belum REJECTED → READY
            DB::table('orders_item')
                ->where('order_id', $id)
                ->whereNotIn('status', ['REJECTED'])
                ->update(['status' => 'READY', 'updated_at' => now()]);

            // Catat log
            DB::table('order_logs')->insert([
                'order_id'   => $id,
                'action'     => 'UPDATE',
                'description'=> 'Koki menyelesaikan pesanan — status menjadi READY.',
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "Pesanan #{$id} sudah SIAP!",
                'new_status' => 'READY',
                'id'         => $id,
                'order_code' => $order->order_code,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // POST /koki/{orderId}/item/{itemId}/update
    // Update status satu item (PENDING → COOKING → READY)
    // ============================================================
    public function updateItem(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'status' => 'required|in:PENDING,COOKING,READY,REJECTED',
        ]);

        try {
            $item = OrderItem::where('id', $itemId)
                ->where('order_id', $orderId)
                ->firstOrFail();

            $item->update([
                'status'          => $request->status,
                'rejected_reason' => $request->status === 'REJECTED' ? ($request->reason ?? null) : null,
                'updated_at'      => now(),
            ]);

            // Cek apakah semua item sudah READY/REJECTED → auto update order
            $pendingCount = DB::table('orders_item')
                ->where('order_id', $orderId)
                ->whereNotIn('status', ['READY', 'REJECTED'])
                ->count();

            $orderAutoReady = false;
            if ($pendingCount === 0) {
                DB::table('orders')
                    ->where('id', $orderId)
                    ->where('status', 'COOKING')
                    ->update(['status' => 'READY', 'updated_at' => now()]);
                $orderAutoReady = true;
            }

            return response()->json([
                'success'          => true,
                'message'          => "Item #{$itemId} diperbarui ke {$request->status}.",
                'item_id'          => $itemId,
                'new_status'       => $request->status,
                'order_auto_ready' => $orderAutoReady,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // POST /koki/{id}/batalkan — Batalkan pesanan
    // Hanya boleh saat status PENDING
    // ============================================================
    public function batalkan(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $order = Order::findOrFail($id);

            if ($order->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => "Pesanan #{$id} tidak bisa dibatalkan (status: {$order->status}). Hanya pesanan PENDING yang bisa dibatalkan.",
                ], 422);
            }

            DB::beginTransaction();

            $order->update([
                'status'     => 'CANCELLED',
                'updated_at' => now(),
            ]);

            DB::table('orders_item')
                ->where('order_id', $id)
                ->update(['status' => 'REJECTED', 'rejected_reason' => $request->reason ?? 'Dibatalkan koki', 'updated_at' => now()]);

            DB::table('order_logs')->insert([
                'order_id'   => $id,
                'action'     => 'CANCEL',
                'description'=> 'Pesanan dibatalkan oleh koki. Alasan: ' . ($request->reason ?? '-'),
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "Pesanan #{$id} berhasil dibatalkan.",
                'new_status' => 'CANCELLED',
                'id'         => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // GET /koki/api/orders — Polling endpoint untuk refresh antrian
    // Digunakan oleh koki.js untuk auto-refresh tanpa reload halaman
    // ============================================================
    public function apiOrders()
    {
        $rawOrders = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', ['PENDING', 'COOKING', 'READY'])
            ->orderByRaw("FIELD(orders.status, 'PENDING','COOKING','READY')")
            ->orderBy('orders.created_at', 'asc')
            ->select('orders.*', 'tables.table_number')
            ->get();

        $orders = $rawOrders->map(function ($order) {
            $items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select(
                    'orders_item.id as item_id',
                    'menus.name as nama',
                    'orders_item.quantity as qty',
                    'orders_item.note as catatan',
                    'orders_item.status as item_status'
                )
                ->get()
                ->toArray();

            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'tipe_pesanan'   => ($order->order_type === 'self order') ? 'Dine In' : 'Take Away',
                'nama_pelanggan' => $order->customer_name ?? 'Pelanggan',
                'nomor_meja'     => $order->table_number,
                'detail_pesanan' => $items,
                'status'         => strtolower($order->status),
                'created_at'     => $order->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'orders'  => $orders,
            'counts'  => [
                'semua'   => $orders->count(),
                'pending' => $orders->where('status', 'pending')->count(),
                'cooking' => $orders->where('status', 'cooking')->count(),
                'ready'   => $orders->where('status', 'ready')->count(),
            ],
        ]);
    }
}