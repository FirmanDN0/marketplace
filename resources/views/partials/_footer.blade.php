{{-- Footer Partial - Consistent across all public pages --}}
<footer class="bg-white border-t border-gray-100 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-10 mb-16">
            <div class="col-span-2 md:col-span-2 space-y-6">
                <a href="{{ route('home') }}" class="inline-block transition duration-300 hover:opacity-85">
                    <img src="{{ asset('images/logo_horizontal.webp') }}" alt="ServeMix" class="h-10 w-auto object-contain">
                </a>
                <p class="text-sm text-gray-500 leading-relaxed max-w-sm font-medium">
                    Hubungkan bisnis Anda dengan talenta freelance profesional terbaik secara mudah, cepat, dan aman.
                </p>
                <div class="flex items-center gap-3">
                    @foreach([
                        ['icon' => 'fa-twitter', 'url' => '#'],
                        ['icon' => 'fa-facebook-f', 'url' => '#'],
                        ['icon' => 'fa-instagram', 'url' => '#'],
                        ['icon' => 'fa-linkedin-in', 'url' => '#'],
                    ] as $social)
                        <a href="{{ $social['url'] }}" class="w-9 h-9 bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95 shadow-sm">
                            <i class="fab {{ $social['icon'] }} text-xs"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-5 text-xs uppercase tracking-widest">Kategori</h4>
                <ul class="space-y-3.5 text-sm font-semibold">
                    <li><a href="{{ route('services.index') }}" class="text-gray-500 hover:text-blue-600 transition">Grafis & Desain</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-500 hover:text-blue-600 transition">Pemasaran Digital</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-500 hover:text-blue-600 transition">Penulisan & Terjemahan</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-500 hover:text-blue-600 transition">Video & Animasi</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-500 hover:text-blue-600 transition">Pemrograman & IT</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-5 text-xs uppercase tracking-widest">Tentang</h4>
                <ul class="space-y-3.5 text-sm font-semibold">
                    <li><a href="{{ route('pages.privacy') }}" class="text-gray-500 hover:text-blue-600 transition">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="text-gray-500 hover:text-blue-600 transition">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-5 text-xs uppercase tracking-widest">Bantuan</h4>
                <ul class="space-y-3.5 text-sm font-semibold">
                    <li><a href="{{ route('pages.faq') }}" class="text-gray-500 hover:text-blue-600 transition">FAQ</a></li>
                    <li><a href="{{ route('customer-service.index') }}" class="text-gray-500 hover:text-blue-600 transition">Layanan Pengguna</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-semibold text-gray-400">
            <span>&copy; {{ date('Y') }} ServeMix. Hak Cipta Dilindungi.</span>
            <div class="flex items-center gap-6">
                <span>Indonesia</span>
                <span>IDR (Rp)</span>
            </div>
        </div>
    </div>
</footer>
