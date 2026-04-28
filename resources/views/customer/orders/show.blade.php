@extends('layouts.app')
@section('title', 'Order Details')
@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
        @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
        <span class="{{ $sc }} text-xs font-semibold px-3 py-1.5 rounded-full">{{ str_replace('_',' ',$order->status) }}</span>
        @if($order->revision_count > 0)
            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1.5 rounded-full"><i class="fas fa-sync-alt mr-1"></i>Revision #{{ $order->revision_count }}</span>
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

    @if($order->isPendingPayment())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between flex-wrap gap-3">
        <span class="text-yellow-700 text-sm font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> This order awaits payment to begin.</span>
        <a href="{{ route('payment.show', $order->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">Pay Now</a>
    </div>
    @endif

    @if($order->isWaitingRequirements())
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="bg-indigo-100 text-indigo-600 rounded-xl w-12 h-12 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clipboard-list text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-indigo-900 mb-1">Action Required: Submit Your Requirements</h3>
                <p class="text-indigo-700 text-sm mb-4">The provider needs your instructions and files before they can start working. The delivery timer will start once you submit these.</p>
                
                <form action="{{ route('customer.orders.requirements', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-indigo-900 mb-1.5">Project Brief / Instructions <span class="text-red-500">*</span></label>
                        <textarea name="requirements" rows="4" required minlength="10" placeholder="Describe exactly what you need. Provide colors, references, or links..." class="w-full rounded-xl border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-sm resize-none"></textarea>
                        @error('requirements') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-indigo-900 mb-1.5">Attach Files <span class="font-normal text-indigo-600">(Optional, max 20MB)</span></label>
                        <input type="file" name="requirements_file" class="w-full text-sm text-indigo-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition">
                        @error('requirements_file') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition inline-flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Submit & Start Order
                    </button>
                </form>
            </div>
        </div>
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
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Service</div><p class="text-sm text-gray-900 font-medium">{{ optional($order->service)->title }}</p></div>
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Provider</div><p class="text-sm text-gray-900">{{ optional($order->provider)->name }}</p></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Package</div><p class="text-sm text-gray-900">{{ optional($order->package)->name }} <span class="text-gray-400">({{ optional($order->package)->package_type }})</span></p></div>
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
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Price Paid</div><p class="text-sm font-semibold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                        <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Placed On</div><p class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</p></div>
                    </div>
                    @if($order->notes)
                    <div><div class="text-xs text-gray-400 uppercase font-medium mb-1">Your Notes</div><p class="text-sm text-gray-700">{{ $order->notes }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Requirements Submitted --}}
            @if($order->requirements_submitted_at)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900"><i class="fas fa-clipboard-check text-green-600 mr-1"></i> Your Requirements</h3></div>
                <div class="p-5">
                    <p class="text-sm text-gray-700 mb-3 whitespace-pre-wrap">{{ $order->requirements }}</p>
                    @if($order->requirements_file)
                        <a href="{{ Storage::url($order->requirements_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                            <i class="fas fa-paperclip"></i> View Attached File
                        </a>
                    @endif
                    <p class="text-xs text-gray-400 mt-3">Submitted on: {{ $order->requirements_submitted_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            @endif

            {{-- Delivery --}}
            @if($order->delivery_message)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900"><i class="fas fa-box text-blue-600 mr-1"></i> Delivery from Provider</h3></div>
                <div class="p-5">
                    <p class="text-sm text-gray-700 mb-3">{{ $order->delivery_message }}</p>
                    @if($order->delivery_file)
                        <a href="{{ Storage::url($order->delivery_file) }}" download class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    @endif
                    <p class="text-xs text-gray-400 mt-3">Delivered: {{ optional($order->delivered_at)->format('M d, Y H:i') }}</p>
                </div>
            </div>
            @endif

            {{-- Revision Request --}}
            @if($order->isInProgress() && $order->revision_count > 0 && $order->revision_message)
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">
                <h3 class="font-semibold text-orange-800 text-sm mb-2"><i class="fas fa-sync-alt mr-1"></i> Your Revision Request #{{ $order->revision_count }}</h3>
                <p class="text-xs text-orange-600 mb-2">Submitted: {{ optional($order->revision_requested_at)->format('M d, Y H:i') }}</p>
                <p class="text-sm text-orange-700">{{ $order->revision_message }}</p>
                <p class="text-xs text-orange-500 mt-2">The provider is working on the revision.</p>
            </div>
            @endif

            {{-- Review --}}
            @if($order->review)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Your Review</h3></div>
                <div class="p-5">
                    <div class="text-yellow-400 mb-2">{!! str_repeat('<i class="fas fa-star"></i>',$order->review->rating) !!}{!! str_repeat('<i class="far fa-star text-gray-300"></i>',5-$order->review->rating) !!}</div>
                    <p class="text-sm text-gray-700">{{ $order->review->comment }}</p>
                    @if($order->review->provider_reply)
                        <div class="mt-4 bg-gray-50 rounded-xl p-4">
                            <span class="text-xs font-semibold text-gray-500">Provider's reply</span>
                            <p class="text-sm text-gray-700 mt-1">{{ $order->review->provider_reply }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column - Actions --}}
        <div class="space-y-6">
            {{-- Contact Provider --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Message Provider</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('messages.start') }}">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $order->provider_id }}">
                        <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                        <button type="submit" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fas fa-comment-dots"></i> Message {{ optional($order->provider)->name }}
                        </button>
                    </form>
                </div>
            </div>

            @if($order->isDelivered())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Review Delivery</h4></div>
                <div class="p-5 space-y-4">
                    <form method="POST" action="{{ route('customer.orders.accept', $order->id) }}"
                          onsubmit="return confirm('Accept delivery and complete the order?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Accept & Complete
                        </button>
                    </form>

                    @if($order->canRequestRevision())
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2"><i class="fas fa-sync-alt text-orange-500 mr-1"></i> Request Revision
                            <span class="text-xs text-gray-400">
                                ({{ $order->revisionsRemaining() }} remaining)
                            </span>
                        </p>
                        <form method="POST" action="{{ route('customer.orders.revision', $order->id) }}"
                              onsubmit="return confirm('Request a revision from the provider?')">
                            @csrf @method('PATCH')
                            <textarea name="revision_message" rows="3" required minlength="20" maxlength="1000"
                                      placeholder="Describe what needs to be changed (min 20 chars)…"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none mb-2">{{ old('revision_message') }}</textarea>
                            @error('revision_message')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center justify-center gap-2">
                                <i class="fas fa-sync-alt"></i> Request Revision
                            </button>
                        </form>
                    </div>
                    @endif

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500 mb-2">Not satisfied? Open a dispute:</p>
                        <form method="POST" action="{{ route('customer.orders.dispute', $order->id) }}"
                              onsubmit="return confirm('Open a dispute for this order?')" class="space-y-2">
                            @csrf
                            <input type="text" name="reason" placeholder="Dispute reason" required
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <textarea name="description" rows="3" required placeholder="Describe the issue (min 30 chars)…"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                            <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center justify-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i> Open Dispute
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($order->canBeReviewed())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <a href="{{ route('customer.reviews.create', $order->id) }}" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-star"></i> Leave a Review
                </a>
            </div>
            @endif

            @if(in_array($order->status, ['pending_payment', 'paid']))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Cancel Order</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}"
                          onsubmit="return confirm('Cancel this order?')" class="space-y-3">
                        @csrf @method('PATCH')
                        <input type="text" name="reason" placeholder="Reason for cancellation" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition">Cancel Order</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
