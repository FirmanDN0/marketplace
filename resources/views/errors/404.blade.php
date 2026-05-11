<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full text-center">
        {{-- Illustration --}}
        <div class="relative mb-8">
            <div class="text-[12rem] font-black text-slate-200/50 leading-none select-none">404</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 animate-bounce shadow-xl shadow-blue-100">
                    <i class="fas fa-search text-5xl"></i>
                </div>
            </div>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-900 mb-3">Waduh! Halaman Hilang</h1>
        <p class="text-slate-500 mb-10 leading-relaxed">
            Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan ke alamat lain.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-2xl font-bold text-sm transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <button onclick="window.history.back()" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-8 py-3.5 rounded-2xl font-bold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Sebelumnya
            </button>
        </div>

        <div class="mt-12 text-slate-400 text-xs font-medium uppercase tracking-widest">
            &copy; {{ date('Y') }} ServeMix Marketplace
        </div>
    </div>
</body>
</html>
