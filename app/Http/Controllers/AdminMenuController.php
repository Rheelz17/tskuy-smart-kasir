<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    // 1. TAMPILKAN HALAMAN UTAMA (Desktop & Mobile Index)
    public function index()
    {
        // Eager loading relasi category dan moods agar query database enteng
        $menus = Menu::with(['category', 'moods'])->latest()->get();
        $categories = Category::all();
        $moods = Mood::all(); // Untuk pilihan checkbox mood di form tambah/edit

        return view('admin.menu', compact('menus', 'categories', 'moods'));
    }

        public function tambah()
    {
        return view('admin.menu-tambah');
    }

    // 2. SIMPAN MENU BARU (Mendukung AJAX Fetch dari Desktop / Form Mobile)
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle upload gambar jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        // Jalankan operasi Insert ke tabel menus
        // Note: Checkbox HTML jika tidak dicentang tidak mengirimkan data, maka kita pakai $request->has()
        $menu = Menu::create([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'description'    => $request->description,
            'image'          => $imagePath,
            'is_available'      => $request->has('is_available'),
            'is_recommended' => $request->has('is_recommended'),
            'is_new'         => $request->has('is_new'),
            'is_promo'       => $request->has('is_promo'),
        ]);

        // Sinkronisasikan pilihan mood (array dari checkbox) ke tabel pivot menu_moods
        if ($request->has('moods')) {
            $menu->moods()->sync($request->moods);
        }

        return response()->json([
            'success'  => true,
            'message' => 'Menu baru berhasil ditambahkan!',
            'data' => $menu
        ], 200);
    }

    // 3. HALAMAN EDIT KHUSUS MOBILE (Mengembalikan View Full Page)
    public function edit($id)
    {
        // Ambil menu berserta id mood yang terhubung dengannya
        $menu = Menu::with('moods')->findOrFail($id);
        $categories = Category::all();
        $moods = Mood::all();

        return view('admin.menu-edit', compact('menu', 'categories', 'moods'));
    }

    // 4. PROSES UPDATE DATA MENU (Mendukung Form Data via Fetch API)
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $menu->image; // Pertahankan gambar lama secara default
        
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari folder storage jika ada sebelum ditimpa
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        // Lakukan update data
        $menu->update([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'description'    => $request->description,
            'image'          => $imagePath,
            'is_recommended' => $request->has('is_recommended'),
            'is_new'         => $request->has('is_new'),
            'is_promo'       => $request->has('is_promo'),
        ]);

        // Sinkronkan ulang data tabel pivot menu_moods
        // Jika tidak ada mood yang dicentang, kirim array kosong [] untuk menghapus relasi lama
        $menu->moods()->sync($request->input('moods', []));

        return response()->json([
            'success' => true, 
            'message' => 'Data menu berhasil diperbarui!'
        ], 200);
    }

    // 5. PROSES HAPUS MENU (Dengan Fitur Proteksi Riwayat Transaksi Kasir)
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // PROTEKSI: Cek apakah menu ini sudah pernah dipesan di tabel orders_item
        if ($menu->items()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Menu tidak boleh dihapus karena memiliki riwayat transaksi di kasir! Silakan nonaktifkan saja status "Aktif"-nya.'
            ], 400);
        }

        // Jika aman belum pernah dipesan, hapus gambar lalu hapus dari database
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        // Relasi pivot menu_moods otomatis terputus jika di migration diset onDelete('cascade')
        // Namun demi keamanan kita detach manual di sini
        $menu->moods()->detach();
        $menu->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Menu berhasil dihapus permanen!'
        ], 200);
    }

    public function toggleStatus(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $menu->update([
            'is_available' => $request->input('is_available') == '1' ? 1 : 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status ketersediaan menu berhasil diperbarui!'
        ], 200);
    }
}
