<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KokiController extends Controller
{
    /**
     * GET /koki
     * Tampilkan antrian pesanan dapur.
     *
     * Struktur $orders yang dikirim ke view:
     *   id              → ID unik pesanan (string)
     *   id_nota         → Nomor nota/order (untuk Takeaway)
     *   tipe_pesanan    → 'Dine In' | 'Takeaway'
     *   nama_pelanggan  → Nama tamu
     *   nomor_meja      → String "01" dst, NULL jika Takeaway
     *   detail_pesanan  → array [ ['nama', 'qty', 'catatan'], ... ]
     *   status          → 'pending' | 'completed'
     *   created_at      → ISO 8601 string (kapan pesanan masuk dapur)
     */
    public function index()
    {
        $orders = collect([

            // ── Dine In — baru masuk (<20 mnt, hijau/normal) ──────
            [
                'id'             => 'TKY-001',
                'id_nota'        => 'T001',
                'tipe_pesanan'   => 'Dine In',
                'nama_pelanggan' => 'Budi Santoso',
                'nomor_meja'     => '05',
                'detail_pesanan' => [
                    ['nama' => 'Nasi Goreng Spesial', 'qty' => 2, 'catatan' => 'Pedas sedang'],
                    ['nama' => 'Pancong Keju',         'qty' => 1, 'catatan' => 'Setengah mateng'],
                    ['nama' => 'Es Teh Manis',          'qty' => 2, 'catatan' => ''],
                ],
                'status'     => 'pending',
                'created_at' => now()->subMinutes(4)->toISOString(),
            ],

            // ── Dine In — menengah (<20 mnt, masih hijau/normal) ──
            [
                'id'             => 'TKY-002',
                'id_nota'        => 'T002',
                'tipe_pesanan'   => 'Dine In',
                'nama_pelanggan' => 'Siti Rahayu',
                'nomor_meja'     => '03',
                'detail_pesanan' => [
                    ['nama' => 'Mie Goreng Seafood',  'qty' => 1, 'catatan' => 'Ekstra sayur'],
                    ['nama' => 'Jus Alpukat',          'qty' => 1, 'catatan' => 'Tanpa susu'],
                    ['nama' => 'Cireng Crispy',        'qty' => 2, 'catatan' => ''],
                    ['nama' => 'Teh Hangat',           'qty' => 1, 'catatan' => ''],
                ],
                'status'     => 'pending',
                'created_at' => now()->subMinutes(11)->toISOString(),
            ],

            // ── Dine In URGENT — >20 mnt, JS otomatis jadikan MERAH ──
            [
                'id'             => 'TKY-003',
                'id_nota'        => 'T003',
                'tipe_pesanan'   => 'Dine In',
                'nama_pelanggan' => 'Hendra Wijaya',
                'nomor_meja'     => '07',
                'detail_pesanan' => [
                    ['nama' => 'Ayam Geprek Sambal Matah', 'qty' => 2, 'catatan' => 'Sambal dipisah'],
                    ['nama' => 'Nasi Putih',               'qty' => 2, 'catatan' => ''],
                    ['nama' => 'Lemon Tea Dingin',          'qty' => 2, 'catatan' => 'Es banyak'],
                ],
                'status'     => 'pending',
                'created_at' => now()->subMinutes(24)->toISOString(), // >20 → merah otomatis
            ],

            // ── Takeaway — baru masuk (<20 mnt) ───────────────────
            [
                'id'             => 'TKY-004',
                'id_nota'        => 'T004',
                'tipe_pesanan'   => 'Takeaway',
                'nama_pelanggan' => 'Dewi Kusuma',
                'nomor_meja'     => null,              // Takeaway: null
                'detail_pesanan' => [
                    ['nama' => 'Kopi Susu Gula Aren', 'qty' => 2, 'catatan' => 'Kurangi gula'],
                    ['nama' => 'Roti Bakar Keju',      'qty' => 1, 'catatan' => 'Panggang kering'],
                ],
                'status'     => 'pending',
                'created_at' => now()->subMinutes(7)->toISOString(),
            ],

            // ── Takeaway URGENT — >20 mnt ─────────────────────────
            [
                'id'             => 'TKY-005',
                'id_nota'        => 'T005',
                'tipe_pesanan'   => 'Takeaway',
                'nama_pelanggan' => 'Raka Pratama',
                'nomor_meja'     => null,
                'detail_pesanan' => [
                    ['nama' => 'Paket Nasi Telor', 'qty' => 1, 'catatan' => 'Ekstra kecap'],
                    ['nama' => 'Es Jeruk',          'qty' => 1, 'catatan' => ''],
                ],
                'status'     => 'pending',
                'created_at' => now()->subMinutes(22)->toISOString(), // >20 → merah otomatis
            ],

            // ── Sudah Selesai ──────────────────────────────────────
            [
                'id'             => 'TKY-006',
                'id_nota'        => 'T006',
                'tipe_pesanan'   => 'Dine In',
                'nama_pelanggan' => 'Linda Agustina',
                'nomor_meja'     => '01',
                'detail_pesanan' => [
                    ['nama' => 'Kentang Goreng', 'qty' => 2, 'catatan' => ''],
                    ['nama' => 'Teh Hangat',      'qty' => 2, 'catatan' => ''],
                ],
                'status'     => 'completed',
                'created_at' => now()->subMinutes(35)->toISOString(),
            ],

        ]);

        return view('koki.index', compact('orders'));
    }

    /**
     * POST /koki/{id}/selesaikan
     * Tandai pesanan sebagai selesai (AJAX dari koki.js).
     *
     * TODO: Ganti dummy logic ini dengan update ke DB nyata:
     *   Order::where('id', $id)->update(['status' => 'completed', 'completed_at' => now()]);
     *   + trigger notifikasi ke kasir & pelanggan via event/broadcast
     */
    public function selesaikan(Request $request, string $id)
    {
        // Validasi input dasar
        $request->validate(['id' => 'sometimes|string']);

        // --- PLACEHOLDER: Update ke DB & broadcast ---
        // $order = Order::findOrFail($id);
        // $order->update(['status' => 'completed', 'kitchen_done_at' => now()]);
        // event(new OrderSelesai($order));
        // ---------------------------------------------

        return response()->json([
            'success' => true,
            'message' => "Pesanan #{$id} berhasil ditandai selesai.",
            'id'      => $id,
        ]);
    }
}