@extends('layouts.app')
@section('title', 'Service Detail')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Service Detail</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">{{ $service->title }}</h3>
                    @php $sc = match($service->status) { 'active' => 'bg-green-100 text-green-700', 'rejected','deleted' => 'bg-red-100 text-red-700', 'paused' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ $service->status }}</span>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Stats --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-center bg-gray-50 rounded-xl py-3">
                            <div class="text-lg font-bold text-gray-900">{{ number_format($service->avg_rating, 1) }} <i class="fas fa-star text-yellow-400 text-sm"></i></div>
                            <div class="text-xs text-gray-500">Rating</div>
                        </div>
                        <div class="text-center bg-gray-50 rounded-xl py-3">
                            <div class="text-lg font-bold text-gray-900">{{ $service->total_reviews }}</div>
                            <div class="text-xs text-gray-500">Reviews</div>
                        </div>
                        <div class="text-center bg-gray-50 rounded-xl py-3">
                            <div class="text-lg font-bold text-gray-900">{{ $service->total_orders }}</div>
                            <div class="text-xs text-gray-500">Orders</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <div><p class="text-xs text-gray-400 mb-1">Provider</p><p class="text-sm font-medium text-gray-900">{{ optional($service->provider)->name }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Category</p><p class="text-sm font-medium text-gray-900">{{ optional($service->category)->name }}</p></div>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($service->description)) !!}</p>
                    </div>

                    {{-- Packages --}}
                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Packages</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-4 py-2 font-semibold text-gray-600 text-xs">Type</th>
                                        <th class="text-left px-4 py-2 font-semibold text-gray-600 text-xs">Name</th>
                                        <th class="text-left px-4 py-2 font-semibold text-gray-600 text-xs">Price</th>
                                        <th class="text-left px-4 py-2 font-semibold text-gray-600 text-xs">Days</th>
                                        <th class="text-left px-4 py-2 font-semibold text-gray-600 text-xs">Revisions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                @foreach($service->packages as $pkg)
                                <tr>
                                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ ucfirst($pkg->package_type) }}</span></td>
                                    <td class="px-4 py-2 text-gray-700">{{ $pkg->name }}</td>
                                    <td class="px-4 py-2 font-semibold text-gray-900">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $pkg->delivery_days }}d</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $pkg->revisions == -1 ? 'Unlimited' : $pkg->revisions }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Update Status</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.services.status', $service->id) }}" class="space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">New Status</label>
                            <select name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach(['draft','active','paused','rejected','deleted'] as $s)
                                    <option value="{{ $s }}" {{ $service->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Rejection Reason <span class="text-gray-400 font-normal">(if rejected)</span></label>
                            <input type="text" name="rejection_reason" placeholder="Optional"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
