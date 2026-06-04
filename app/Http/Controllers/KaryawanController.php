<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        return view('admin.karyawan');
    }

    public function tambah()
    {
        return view('admin.karyawan-tambah');
    }

    public function edit()
    {
        // Sementara return view langsung, nanti di sini tempat mengambil data DB berdasarkan ID
        return view('admin.karyawan-edit');
    }
}
