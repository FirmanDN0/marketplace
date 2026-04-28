@extends('layouts.app')
@section('title', 'Order Detail')
@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
        @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
        <span class="{{ $sc }} text-xs font-semibold px-3 py-1.5 rounded-full">{{ str_replace('_',' ',$order->status) }}</span>
        @if($order->revision_count > 0)
            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1.5 rounded-full" title="Revision #{{ $order->revision_count }}"><i class="fas fa-sync-alt mr-1"></i>Rev #{{ $order->revision_count }}</span>
        @endif
    </div>

    {{-- Visual Order Timeline --}}
    @if(!in_array($order->status, ['pending_payment', 'cancelled']))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        @php
            $steps = [
                'paid' => ['icon' => 'fa-receipt', 'label' => 'Order Placed'],
                'reqs' => ['icon' => 'fa-clipboard-list', 'label' => 'Requirements'],
                'work' => ['icon' => 'fa-laptop-code', 'label' => 'In Progress'],
                'done' => ['icon' => 'fa-box-open', 'label' => 'Delivered']
            ];
            
            $currentStep = 1;
            if ($order->requirements_submitted_at) $currentStep = 2;
            if ($order->status === 'in_progress') $currentStep = 3;
            if (in_array($order->status, ['delivered', 'completed', 'disputed'])) $currentStep = 4;
        @endphp
        
        <div class="relative max-w-3xl mx-auto">
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-gray-100">
                <div style="width: {{ (($currentStep - 1) / 3) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
            </div>
            <div class="flex justify-between w-full absolute top-0 -mt-3.5">
                @php $i = 1; @endphp
                @foreach($steps as $key => $step)
                    @php 
                        $active = $currentStep >= $i;
                        $color = $active ? 'text-blue-600' : 'text-gray-400';
                        $bg = $active ? 'bg-blue-100' : 'bg-gray-50';
                    @endphp
                    <div class="flex flex-col items-center" style="width: 20px;">
                        <div class="w-9 h-9 {{ $bg }} {{ $color }} rounded-full flex items-center justify-center mb-1.5 {{ $active ? 'ring-4 ring-white' : '' }} z-10 relative">
                            <i class="fas {{ $step['icon'] }} text-sm"></i>
                        </div>
                        <span class="text-[11px] font-semibold {{ $color }} whitespace-nowrap">{{ $step['label'] }}</span>
                    </div>
                    @php $i++; @endphp
                @endforeach
            </div>
        </div>
        <div class="h-6"></div> {{-- spacer for absolute elements --}}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Order Info</h3></div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Customer</div><p class="text-sm text-gray-900 font-medium">{{ optional($order->customer)->name }}</p></div>
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Service</div><p class="text-sm text-gray-900">{{ optional($order->service)->title }}</p></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Package</div><p class="text-sm text-gray-900">{{ optional($order->package)->name }}</p></div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase font-medium mb-1">Delivery Deadline</div>
                            @if($order->delivery_deadline)
                                @php
                                    $now = now();
                                    $isActive = in_array($order->status, ['paid', 'in_progress']);
                                    $isOverdue = $isActive && $now->gt($order->delivery_deadline);
                                    $hoursLeft = $isActive ? $now->diffInHours($order->delivery_deadline, false) : null;
                                @endphp
                                <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : ($hoursLeft !== null && $hoursLeft <= 24 ? 'text-orange-600' : 'text-gray-900') }}">
                                    {{ $order->delivery_deadline->format('M d, Y H:i') }}
                                </p>
                                @if($isActive)
                                    @if($isOverdue)
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full"><i class="fas fa-exclamation-circle"></i> Terlambat {{ $now->diffForHumans($order->delivery_deadline, true) }}</span>
                                    @elseif($hoursLeft <= 24)
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full"><i class="fas fa-clock"></i> Sisa {{ $now->diffForHumans($order->delivery_deadline, true) }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full"><i class="fas fa-clock"></i> Sisa {{ $now->diffForHumans($order->delivery_deadline, true) }}</span>
                                    @endif
                                @endif
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Order Price</div><p class="text-sm font-semibold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Your Earning</div><p class="text-sm font-semibold text-green-600">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</p></div>
                    </div>
                    @if($order->notes)
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Customer Notes</div><p class="text-sm text-gray-700">{{ $order->notes }}</p></div>
                    @endif
                    @if($order->delivery_message)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <strong class="text-green-700 text-sm"><i class="fas fa-check mr-1"></i> Delivery Submitted</strong>
                            <p class="text-sm text-green-700 mt-1">{{ $order->delivery_message }}</p>
                        </div>
                    @endif
                    @if($order->revision_count > 0 && $order->isInProgress())
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                        <strong class="text-orange-700 text-sm"><i class="fas fa-sync-alt mr-1"></i> Revision #{{ $order->revision_count }} Requested
                            <span class="font-normal text-xs text-orange-500">— {{ optional($order->revision_requested_at)->format('M d, Y H:i') }}</span>
                        </strong>
                        <p class="text-sm text-orange-700 mt-1">{{ $order->revision_message }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Customer Requirements --}}
            @if($order->requirements_submitted_at)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900"><i class="fas fa-clipboard-list text-indigo-600 mr-1"></i> Customer's Requirements</h3></div>
                <div class="p-5">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $order->requirements }}</p>
                        @if($order->requirements_file)
                            <div class="mt-4">
                                <a href="{{ Storage::url($order->requirements_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                    <i class="fas fa-download"></i> Download Attached File
                                </a>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-3">Submitted on: {{ $order->requirements_submitted_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            @endif

            {{-- Review --}}
            @if($order->review)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Customer Review</h3></div>
                <div class="p-5">
                    <div class="text-yellow-400 mb-2">{!! str_repeat('<i class="fas fa-star"></i>',$order->review->rating) !!}{!! str_repeat('<i class="far fa-star text-gray-300"></i>',5-$order->review->rating) !!}</div>
                    <p class="text-sm text-gray-700 mb-4">{{ $order->review->comment }}</p>

                    @if($order->review->provider_reply)
                        <div class="bg-gray-50 rounded-xl p-4 mb-3">
                            <div class="text-xs font-semibold text-gray-500 mb-1">Your Reply &middot; {{ optional($order->review->replied_at)->format('M d, Y') }}</div>
                            <p class="text-sm text-gray-700">{{ $order->review->provider_reply }}</p>
                        </div>
                        <form method="POST" action="{{ route('provider.reviews.reply.delete', $order->review->id) }}" onsubmit="return confirm('Remove your reply?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium">Remove reply</button>
                        </form>
                    @else
                        @if(session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('provider.reviews.reply', $order->review->id) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Reply to this review</label>
                                <textarea name="provider_reply" rows="3" required minlength="5" maxlength="1000"
                                          placeholder="Write a professional reply…"
                                          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('provider_reply') }}</textarea>
                                @error('provider_reply')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl font-medium text-sm transition">Post Reply</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column - Actions --}}
        <div class="space-y-6">
            {{-- Contact Customer --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Message Customer</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('messages.start') }}">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                        <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                        <button type="submit" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fas fa-comment-dots"></i> Message {{ optional($order->customer)->name }}
                        </button>
                    </form>
                </div>
            </div>

            @if($order->isWaitingRequirements())
            <div class="bg-yellow-50 rounded-2xl border border-yellow-200 overflow-hidden">
                <div class="p-5 text-center">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-hourglass-half text-xl"></i>
                    </div>
                    <h4 class="font-bold text-yellow-900 mb-1">Waiting for Requirements</h4>
                    <p class="text-sm text-yellow-700">The customer has paid, but hasn't submitted their requirements yet. The delivery timer will start once they do.</p>
                </div>
            </div>
            @endif

            @if($order->isInProgress())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-900 text-sm">
                        @if($order->revision_count > 0) <i class="fas fa-sync-alt text-orange-500 mr-1"></i> Re-submit Delivery (Revision #{{ $order->revision_count }})
                        @else <i class="fas fa-paper-plane text-blue-600 mr-1"></i> Submit Delivery @endif
                    </h4>
                </div>
                <div class="p-5">
                    @if($order->revision_count > 0 && $order->revision_message)
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4 text-sm text-orange-700">
                        <p class="font-medium text-xs mb-1">Customer's Revision Request:</p>
                        <p>{{ $order->revision_message }}</p>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('provider.orders.deliver', $order->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Message <span class="text-gray-400 font-normal">(min 20 chars)</span></label>
                            <textarea name="delivery_message" rows="5" required placeholder="Describe what you've delivered…"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery File <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="file" name="delivery_file" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i> Submit Delivery
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Cancel Order --}}
            @if(in_array($order->status, ['paid', 'in_progress']))
            <div x-data="{ showCancel: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5">
                    <button @click="showCancel = !showCancel" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fas fa-times-circle"></i> Batalkan Order
                    </button>
                    <div x-show="showCancel" x-transition class="mt-4">
                        <p class="text-xs text-red-500 mb-3">Dana akan dikembalikan ke saldo customer. Tindakan ini tidak bisa dibatalkan.</p>
                        <form method="POST" action="{{ route('provider.orders.cancel', $order->id) }}" onsubmit="return confirm('Yakin ingin membatalkan order ini?')">
                            @csrf @method('PATCH')
                            <textarea name="cancel_reason" rows="3" required minlength="10" maxlength="500"
                                      placeholder="Alasan pembatalan (min 10 karakter)..."
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none mb-3"></textarea>
                            @error('cancel_reason')<span class="text-red-500 text-xs block mb-2">{{ $message }}</span>@enderror
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition">
                                Konfirmasi Pembatalan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
