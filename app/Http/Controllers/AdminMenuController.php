<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use App\Models\Transaction; // 👈 1. WAJIB TAMBAH INI biar bisa narik data transaksi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // TITIPAN: 0. DASHBOARD & PENJUALAN
    // ──────────────────────────────────────────────────────────
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function penjualan()
    {
        // Ambil data transaksi terbaru + batasi 10 data per halaman (Pagination)
        // Gunakan ->with('details') jika relasi detail item transaksi ada di model kamu
        $transactions = Transaction::with('details')->latest()->paginate(10);

        // Lempar data variabel $transactions ke view utama admin penjualan
        return view('admin.penjualan', compact('transactions'));
    }

    // ──────────────────────────────────────────────────────────
    // 1. INDEX — Tampilkan halaman utama manajemen menu
    // ──────────────────────────────────────────────────────────
    public function index()
    {
        $menus      = Menu::with(['category', 'moods'])->latest()->get();
        $categories = Category::all();
        $moods      = Mood::all();

        return view('admin.menu', compact('menus', 'categories', 'moods'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. TAMBAH — Halaman form tambah menu (mobile full-page)
    //    Kirim $categories dan $moods agar form dapat dirender
    //    dengan pilihan dinamis dari database.
    // ──────────────────────────────────────────────────────────
    public function tambah()
    {
        $categories = Category::all();   // untuk <select> kategori
        $moods      = Mood::all();       // untuk chips mood

        return view('admin.menu-tambah', compact('categories', 'moods'));
    }

    // ──────────────────────────────────────────────────────────
    // 3. STORE — Proses simpan menu baru
    //    Mendukung AJAX Fetch dari Desktop Popup & Mobile Page
    // ──────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'description'    => $request->description,
            'image'          => $imagePath,
            'is_available'   => $request->has('is_available'),
            'is_recommended' => $request->has('is_recommended'),
            'is_new'         => $request->has('is_new'),
            'is_promo'       => $request->has('is_promo'),
        ]);

        // Sync relasi many-to-many ke tabel pivot menu_moods
        if ($request->has('moods')) {
            $menu->moods()->sync($request->moods);
        } else {
            $menu->moods()->sync([]); // Kosongkan jika tidak ada mood dipilih
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu baru berhasil ditambahkan!',
            'data'    => $menu->load('category', 'moods'),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────
    // 4. EDIT — Halaman form edit menu (mobile full-page)
    //    Kirim $menu beserta relasi moods yang sudah dipilih,
    //    $categories untuk select, $moods untuk semua pilihan chips.
    // ──────────────────────────────────────────────────────────
    public function edit($id)
    {
        // Eager load moods agar blade bisa cek $menu->moods->pluck('id')
        $menu       = Menu::with('moods')->findOrFail($id);
        $categories = Category::all();
        $moods      = Mood::all();

        return view('admin.menu-edit', compact('menu', 'categories', 'moods'));
    }

    // ──────────────────────────────────────────────────────────
    // 5. UPDATE — Proses update menu
    //    Mendukung method spoofing PUT dari Fetch API mobile.
    //    Bonus: tangani flag hapus_foto dari blade edit.
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $menu->image; // Pertahankan gambar lama secara default

        // Cek: user minta hapus foto (tombol Hapus di edit mobile)
        if ($request->input('hapus_foto') == '1') {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = null;
        }

        // Cek: ada file foto baru yang diupload (ganti foto)
        if ($request->hasFile('image')) {
            // Hapus foto lama sebelum simpan yang baru
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu->update([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'description'    => $request->description,
            'image'          => $imagePath,
            'is_available'   => $request->has('is_available'),
            'is_recommended' => $request->has('is_recommended'),
            'is_new'         => $request->has('is_new'),
            'is_promo'       => $request->has('is_promo'),
        ]);

        // Sync moods — kirim array kosong jika tidak ada yang dicentang
        $menu->moods()->sync($request->input('moods', []));

        return response()->json([
            'success' => true,
            'message' => 'Data menu berhasil diperbarui!',
            'data'    => $menu->fresh()->load('category', 'moods'),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────
    // 6. DESTROY — Hapus menu (dengan proteksi riwayat transaksi)
    // ──────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Proteksi: jangan hapus jika sudah ada di riwayat order
        if ($menu->items()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Menu tidak boleh dihapus karena memiliki riwayat transaksi! Nonaktifkan status "Tersedia" saja.',
            ], 400);
        }

        // Hapus file gambar dari storage
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        // Putus relasi pivot dulu, lalu hapus
        $menu->moods()->detach();
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus permanen!',
            'value'   => 200, // Menjaga return response format sebelumnya
        ], 200);
    }

    // ──────────────────────────────────────────────────────────
    // 7. TOGGLE STATUS — Aktif / Nonaktif cepat dari tabel
    // ──────────────────────────────────────────────────────────
    public function toggleStatus(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $menu->update([
            'is_available' => $request->input('is_available') == '1' ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status ketersediaan menu berhasil diperbarui!',
        ], 200);
    }
}