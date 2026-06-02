<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller; // Manggil file induk di atas
use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use Illuminate\Support\Facades\Cookie;

class MenuController extends Controller
{
    // Fungsi buat nyimpen nomor meja pas user scan QR (Contoh: /table/4)
    public function initializeTable($number)
    {
        Cookie::queue('tskuy_table_number', $number, 300); // Simpan di Cookie 5 jam
        return redirect()->route('pelanggan.orders');
    }

    // Fungsi buat nampilin halaman menu
    public function index()
    {
        $tableNumber = Cookie::get('tskuy_table_number');
        $moods = Mood::all();
        $categories = Category::with('menus.moods')->get();

        return view('pelanggan.ordersPelanggan', compact('categories', 'moods', 'tableNumber'));
    }
}