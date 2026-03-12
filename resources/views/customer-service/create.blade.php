@extends('layouts.app')
@section('title', 'Percakapan Baru - CS')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('customer-service.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <h1 class="text-2xl font-bold text-gray-900">Mulai Percakapan Baru</h1>
        </div>
        <p class="text-sm text-gray-500 ml-10">AI kami akan menjawab segera. Anda bisa meminta CS manusia kapan saja.</p>
    </div>

    {{-- AI Info Banner --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0"><i class="fas fa-robot text-lg"></i></div>
        <div>
            <p class="text-sm font-semibold text-indigo-800">Dijawab oleh AI Gemini</p>
            <p class="text-xs text-indigo-600">Pertanyaan Anda akan dijawab langsung oleh AI. Jika diperlukan, AI atau Anda bisa meminta bantuan CS manusia.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Detail Pertanyaan</h3></div>
        <div class="p-5">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('customer-service.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Topik / Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        placeholder="cth: Cara top-up wallet, Status pesanan saya, dll..."
                        required maxlength="255"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan / Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="5"
                        placeholder="Tuliskan pertanyaan atau masalah Anda di sini..."
                        required maxlength="3000"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('message') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Maksimal 3000 karakter</p>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                        <i class="fas fa-rocket"></i> Kirim & Mulai Chat
                    </button>
                    <a href="{{ route('customer-service.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick Topics --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Topik Umum</h3></div>
        <div class="p-5 flex flex-wrap gap-2">
            @foreach(['Cara top-up wallet', 'Status pesanan', 'Cara withdraw saldo', 'Cara mendaftar jadi provider', 'Masalah pembayaran', 'Cara membuat dispute', 'Cara menambah layanan'] as $topic)
            <button type="button" onclick="document.querySelector('[name=subject]').value='{{ $topic }}'"
                    class="px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition">
                {{ $topic }}
            </button>
            @endforeach
        </div>
    </div>
</div>
@endsection
