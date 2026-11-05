<x-app-layout>
    @php $card = 'background-color:#ffffff;padding:24px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);'; @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="display:flex;gap:24px;align-items:flex-start;">

                <x-sidebar />

                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:20px;">

                    @if (session('status'))
                        <div style="padding:14px 16px;background-color:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;font-size:14px;font-weight:600;">
                            ✅ {{ session('status') }}
                        </div>
                    @endif

                    <div style="{{ $card }}"><livewire:profile-photo /></div>

                    <div style="{{ $card }}"><livewire:profile-addresses /></div>

                    <div style="{{ $card }}">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div style="{{ $card }}">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div style="{{ $card }}">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>