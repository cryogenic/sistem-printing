<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Pastikan model Order wujud
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    // Paparkan halaman antaramuka jadual
    public function index()
    {
        return view('orders.index');
    }

    // Bekalkan data JSON untuk DataTables
    public function getOrders(Request $request)
    {
        if ($request->ajax()) {
            // Dalam sistem sebenar, tarik data dari jadual orders.
            // Buat masa ini, kita gunakan koleksi contoh untuk ujian:
            $data = collect([
                ['id' => 1, 'order_no' => 'ORD-001', 'customer' => 'Amzar', 'total' => 'RM 65.00', 'status' => 'Sedang Dicetak'],
                ['id' => 2, 'order_no' => 'ORD-002', 'customer' => 'Syarikat ABC', 'total' => 'RM 120.00', 'status' => 'Menunggu Fail'],
                ['id' => 3, 'order_no' => 'ORD-003', 'customer' => 'Ejen VIP', 'total' => 'RM 45.00', 'status' => 'Sedia Diambil'],
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="#" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">Lihat</a> ';
                    $btn .= '<a href="#" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Kemaskini</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    // Fungsi papar papan Kanban
    public function kanban()
    {
        // Simulasi data pesanan yang dikelompokkan mengikut status
        // Dalam sistem sebenar, gunakan: $orders = Order::all()->groupBy('production_status');
        $orders = collect([
            (object)['id' => 1, 'order_no' => 'ORD-001', 'customer' => 'Amzar', 'production_status' => 'pending_artwork'],
            (object)['id' => 2, 'order_no' => 'ORD-002', 'customer' => 'Syarikat ABC', 'production_status' => 'in_printing'],
            (object)['id' => 3, 'order_no' => 'ORD-003', 'customer' => 'Ejen VIP', 'production_status' => 'finishing'],
            (object)['id' => 4, 'order_no' => 'ORD-004', 'customer' => 'Sekolah Seri', 'production_status' => 'ready_pickup'],
        ])->groupBy('production_status');

        return view('orders.kanban', compact('orders'));
    }

    // Fungsi kemas kini status melalui AJAX bila kad dilepaskan (drop)
    public function updateKanbanStatus(Request $request)
    {
        // Dalam sistem sebenar, kemas kini pangkalan data:
        // $order = Order::find($request->order_id);
        // $order->production_status = $request->new_status;
        // $order->save();

        return response()->json([
            'success' => true, 
            'message' => 'Status pesanan ' . $request->order_id . ' berjaya ditukar ke ' . $request->new_status
        ]);
    }
}