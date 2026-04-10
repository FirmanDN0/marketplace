@extends('layouts.app')
@section('title', 'FAQ - Pertanyaan Umum')
@section('content')
<div class="bg-white min-h-screen">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Pertanyaan Umum (FAQ)</h1>
    <p class="text-gray-500 mb-8">Jawaban untuk pertanyaan yang sering ditanyakan tentang ServeMix.</p>

    <div x-data="{ open: null }" class="space-y-3">

        @php $faqs = [
            ['q' => 'Apa itu ServeMix?', 'a' => 'ServeMix adalah marketplace layanan digital yang menghubungkan penyedia layanan (provider) dengan pelanggan (customer). Provider dapat menawarkan berbagai layanan seperti desain grafis, pengembangan web, penulisan konten, dan lainnya.'],
            ['q' => 'Bagaimana cara mendaftar?', 'a' => 'Klik tombol "Daftar" di halaman utama, isi data diri Anda, pilih peran sebagai Customer atau Provider, verifikasi email, dan akun Anda siap digunakan.'],
            ['q' => 'Apa perbedaan Customer dan Provider?', 'a' => 'Customer adalah pengguna yang membeli layanan, sedangkan Provider adalah pengguna yang menawarkan dan menjual layanan. Satu akun hanya bisa memiliki satu peran.'],
            ['q' => 'Bagaimana cara memesan layanan?', 'a' => 'Telusuri layanan yang tersedia, pilih paket yang sesuai (Basic/Standard/Premium), lakukan pemesanan, dan bayar melalui payment gateway atau saldo dompet.'],
            ['q' => 'Metode pembayaran apa saja yang tersedia?', 'a' => 'Kami menyediakan pembayaran melalui Midtrans (transfer bank, e-wallet, kartu kredit/debit) dan saldo dompet ServeMix. Anda dapat top up saldo kapan saja.'],
            ['q' => 'Berapa biaya layanan platform?', 'a' => 'ServeMix mengenakan biaya layanan sebesar 10% dari setiap transaksi yang berhasil. Biaya ini dipotong dari pendapatan provider.'],
            ['q' => 'Bagaimana jika saya tidak puas dengan hasil kerja?', 'a' => 'Anda dapat meminta revisi kepada provider. Jika masih tidak puas setelah revisi, Anda dapat membuka dispute dan admin akan meninjau kasus Anda.'],
            ['q' => 'Bagaimana cara membatalkan pesanan?', 'a' => 'Customer dapat membatalkan pesanan sebelum provider mulai mengerjakan. Provider juga dapat membatalkan dengan alasan valid. Dana akan dikembalikan ke saldo dompet.'],
            ['q' => 'Bagaimana cara menarik saldo?', 'a' => 'Masuk ke halaman Wallet, klik "Withdraw", isi jumlah (minimum Rp 50.000), pilih metode penarikan, dan masukkan detail rekening. Admin akan memproses dalam 1-3 hari kerja.'],
            ['q' => 'Apakah data saya aman?', 'a' => 'Ya, kami menggunakan enkripsi password, CSRF protection, verifikasi email, dan rate limiting untuk melindungi akun Anda. Baca Kebijakan Privasi kami untuk detail lengkap.'],
            ['q' => 'Bagaimana cara menghubungi support?', 'a' => 'Gunakan fitur Customer Service di platform. Anda akan dilayani oleh AI assistant terlebih dahulu, dan bisa minta dialihkan ke agen manusia jika diperlukan.'],
            ['q' => 'Apa yang terjadi jika pesanan tidak dibayar?', 'a' => 'Pesanan yang tidak dibayar dalam 24 jam akan otomatis dibatalkan oleh sistem.'],
            ['q' => 'Bagaimana cara menjadi Provider?', 'a' => 'Daftar akun dengan peran Provider, selesaikan proses onboarding (isi profil, tambah keahlian), lalu buat layanan pertama Anda.'],
        ]; @endphp

        @foreach($faqs as $i => $faq)
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                    class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                <span class="font-medium text-gray-900 text-sm pr-4">{{ $faq['q'] }}</span>
                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open === {{ $i }}" x-collapse>
                <div class="px-5 pb-4 text-sm text-gray-600 leading-relaxed">{{ $faq['a'] }}</div>
            </div>
        </div>
        @endforeach

    </div>

    <div class="mt-10 bg-blue-50 rounded-2xl p-6 text-center">
        <h3 class="font-semibold text-gray-900 mb-2">Masih punya pertanyaan?</h3>
        <p class="text-sm text-gray-600 mb-4">Tim support kami siap membantu Anda.</p>
        <a href="{{ route('customer-service.index') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
            <i class="fas fa-headset"></i> Hubungi Customer Service
        </a>
    </div>
</div>
</div>
@endsection
