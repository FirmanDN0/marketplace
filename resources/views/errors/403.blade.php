<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dilarang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center">
        {{-- Illustration --}}
        <div class="relative mb-8">
            <div class="text-[12rem] font-black text-red-100/50 leading-none select-none">403</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center text-red-600 shadow-xl shadow-red-100">
                    <i class="fas fa-lock text-5xl"></i>
                </div>
            </div>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-900 mb-3">Akses Ditolak!</h1>
        <p class="text-slate-500 mb-10 leading-relaxed">
            Ups! Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi admin jika ini adalah kesalahan.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded-2xl font-bold text-sm transition shadow-lg shadow-slate-200 flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <a href="{{ route('customer-service.index') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-8 py-3.5 rounded-2xl font-bold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-headset"></i> Hubungi Dukungan
            </a>
        </div>

        <div class="mt-12 text-slate-400 text-xs font-medium uppercase tracking-widest">
            &copy; {{ date('Y') }} ServeMix Marketplace
        </div>
    </div>
</body>
</html>
