<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ToyyibPayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FpxController;
use App\Http\Controllers\TopupReceiptController;
use App\Livewire\AdminOrderManager;
use App\Livewire\PrintingCalculator;
use App\Livewire\OrderList;
use App\Livewire\OrderSummary;
use App\Livewire\WalletTopup;
use App\Livewire\BillingHistory;
use App\Livewire\AdminSettings;
use App\Livewire\AdminCustomers;

// Webhook ToyyibPay: DI LUAR group auth (server ToyyibPay tiada sesi)
Route::post('/toyyibpay/webhook', [FpxController::class, 'webhook'])->name('toyyibpay.webhook');

// Laluan Khas Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/orders', AdminOrderManager::class)->name('admin.orders');
    Route::get('/admin/settings', AdminSettings::class)->name('admin.settings');
    Route::get('/admin/customers', AdminCustomers::class)->name('admin.customers');
});

// Halaman Utama: terus ke dashboard (kalau dah log masuk) atau log masuk
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Dashboard (data sebenar)
Route::get('/dashboard', function () {
    $base = \App\Models\Order::where('user_id', auth()->id());

    $stats = [
        'total'  => (clone $base)->count(),
        'unpaid' => (clone $base)->where('payment_status', 'unpaid')->count(),
        'paid'   => (clone $base)->where('payment_status', 'paid')->count(),
    ];

    $labels = $orders = $revenue = [];
    for ($i = 5; $i >= 0; $i--) {
        $m = now()->startOfMonth()->subMonths($i);
        $monthly = (clone $base)
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month);

        $labels[]  = $m->format('M');
        $orders[]  = (clone $monthly)->count();
        $revenue[] = (float) (clone $monthly)->where('payment_status', 'paid')->sum('total_amount');
    }

    $byStatus = (clone $base)->latest()->get()->groupBy('production_status');

    return view('dashboard', compact('stats', 'labels', 'orders', 'revenue', 'byStatus'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Laluan untuk pengguna yang log masuk
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Sistem printing
    Route::get('/kalkulator-printing', PrintingCalculator::class)->name('printing.calculator');
    Route::get('/senarai-tempahan', OrderList::class)->name('orders.list');
    Route::get('/rumusan-tempahan/{orderNumber}', OrderSummary::class)->name('order.summary');

    // FPX (ToyyibPay): pelanggan dipulangkan ke sini selepas bayar order
    Route::get('/toyyibpay/return', [FpxController::class, 'return'])->name('toyyibpay.return');

    // Top-up wallet
    Route::get('/wallet/topup', WalletTopup::class)->name('wallet.topup');
    Route::get('/wallet/topup/return', [FpxController::class, 'topupReturn'])->name('wallet.topup.return');

    // Sejarah bil (top-up & pembayaran)
    Route::get('/billing/history', BillingHistory::class)->name('billing.history');

    // Callback lama ToyyibPayController (boleh dikekalkan)
    Route::get('/payment/callback', [ToyyibPayController::class, 'paymentCallback'])->name('toyyibpay.callback');

    // Invois
    Route::get('/invoice/download/{order_id}', [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');

    // Resit top-up wallet
    Route::get('/receipt/topup/{topup_id}', [TopupReceiptController::class, 'download'])->name('receipt.topup');

    // DataTables lama
    Route::get('/orders-data', [OrderController::class, 'getOrders'])->name('orders.data');

    // Kanban lama
    Route::get('/kanban', [OrderController::class, 'kanban'])->name('orders.kanban');
    Route::post('/kanban/update', [OrderController::class, 'updateKanbanStatus'])->name('orders.kanban.update');

    // CRUD produk
    Route::resource('products', ProductController::class);
});

require __DIR__.'/auth.php';