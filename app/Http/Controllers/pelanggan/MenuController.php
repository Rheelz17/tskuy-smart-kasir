<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function initializeTable($number)
    {
        Cookie::queue('tskuy_table_number', $number, 300);
        return redirect()->route('pelanggan.orders');
    }

    public function index()
    {
        $tableNumber = Cookie::get('tskuy_table_number');
        $moods = Mood::all();
        $categories = Category::with('menus.moods')->get();

        return view('pelanggan.ordersPelanggan', compact('categories', 'moods', 'tableNumber'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'type' => 'required|in:open_bill,pay_now',
            'items' => 'required|array',
        ]);

        $tableNumber = Cookie::get('tskuy_table_number') ?? '4';
        $user = auth()->user();

        $tableRow = DB::table('tables')->where('table_number', $tableNumber)->first();
        $tableId = $tableRow ? $tableRow->id : 1; 

        $orderCode = 'TSK-' . strtoupper(Str::random(4)) . '-' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'order_code'    => $orderCode,
                'customer_name' => $user->name,
                'table_id'      => $tableId,
                'order_type'    => 'self order',
                'source'        => 'qr',
                'status'        => 'PENDING',
                'is_open_bill'  => $request->type === 'open_bill' ? 1 : 0,
                'subtotal'      => 0, 
                'tax'           => 0, 
                'total'         => 0, 
                'created_by'    => $user->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $subtotalSum = 0;

            foreach ($request->items as $item) {
                $menu = DB::table('menus')->where('id', $item['id'])->first();
                if ($menu) {
                    $itemPrice = $menu->price;
                    $itemSubtotal = $itemPrice * $item['qty'];
                    $subtotalSum += $itemSubtotal;

                    DB::table('orders_item')->insert([
                        'order_id'   => $orderId,
                        'menu_id'    => $menu->id,
                        'quantity'   => $item['qty'],
                        'price'      => $itemPrice,
                        'note'       => $item['catatan'] ?? null,
                        'status'     => 'PENDING',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $taxAmount = $subtotalSum * 0.10;
            $totalAmount = $subtotalSum + $taxAmount;

            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotalSum,
                'tax'      => $taxAmount,
                'total'    => $totalAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan sukses terkirim ke database!',
                'redirect_url' => route('pelanggan.riwayat')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal input: ' . $e->getMessage()
            ], 500);
        }
    }
}