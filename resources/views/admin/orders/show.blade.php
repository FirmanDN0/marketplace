@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-violet-500/20">
                <i class="fas fa-receipt text-sm"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan {{ $order->order_number }}</h1>
            @php $sc = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80"><h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500 text-sm"></i>Info Pesanan</h3></div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Pembeli</p><p class="text-sm font-bold text-gray-900">{{ optional($order->customer)->name }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Penyedia</p><p class="text-sm font-bold text-gray-900">{{ optional($order->provider)->name }}</p></div>
                    </div>
                    <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Layanan</p><p class="text-sm font-bold text-gray-900">{{ optional($order->service)->title }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Paket</p><p class="text-sm text-gray-700 font-medium">{{ optional($order->package)->name }} <span class="text-gray-400">({{ optional($order->package)->package_type }})</span></p></div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Harga Pesanan</p><p class="text-sm font-extrabold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Biaya Platform</p><p class="text-sm font-bold text-orange-600">Rp {{ number_format($order->platform_fee, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Pendapatan Penyedia</p><p class="text-sm font-bold text-emerald-600">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</p></div>
                    </div>
                    @if($order->notes)
                        <div class="pt-4 border-t border-gray-100"><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Catatan Pembeli</p><p class="text-sm text-gray-700 font-medium">{{ $order->notes }}</p></div>
                    @endif
                    @if($order->delivery_message)
                        <div class="pt-4 border-t border-gray-100"><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Pesan Pengiriman</p><p class="text-sm text-gray-700 font-medium">{{ $order->delivery_message }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Payment --}}
            @if($order->payment)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80"><h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-credit-card text-emerald-500 text-sm"></i>Pembayaran</h3></div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Jumlah</p><p class="text-sm font-bold text-gray-900">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Metode</p><p class="text-sm text-gray-700 font-medium">{{ $order->payment->payment_method }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Status</p>
                            @php $psc = match($order->payment->status) { 'paid','settlement','capture' => 'bg-emerald-100 text-emerald-700', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $psc }}">{{ $order->payment->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Review --}}
            @if($order->review)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80"><h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-star text-amber-400 text-sm"></i>Ulasan</h3></div>
                <div class="p-6">
                    <div class="flex items-center gap-1 text-yellow-400 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $order->review->rating ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700 font-medium">{{ $order->review->comment }}</p>
                </div>
            </div>
            @endif

            {{-- Dispute --}}
            @if($order->dispute)
            <div class="bg-white rounded-3xl shadow-sm border border-red-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50/50"><h3 class="font-extrabold text-red-700 flex items-center gap-2"><i class="fas fa-exclamation-triangle text-sm"></i>Sengketa</h3></div>
                <div class="p-6 space-y-3">
                    <p class="text-sm"><span class="font-bold text-gray-700">Alasan:</span> {{ $dispute->reason }}</p>
                    <p class="text-sm text-gray-600 font-medium">{{ $order->dispute->description }}</p>
                    @php $dsc = match($order->dispute->status) { 'resolved','closed' => 'bg-emerald-100 text-emerald-700', 'under_review' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold {{ $dsc }}">{{ str_replace('_',' ',$order->dispute->status) }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div>
            @if(!in_array($order->status, ['completed','cancelled']))
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80"><h4 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-shield-alt text-violet-500 text-sm"></i>Aksi Admin</h4></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}"
                          onsubmit="return confirm('Batalkan pesanan ini secara paksa?')">
                        @csrf @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Pembatalan</label>
                            <input type="text" name="reason" placeholder="Alasan (wajib diisi)" required
                                   class="w-full rounded-xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent bg-gray-50 font-medium">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-4 py-3 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
                            <i class="fas fa-exclamation-triangle"></i> Batalkan Paksa
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
