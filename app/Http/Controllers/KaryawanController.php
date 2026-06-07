<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        // Ambil user yang rolenya super_admin(1), kasir(2), dan koki(4)
        $karyawan = User::whereIn('role_id', [1, 2, 4])->get();
        return view('admin.karyawan', compact('karyawan'));
    }

    public function tambah()
    {
        return view('admin.karyawan-tambah');
    }

    // PROSES SIMPAN (MOBILE & DESKTOP POPUP)
    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|in:1,2,4', // Masukan berbentuk ID Role
            'telp'    => 'nullable|string|max:20',
            'email'   => 'required|email|unique:users,email',
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'alamat'  => 'nullable|string',
        ]);

        // 1. Tentukan prefix berdasarkan Jabatan/Role yang dipilih
        $roleId = $request->jabatan; // Misal: 1 = Admin, 2 = Kasir, 4 = Koki
        $prefix = 'KRY'; // Default jika tidak cocok

        if ($roleId == 1) {
            $prefix = 'ADM';
        } elseif ($roleId == 2) {
            $prefix = 'KSR';
        } elseif ($roleId == 4) {
            $prefix = 'KKI';
        }

        // 2. CARI NOMOR TERAKHIR DI DATABASE BERDASARKAN PREFIXNYA
        // Mengambil data urutan paling terakhir (terbesar)
        $lastUser = User::where('employee_id', 'LIKE', $prefix . '-%')
                        ->orderBy('employee_id', 'desc')
                        ->first();

        if ($lastUser) {
            // Jika ada, ambil angka di belakangnya (misal KSR-001 diambil 001 -> jadi angka 1)
            $lastNumber = (int) substr($lastUser->employee_id, strpos($lastUser->employee_id, '-') + 1);
            $nextNumber = $lastNumber + 1; // Ditambah 1 menjadi 2
        } else {
            // Jika belum ada sama sekali di database, mulai dari 1
            $nextNumber = 1;
        }

        // 3. Gabungkan kembali menjadi ID baru (misal: KSR-002)
        $employeeId = $prefix . '-' . sprintf('%03d', $nextNumber);

        // Generasi Otomatis Username dari Email
        $username = explode('@', $request->email)[0] . rand(10, 99);

        $user = new User();
        $user->employee_id = $employeeId;
        $user->name = $request->nama;
        $user->username = $username;
        $user->email = $request->email;
        $user->phone = $request->telp;
        $user->is_active = $request->input('is_active', 0);
        $user->role_id = $request->jabatan;
        $user->password = Hash::make('tskuy123'); // Password bawaan awal awal
        $user->is_active = $request->boolean('is_active') ? 1 : 0;
        $user->alamat = $request->alamat; 

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('karyawan-photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan baru berhasil ditambahkan!'
        ]);
    }

    public function edit($id)
    {
        $karyawan = User::findOrFail($id);
        return view('admin.karyawan-edit', compact('karyawan'));
    }

    // 5. PROSES UPDATE (MOBILE & DESKTOP POPUP)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|in:1,2,4',
            'telp'    => 'nullable|string|max:20',
            'email'   => 'required|email|unique:users,email,' . $id,
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'alamat'  => 'nullable|string',
        ]);

        $user->name = $request->nama;
        $user->role_id = $request->jabatan;
        $user->phone = $request->telp;
        $user->email = $request->email;
        $user->alamat = $request->alamat;
        $user->is_active = $request->input('is_active', 0);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('karyawan-photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus!'
        ]);
    }
}