{{-- Footer Partial - Consistent across all public pages --}}
<footer class="bg-white border-t border-gray-200 pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
            <div class="col-span-2 md:col-span-2">
                <a href="{{ route('home') }}" class="inline-block mb-5">
                    <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-9 w-auto object-contain">
                </a>
                <p class="text-sm text-gray-500 leading-relaxed mb-5 max-w-sm">Hubungkan dengan freelancer profesional terbaik dan selesaikan proyek Anda lebih cepat, lebih baik, dan lebih efisien.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 bg-gray-100 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300"><i class="fab fa-twitter text-sm"></i></a>
                    <a href="#" class="w-9 h-9 bg-gray-100 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300"><i class="fab fa-facebook-f text-sm"></i></a>
                    <a href="#" class="w-9 h-9 bg-gray-100 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300"><i class="fab fa-instagram text-sm"></i></a>
                    <a href="#" class="w-9 h-9 bg-gray-100 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300"><i class="fab fa-linkedin-in text-sm"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wider">Kategori</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Grafis & Desain</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Pemasaran Digital</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Penulisan & Terjemahan</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Video & Animasi</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Musik & Audio</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Pemrograman & Teknologi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wider">Tentang</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="{{ route('pages.privacy') }}" class="hover:text-blue-600 transition">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="hover:text-blue-600 transition">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4 text-sm uppercase tracking-wider">Bantuan</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="{{ route('pages.faq') }}" class="hover:text-blue-600 transition">FAQ</a></li>
                    <li><a href="{{ route('customer-service.index') }}" class="hover:text-blue-600 transition">Layanan Pelanggan</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-400">
            <span>&copy; {{ date('Y') }} ServeMix International Ltd. Hak cipta dilindungi.</span>
            <div class="flex items-center gap-4">
                <span>Indonesia</span>
                <span>IDR</span>
            </div>
        </div>
    </div>
</footer>
