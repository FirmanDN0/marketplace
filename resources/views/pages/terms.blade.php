@extends('layouts.app')
@section('title', 'Syarat & Ketentuan')
@section('content')
<div class="bg-white min-h-screen">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Syarat & Ketentuan</h1>
    <p class="text-sm text-gray-400 mb-8">Terakhir diperbarui: {{ now()->format('d F Y') }}</p>

    <div class="prose prose-gray max-w-none space-y-6 text-gray-700 text-sm leading-relaxed">

        <h2 class="text-lg font-semibold text-gray-900 mt-8">1. Penerimaan Syarat</h2>
        <p>Dengan mengakses dan menggunakan platform ServeMix, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak menyetujui salah satu syarat, Anda tidak diperkenankan menggunakan layanan kami.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">2. Definisi</h2>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Platform</strong> — ServeMix, marketplace layanan digital.</li>
            <li><strong>Provider</strong> — Pengguna yang menawarkan dan menjual layanan.</li>
            <li><strong>Customer</strong> — Pengguna yang membeli layanan.</li>
            <li><strong>Order</strong> — Transaksi pembelian layanan antara Customer dan Provider.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">3. Pendaftaran Akun</h2>
        <p>Pengguna wajib mendaftar dengan informasi yang benar dan valid. Setiap akun bersifat personal dan tidak boleh dipindahtangankan. ServeMix berhak menonaktifkan akun yang melanggar ketentuan.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">4. Layanan & Transaksi</h2>
        <p>Provider bertanggung jawab atas kualitas layanan yang ditawarkan. Customer wajib memberikan deskripsi kebutuhan yang jelas. Platform mengenakan biaya layanan sebesar 10% dari setiap transaksi yang berhasil.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">5. Pembayaran</h2>
        <p>Pembayaran dilakukan melalui payment gateway (Midtrans) atau saldo dompet. Dana akan ditahan oleh platform hingga order selesai. Refund dilakukan ke saldo dompet sesuai kebijakan pembatalan.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">6. Pembatalan & Refund</h2>
        <ul class="list-disc pl-6 space-y-1">
            <li>Customer dapat membatalkan order sebelum provider mulai mengerjakan.</li>
            <li>Provider dapat membatalkan order yang belum selesai dengan alasan yang valid.</li>
            <li>Refund dikembalikan ke saldo dompet pengguna.</li>
            <li>Order yang tidak dibayar dalam 24 jam akan otomatis dibatalkan.</li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">7. Dispute</h2>
        <p>Jika terjadi perselisihan, Customer dapat membuka dispute melalui halaman order. Admin akan meninjau dan menyelesaikan dispute berdasarkan bukti yang tersedia.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">8. Penarikan Dana</h2>
        <p>Provider dapat menarik saldo dengan minimum Rp 50.000. Penarikan diproses oleh admin dalam 1-3 hari kerja melalui metode yang tersedia (transfer bank, e-wallet).</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">9. Konten & Perilaku</h2>
        <p>Pengguna dilarang mengunggah konten ilegal, menyinggung, atau melanggar hak cipta. ServeMix berhak menghapus konten dan menonaktifkan akun yang melanggar.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">10. Batasan Tanggung Jawab</h2>
        <p>ServeMix bertindak sebagai perantara dan tidak bertanggung jawab atas kualitas layanan yang diberikan oleh Provider. Platform menyediakan mekanisme dispute untuk menyelesaikan perselisihan.</p>

        <h2 class="text-lg font-semibold text-gray-900 mt-8">11. Perubahan Ketentuan</h2>
        <p>ServeMix berhak mengubah syarat dan ketentuan sewaktu-waktu. Perubahan akan diinformasikan melalui email atau notifikasi di platform.</p>
    </div>
</div>
</div>
@endsection
