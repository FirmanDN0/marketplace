@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-box text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan Saya</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Lacak dan kelola pembelian Anda</p>
            </div>
        </div>
        <a href="{{ route('services.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center gap-2 self-start sm:self-auto shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
            <i class="fas fa-plus"></i> Pesan Layanan Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100/80">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="status" class="rounded-xl border border-gray-200/80 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 font-medium text-gray-600">
                    <option value="">Semua Status</option>
                    @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md shadow-blue-500/10">Filter</button>
                @if(request('status'))
                    <a href="{{ route('customer.orders.index') }}" class="text-sm text-gray-400 hover:text-red-500 font-semibold transition-colors"><i class="fas fa-times mr-1"></i>Hapus</a>
                @endif
            </form>
        </div>

        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-50">
            @forelse($orders as $order)
            <a href="{{ route('customer.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-blue-50/30 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-blue-600">{{ $order->order_number }}</span>
                    @php $sc = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-cyan-100 text-cyan-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                    <span class="{{ $sc }} text-xs font-bold px-2.5 py-1 rounded-full">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                </div>
                <div class="text-sm text-gray-700 font-medium truncate mb-1">{{ Str::limit(optional($order->service)->title,50) }}</div>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span class="font-medium">{{ $order->created_at->format('d M Y') }}</span>
                    <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
            </a>
            @empty
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
                <h3 class="font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
                <p class="text-gray-400 text-sm font-medium">Mulai jelajahi layanan profesional.</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-left font-semibold">No. Pesanan</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Layanan</th>
                        <th class="px-6 py-3.5 text-left font-semibold hidden md:table-cell">Penyedia</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Status</th>
                        <th class="px-6 py-3.5 text-left font-semibold">Harga</th>
                        <th class="px-6 py-3.5 text-left font-semibold hidden md:table-cell">Tanggal</th>
                        <th class="px-6 py-3.5 text-left font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 font-bold">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-medium max-w-[220px] truncate">{{ Str::limit(optional($order->service)->title,40) }}</td>
                    <td class="px-6 py-4 text-gray-600 hidden md:table-cell">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 shadow-sm">{{ strtoupper(substr(optional($order->provider)->name ?? 'U', 0, 1)) }}</div>
                            <span class="font-medium">{{ optional($order->provider)->name }}</span>
                            <x-verified-badge :user="$order->provider" />
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $sc = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-cyan-100 text-cyan-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="{{ $sc }} text-xs font-bold px-2.5 py-1 rounded-full">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-400 font-medium hidden md:table-cell">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition"><i class="fas fa-eye text-xs"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-16 text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
                    <h3 class="font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
                    <p class="text-gray-400 text-sm font-medium">Mulai jelajahi layanan profesional.</p>
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100/80">{{ $orders->links() }}</div>
        @endif
    </div>

</div>
@endsection
