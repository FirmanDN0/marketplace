@extends('layouts.app')
@section('title', 'Kebijakan Privasi')
@section('content')
<div class="bg-white min-h-screen">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Kebijakan Privasi</h1>
    <p class="text-sm text-gray-400 mb-8">Terakhir diperbarui: {{ now()->format('d F Y') }}</p>

    <div class="prose prose-gray max-w-none space-y-6 text-gray-700 text-sm leading-relaxed">

        <h2 class="text-lg font-semibold text-gray-900 mt-8">1. Informasi yang Kami Kumpulkan</h2>
        <p>Kami mengumpulkan informasi yang Anda berikan saat mendaftar dan menggunakan platform:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Data Pribadi:</strong> Nama, email, username, nomor telepon, foto profil.</li>
            <li><strong>Data Profil:</strong> Bio, keahlian, bahasa, pengalaman, lokasi.</li>
            <li><strong>Data Transaksi:</strong> Riwayat pesanan, pembayaran, penarikan dana.</li>
            <li><strong>Data Komunikasi:</strong> Pesan antara pengguna, tiket customer service.</li>
            <li><strong>Data Teknis:</strong> Alamat IP, browser, cookies, log akses.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">2. Penggunaan Informasi</h2>
        <p>Informasi Anda digunakan untuk:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Menyediakan dan mengelola layanan platform.</li>
            <li>Memproses transaksi dan pembayaran.</li>
            <li>Mengirim notifikasi terkait aktivitas akun dan order.</li>
            <li>Meningkatkan pengalaman pengguna dan keamanan platform.</li>
            <li>Menyelesaikan dispute dan memberikan dukungan pelanggan.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">3. Penyimpanan Data</h2>
        <p>Data Anda disimpan dengan aman di server kami. Kami menggunakan enkripsi untuk melindungi data sensitif seperti password. Data transaksi disimpan sesuai kebutuhan hukum dan akuntansi.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">4. Pembagian Informasi</h2>
        <p>Kami tidak menjual data pribadi Anda. Informasi mungkin dibagikan dengan:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Pengguna lain:</strong> Nama dan profil publik terlihat oleh pengguna lain sesuai konteks (misal: provider melihat nama customer pada order).</li>
            <li><strong>Payment gateway:</strong> Data pembayaran dikirim ke Midtrans untuk memproses transaksi.</li>
            <li><strong>Pihak berwenang:</strong> Jika diwajibkan oleh hukum.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">5. Cookies</h2>
        <p>Platform menggunakan cookies untuk mengelola sesi login, preferensi, dan keamanan. Anda dapat mengatur cookies melalui pengaturan browser.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">6. Hak Pengguna</h2>
        <p>Anda berhak untuk:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Mengakses dan memperbarui data pribadi Anda melalui halaman profil.</li>
            <li>Meminta penghapusan akun dan data terkait.</li>
            <li>Menolak menerima email promosi.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">7. Keamanan</h2>
        <p>Kami menerapkan langkah-langkah keamanan termasuk enkripsi password, CSRF protection, rate limiting, dan verifikasi email untuk melindungi akun Anda.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">8. Perubahan Kebijakan</h2>
        <p>Kebijakan privasi ini dapat berubah sewaktu-waktu. Perubahan signifikan akan diinformasikan melalui email atau notifikasi platform.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">9. Kontak</h2>
        <p>Untuk pertanyaan tentang kebijakan privasi, hubungi kami melalui fitur Customer Service di platform atau email ke <strong>servemix0@gmail.com</strong>.</p>
    </div>
</div>
</div>
@endsection
