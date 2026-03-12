@extends('layouts.app')
@section('title', 'Setup Profil Provider — Langkah 2')
@section('content')
<div class="max-w-2xl mx-auto">

    @include('provider.onboarding._progress', ['current' => 2])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Langkah 2: Keahlian & Pengalaman</h2>
            <p class="text-sm text-gray-500 mt-1">Tunjukkan skill dan pengalamanmu</p>
        </div>
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.save', 2) }}" class="space-y-5">
                @csrf

                {{-- Skills --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keahlian <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">(maks 10)</span></label>
                    @php
                        $savedSkills = old('skills', $user->profile->skills ?? []);
                        $suggestedSkills = ['Desain Grafis','Logo Design','UI/UX Design','Ilustrasi','Video Editing','Animasi','Penulisan Konten','Copywriting','SEO','Social Media','Pemrograman Web','Pemrograman Mobile','Laravel','React','Vue.js','Wordpress','Fotografi','Musik & Audio','Data Entry','Terjemahan'];
                    @endphp
                    <div id="skill-tags" class="flex flex-wrap gap-2 mb-3">
                        @foreach($savedSkills as $sk)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-600 text-white text-xs font-medium" data-skill="{{ $sk }}">
                            {{ $sk }}
                            <button type="button" onclick="removeSkill(this.parentElement)" class="hover:text-blue-200 transition">&times;</button>
                            <input type="hidden" name="skills[]" value="{{ $sk }}">
                        </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mb-3">
                        <input id="skill-input" type="text" placeholder="Ketik keahlian lalu tekan Enter..."
                               class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addSkill(this.value);this.value=''}">
                        <button type="button" onclick="addSkill(document.getElementById('skill-input').value);document.getElementById('skill-input').value=''"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">Tambah</button>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($suggestedSkills as $s)
                        <button type="button" onclick="addSkill('{{ $s }}')"
                                class="text-xs px-3 py-1.5 rounded-full border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition">
                            + {{ $s }}
                        </button>
                        @endforeach
                    </div>
                    @error('skills')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Languages --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bahasa yang Dikuasai <span class="text-red-500">*</span></label>
                    @php
                        $savedLangs = old('languages', $user->profile->languages ?? []);
                        $langOptions = ['Indonesia','Inggris','Mandarin','Arab','Jepang','Korea','Spanyol','Prancis','Jerman','Belanda'];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($langOptions as $lang)
                        @php $checked = in_array($lang, (array)$savedLangs); @endphp
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-xl border cursor-pointer transition
                            {{ $checked ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                            <input type="checkbox" name="languages[]" value="{{ $lang }}" {{ $checked ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                onchange="let l=this.closest('label');if(this.checked){l.classList.add('border-blue-400','bg-blue-50');l.classList.remove('border-gray-200')}else{l.classList.remove('border-blue-400','bg-blue-50');l.classList.add('border-gray-200')}">
                            <span class="text-sm text-gray-700">{{ $lang }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('languages')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Experience --}}
                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1.5">Lama Pengalaman <span class="text-red-500">*</span></label>
                    <select id="experience_years" name="experience_years" required
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih...</option>
                        <option value="0" {{ old('experience_years', $user->profile->experience_years) == 0 ? 'selected' : '' }}>Baru mulai (&lt; 1 tahun)</option>
                        @for($y = 1; $y <= 10; $y++)
                        <option value="{{ $y }}" {{ old('experience_years', $user->profile->experience_years) == $y ? 'selected' : '' }}>
                            {{ $y }} tahun{{ $y >= 10 ? '+' : '' }}
                        </option>
                        @endfor
                    </select>
                    @error('experience_years')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('provider.onboarding.show', 1) }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i> Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                        Lanjut <i class="fas fa-arrow-right"></i> Lokasi & Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addSkill(val) {
    val = val.trim();
    if (!val) return;
    const container = document.getElementById('skill-tags');
    const existing = [...container.querySelectorAll('input[name="skills[]"]')].map(i => i.value);
    if (existing.includes(val) || existing.length >= 10) return;
    const span = document.createElement('span');
    span.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-600 text-white text-xs font-medium';
    span.dataset.skill = val;
    span.innerHTML = `${val} <button type="button" onclick="removeSkill(this.parentElement)" class="hover:text-blue-200 transition">&times;</button><input type="hidden" name="skills[]" value="${val}">`;
    container.appendChild(span);
}
function removeSkill(el) { el.remove(); }
</script>
@endsection
