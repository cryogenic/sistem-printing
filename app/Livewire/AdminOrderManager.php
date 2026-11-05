<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class AdminOrderManager extends Component
{
    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->production_status = $newStatus;
            $order->save();

            session()->flash('success', 'Status pesanan ' . $order->order_number . ' dikemas kini!');
        }
    }

    public function downloadArtwork($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->artwork_path) {
            return Storage::disk('public')->download($order->artwork_path);
        }
    }

    public function render()
    {
        // Ambil SEMUA pesanan daripada SEMUA pelanggan
        $orders = Order::with('user')->latest()->get();

        return view('livewire.admin-order-manager', [
            'orders' => $orders
        ]);
    }
}