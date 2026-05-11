@php
    $statusConfig = match($conv->status) {
        'ai'     => ['color' => 'bg-indigo-100 text-indigo-700', 'icon' => 'fa-robot', 'label' => 'AI', 'dot' => 'bg-indigo-500'],
        'human'  => ['color' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-headset', 'label' => 'Menunggu Agen', 'dot' => 'bg-amber-500'],
        'closed' => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-check-circle', 'label' => 'Selesai', 'dot' => 'bg-gray-400'],
        default  => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-circle', 'label' => $conv->status, 'dot' => 'bg-gray-400'],
    };
    $avatarBg = match($conv->status) {
        'ai'     => 'bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600',
        'human'  => 'bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600',
        'closed' => 'bg-gray-100 text-gray-400',
        default  => 'bg-gray-100 text-gray-400',
    };
@endphp
<a href="{{ route('customer-service.show', $conv->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-blue-50/40 transition group">
    <div class="relative">
        <div class="w-11 h-11 rounded-xl {{ $avatarBg }} flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $statusConfig['icon'] }}"></i>
        </div>
        @if($conv->status !== 'closed')
        <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 {{ $statusConfig['dot'] }} rounded-full border-2 border-white"></div>
        @endif
    </div>
    <div class="flex-1 min-w-0">
        <div class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm">{{ $conv->subject ?? 'Tanpa Judul' }}</div>
        <div class="text-xs text-gray-500 truncate mt-0.5">
            {{ optional($conv->lastMessage)->message ? Str::limit($conv->lastMessage->message, 80) : 'Belum ada pesan' }}
        </div>
    </div>
    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
        <span class="{{ $statusConfig['color'] }} text-[11px] font-semibold px-2.5 py-1 rounded-full"><i class="fas {{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['label'] }}</span>
        <div class="text-[11px] text-gray-400">{{ $conv->updated_at->diffForHumans() }}</div>
    </div>
</a>
