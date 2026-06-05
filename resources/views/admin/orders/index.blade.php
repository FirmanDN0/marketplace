@extends('layouts.app')
@section('title', 'Admin: Semua Pesanan')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-violet-500/20">
                <i class="fas fa-receipt text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Semua Pesanan</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Pantau seluruh transaksi platform</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        {{-- Filter --}}
        <div class="px-6 py-4 border-b border-gray-100/80">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, customer, provider…"
                           class="rounded-xl border border-gray-200/80 pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-56 bg-gray-50 font-medium">
                </div>
                <select name="status" class="rounded-xl border border-gray-200/80 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 font-medium text-gray-600">
                    <option value="">Semua Status</option>
                    @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled','disputed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-gray-200/80 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 font-medium" title="Dari tanggal">
                <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-gray-200/80 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 font-medium" title="Sampai tanggal">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md shadow-blue-500/10">Filter</button>
                @if(request()->hasAny(['search','status','from','to']))
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-red-500 text-sm font-semibold transition-colors"><i class="fas fa-times mr-1"></i>Hapus</a>
                @endif
            </form>
        </div>

        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-50">
            @forelse($orders as $order)
            <a href="{{ route('admin.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-blue-50/30 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-blue-600">{{ $order->order_number }}</span>
                    @php $sc = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
                </div>
                <div class="text-sm text-gray-700 truncate mb-1 font-medium">{{ Str::limit(optional($order->service)->title, 45) }}</div>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span class="font-medium">{{ optional($order->customer)->name }} &rarr; {{ optional($order->provider)->name }}</span>
                    <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
            </a>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
                <h3 class="font-bold text-gray-800">Tidak ada pesanan ditemukan.</h3>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400">
                        <th class="px-6 py-3.5 font-semibold">No. Pesanan</th>
                        <th class="px-6 py-3.5 font-semibold hidden md:table-cell">Pembeli</th>
                        <th class="px-6 py-3.5 font-semibold hidden md:table-cell">Penyedia</th>
                        <th class="px-6 py-3.5 font-semibold hidden lg:table-cell">Layanan</th>
                        <th class="px-6 py-3.5 font-semibold">Harga</th>
                        <th class="px-6 py-3.5 font-semibold">Status</th>
                        <th class="px-6 py-3.5 font-semibold hidden md:table-cell">Tanggal</th>
                        <th class="px-6 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4 font-bold text-blue-600">
                        <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-medium hidden md:table-cell">{{ optional($order->customer)->name }}</td>
                    <td class="px-6 py-4 text-gray-700 font-medium hidden md:table-cell">{{ optional($order->provider)->name }}</td>
                    <td class="px-6 py-4 text-gray-700 font-medium hidden lg:table-cell max-w-[200px] truncate">{{ Str::limit(optional($order->service)->title, 40) }}</td>
                    <td class="px-6 py-4 font-bold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php $sc = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 font-medium hidden md:table-cell">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $order->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition"><i class="fas fa-eye text-xs"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
                    <h3 class="font-bold text-gray-800">Tidak ada pesanan ditemukan.</h3>
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
