@php
    $steps = [
        1 => ['label' => 'Profil Dasar',   'icon' => 'fa-user'],
        2 => ['label' => 'Keahlian',        'icon' => 'fa-bolt'],
        3 => ['label' => 'Lokasi & Tarif',  'icon' => 'fa-map-marker-alt'],
        4 => ['label' => 'Selesai',         'icon' => 'fa-check-circle'],
    ];
@endphp

<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">Langkah Registrasi Provider</h2>
    <div class="flex items-start justify-between max-w-2xl mx-auto">
        @foreach($steps as $num => $step)
        @php
            $isDone   = $num < $current;
            $isActive = $num === $current;
        @endphp
        <div class="flex flex-col items-center flex-1 relative">
            {{-- Connector line --}}
            @if($num > 1)
            <div class="absolute top-5 -left-1/2 w-full h-0.5 {{ $isDone ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
            @endif

            {{-- Circle --}}
            <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                {{ $isDone ? 'bg-blue-600 text-white' : ($isActive ? 'bg-blue-100 text-blue-600 ring-2 ring-blue-400' : 'bg-gray-100 text-gray-400') }}">
                @if($isDone)
                    <i class="fas fa-check text-sm"></i>
                @else
                    {{ $num }}
                @endif
            </div>

            {{-- Label --}}
            <div class="mt-2 text-center">
                <div class="text-xs font-semibold {{ $isActive ? 'text-blue-600' : ($isDone ? 'text-gray-700' : 'text-gray-400') }}">{{ $step['label'] }}</div>
                @if($isDone)
                    <div class="text-[10px] text-green-500 font-medium">Selesai</div>
                @elseif($isActive)
                    <div class="text-[10px] text-blue-500 font-medium">Saat ini</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
