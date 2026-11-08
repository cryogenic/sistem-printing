@php
    $balance = auth()->user()->wallet->balance ?? 0;
    $shopName = \App\Models\Setting::get('shop_name', 'PrintFlow');
    $tab = request('tab', 'topup');

    $item = function (bool $active) {
        return 'display:flex;align-items:center;padding:10px 12px;border-radius:8px;text-decoration:none;font-size:14px;'
            . ($active
                ? 'background-color:#eef2ff;color:#4338ca;font-weight:600;'
                : 'color:#4b5563;font-weight:500;');
    };
    $adminItem = function (bool $active) {
        return 'display:flex;align-items:center;padding:10px 12px;border-radius:8px;text-decoration:none;font-size:14px;color:#dc2626;font-weight:600;'
            . ($active ? 'background-color:#fef2f2;' : '');
    };
    $sectionLabel = 'font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;';
@endphp

<div style="width:260px;flex-shrink:0;background-color:#ffffff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);">

    <!-- Logo -->
    <div style="display:flex;align-items:center;gap:12px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;margin-bottom:16px;">
        <div style="background-color:#4f46e5;color:#ffffff;border-radius:8px;padding:8px 10px;font-weight:bold;font-size:14px;">
            {{ strtoupper(mb_substr($shopName, 0, 2)) }}
        </div>
        <div style="min-width:0;">
            <h3 style="font-weight:bold;color:#1f2937;font-size:14px;margin:0;">{{ $shopName }}</h3>
            <p style="font-size:11px;color:#9ca3af;margin:0;">{{ \App\Models\Setting::get('shop_tagline') }}</p>
        </div>
    </div>

    <!-- Profil pengguna -->
    <a href="{{ route('profile.edit') }}"
       style="display:flex;align-items:center;gap:10px;padding:10px;margin-bottom:16px;background-color:#f9fafb;border:1px solid #f3f4f6;border-radius:10px;text-decoration:none;">
        <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background-color:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
            @if (auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            @endif
        </div>
        <div style="min-width:0;">
            <p style="margin:0;font-size:13px;font-weight:700;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
            <p style="margin:0;font-size:11px;color:#6366f1;">Edit profil</p>
        </div>
    </a>

    <!-- Butang + New Order -->
    <a href="{{ route('printing.calculator') }}"
       style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;box-sizing:border-box;padding:11px 12px;margin-bottom:20px;background-color:#1d4ed8;color:#ffffff;font-weight:700;font-size:14px;border-radius:8px;text-decoration:none;">
        <span style="font-size:18px;line-height:1;">+</span> New Order
    </a>

    <nav style="display:flex;flex-direction:column;gap:4px;">
        <div style="{{ $sectionLabel }}color:#9ca3af;">ORDERING</div>

        <a href="{{ route('dashboard') }}" style="{{ $item(request()->routeIs('dashboard')) }}">
            <svg style="width:18px;height:18px;margin-right:10px;opacity:0.8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <a href="{{ route('orders.list') }}" style="{{ $item(request()->routeIs('orders.list')) }}">
            <svg style="width:18px;height:18px;margin-right:10px;opacity:0.8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            My Orders
        </a>

        @if (auth()->user()->role === 'admin')
            <div style="{{ $sectionLabel }}color:#dc2626;margin-top:20px;">ADMIN MANAGEMENT</div>

            <a href="{{ route('admin.orders') }}" style="{{ $adminItem(request()->routeIs('admin.orders')) }}">
                <svg style="width:18px;height:18px;margin-right:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Admin Orders Panel
            </a>

            <a href="{{ route('admin.customers') }}" style="{{ $adminItem(request()->routeIs('admin.customers')) }}">
                <svg style="width:18px;height:18px;margin-right:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pelanggan
            </a>

            <a href="{{ route('admin.settings') }}" style="{{ $adminItem(request()->routeIs('admin.settings')) }}">
                <svg style="width:18px;height:18px;margin-right:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Tetapan
            </a>
        @endif

        <div style="{{ $sectionLabel }}color:#9ca3af;margin-top:20px;">BILLING</div>

        <!-- Wallet: baki + butang Top Up -->
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <a href="{{ route('wallet.topup') }}" style="{{ $item(request()->routeIs('wallet.topup')) }}flex:1;min-width:0;">
                <svg style="width:18px;height:18px;margin-right:10px;opacity:0.6;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                RM {{ number_format($balance, 2) }}
            </a>
            <a href="{{ route('wallet.topup') }}"
               style="background-color:#059669;color:#ffffff;font-size:12px;font-weight:700;padding:6px 10px;border-radius:6px;text-decoration:none;white-space:nowrap;">
                + Top Up
            </a>
        </div>

        <a href="{{ route('billing.history', ['tab' => 'topup']) }}"
           style="{{ $item(request()->routeIs('billing.history') && $tab === 'topup') }}">
            <svg style="width:18px;height:18px;margin-right:10px;opacity:0.6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Sejarah Top-Up
        </a>

        <a href="{{ route('billing.history', ['tab' => 'payments']) }}"
           style="{{ $item(request()->routeIs('billing.history') && $tab === 'payments') }}">
            <svg style="width:18px;height:18px;margin-right:10px;opacity:0.6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9.5 8.5h.01M14.5 13.5h.01M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16l-3-2-2 2-2-2-2 2-2-2-3 2z"/></svg>
            Sejarah Pembayaran
        </a>
    </nav>
</div>