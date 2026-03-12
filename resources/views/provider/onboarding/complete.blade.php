@extends('layouts.app')
@section('title', 'Selamat Datang, Provider!')
@section('content')
<div class="max-w-2xl mx-auto">

    @include('provider.onboarding._progress', ['current' => 4])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-20 h-20 rounded-full bg-yellow-100 text-yellow-500 flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-trophy text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Profil providermu siap!</h1>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Selamat datang di ServeMix! Sekarang kamu bisa mulai membuat layanan dan menerima pesanan dari pelanggan.</p>

        <div class="bg-gray-50 rounded-xl p-5 mb-8 text-left max-w-sm mx-auto">
            <p class="text-sm font-semibold text-gray-700 mb-3">Langkah selanjutnya:</p>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-edit text-sm"></i></span>
                    <span class="text-sm text-gray-600">Buat listing layanan pertamamu agar pelanggan bisa menemukan kamu</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center shrink-0"><i class="fas fa-briefcase text-sm"></i></span>
                    <span class="text-sm text-gray-600">Lengkapi paket harga (basic, standard, premium) agar lebih menarik</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center shrink-0"><i class="fas fa-star text-sm"></i></span>
                    <span class="text-sm text-gray-600">Kerjakan pesanan dengan baik untuk mendapatkan rating tinggi</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('provider.services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Layanan Pertama
            </a>
            <a href="{{ route('provider.dashboard') }}" class="bg-white border border-gray-200 hover:border-blue-300 text-gray-700 px-6 py-3 rounded-xl font-semibold text-sm transition">
                Pergi ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
