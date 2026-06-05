<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceImage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AppNotification;
use App\Models\Dispute;
use App\Models\WithdrawRequest;
use App\Models\TopUp;
use App\Models\CsConversation;
use App\Models\CsMessage;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Truncate all tables to start fresh ──────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('dispute_messages')->truncate();
        DB::table('disputes')->truncate();
        DB::table('custom_offers')->truncate();
        DB::table('cs_messages')->truncate();
        DB::table('cs_conversations')->truncate();
        DB::table('top_ups')->truncate();
        DB::table('withdraw_requests')->truncate();
        DB::table('favorites')->truncate();
        DB::table('notifications')->truncate();
        DB::table('messages')->truncate();
        DB::table('conversations')->truncate();
        DB::table('reviews')->truncate();
        DB::table('payment_logs')->truncate();
        DB::table('payments')->truncate();
        DB::table('transactions')->truncate();
        DB::table('orders')->truncate();
        DB::table('service_packages')->truncate();
        DB::table('service_images')->truncate();
        DB::table('services')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();
        $password = Hash::make('password');

        // ─── 1. Call CategorySeeder ──────────────────────────────────────────
        $this->command->info('Seeding Categories...');
        $this->call(CategorySeeder::class);

        // ─── 2. Create Users ─────────────────────────────────────────────────
        $this->command->info('Creating Users & Profiles...');

        // Admin
        $admin = User::create([
            'name'              => 'Super Admin',
            'username'          => 'admin',
            'email'             => 'admin@marketplace.com',
            'password'          => $password,
            'role'              => 'admin',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);
        UserProfile::create([
            'user_id' => $admin->id,
            'balance' => 0,
            'country' => 'Indonesia',
            'city'    => 'Jakarta',
        ]);

        // Providers
        $provider1 = User::create([
            'name'                => 'Provider Demo',
            'username'            => 'provider',
            'email'               => 'provider@marketplace.com',
            'password'            => $password,
            'role'                => 'provider',
            'status'              => 'active',
            'provider_setup_step' => 3,
            'email_verified_at'   => $now,
        ]);
        UserProfile::create([
            'user_id'              => $provider1->id,
            'bio'                  => 'Profesional berpengalaman di bidang pengembangan web dan desain sistem Laravel.',
            'skills'               => ['Laravel', 'Vue.js', 'UI/UX Design', 'TailwindCSS'],
            'country'              => 'Indonesia',
            'city'                 => 'Jakarta',
            'languages'            => ['Bahasa Indonesia', 'English'],
            'experience_years'     => 5,
            'hourly_rate'          => 150000,
            'balance'              => 2400000.00,
            'total_earned'         => 3200000.00,
            'is_verified_provider' => true,
        ]);

        \App\Models\ProviderPortfolio::insert([
            [
                'provider_id' => $provider1->id,
                'title'       => 'E-Commerce Dashboard',
                'description' => 'Dashboard admin lengkap dengan grafik penjualan dan manajemen inventaris.',
                'media_path'  => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=500&q=80',
                'media_type'  => 'image',
                'sort_order'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'provider_id' => $provider1->id,
                'title'       => 'Sistem Booking Klinik',
                'description' => 'Aplikasi web untuk penjadwalan pasien dan rekam medis elektronik.',
                'media_path'  => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=500&q=80',
                'media_type'  => 'image',
                'sort_order'  => 2,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'provider_id' => $provider1->id,
                'title'       => 'Company Profile Startup',
                'description' => 'Website modern yang menawan untuk startup teknologi berbasis di Jakarta.',
                'media_path'  => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=500&q=80',
                'media_type'  => 'image',
                'sort_order'  => 3,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'provider_id' => $provider1->id,
                'title'       => 'Sistem Kasir (POS)',
                'description' => 'Point of Sales responsif yang bekerja secara realtime.',
                'media_path'  => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=500&q=80',
                'media_type'  => 'image',
                'sort_order'  => 4,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]
        ]);

        $provider2 = User::create([
            'name'                => 'Sarah Designer',
            'username'            => 'sarah',
            'email'               => 'sarah@marketplace.com',
            'password'            => $password,
            'role'                => 'provider',
            'status'              => 'active',
            'provider_setup_step' => 3,
            'email_verified_at'   => $now,
        ]);
        UserProfile::create([
            'user_id'          => $provider2->id,
            'bio'              => 'UI/UX Designer yang berfokus pada desain mobile app dan website modern yang intuitif.',
            'skills'           => ['Figma', 'Adobe Illustrator', 'UI Design', 'Wireframing'],
            'country'          => 'Indonesia',
            'city'             => 'Bandung',
            'languages'        => ['Bahasa Indonesia', 'English'],
            'experience_years' => 3,
            'hourly_rate'      => 100000,
            'balance'          => 1200000.00,
            'total_earned'     => 1500000.00,
        ]);

        $provider3 = User::create([
            'name'                => 'Alex Editor',
            'username'            => 'alex',
            'email'               => 'alex@marketplace.com',
            'password'            => $password,
            'role'                => 'provider',
            'status'              => 'active',
            'provider_setup_step' => 3,
            'email_verified_at'   => $now,
        ]);
        UserProfile::create([
            'user_id'          => $provider3->id,
            'bio'              => 'Video Editor & Motion Designer profesional untuk konten Youtube, Reels, dan video promosi.',
            'skills'           => ['Premiere Pro', 'After Effects', 'Color Grading', 'Sound Design'],
            'country'          => 'Indonesia',
            'city'             => 'Surabaya',
            'languages'        => ['Bahasa Indonesia'],
            'experience_years' => 4,
            'hourly_rate'      => 80000,
            'balance'          => 800000.00,
            'total_earned'     => 800000.00,
        ]);

        // Customers
        $customer1 = User::create([
            'name'              => 'Customer Demo',
            'username'          => 'customer',
            'email'             => 'customer@marketplace.com',
            'password'          => $password,
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);
        UserProfile::create([
            'user_id'     => $customer1->id,
            'balance'     => 5000000.00,
            'total_spent' => 1500000.00,
            'country'     => 'Indonesia',
            'city'        => 'Malang',
        ]);

        $customer2 = User::create([
            'name'              => 'Jane Client',
            'username'          => 'jane',
            'email'             => 'jane@marketplace.com',
            'password'          => $password,
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);
        UserProfile::create([
            'user_id'     => $customer2->id,
            'balance'     => 3500000.00,
            'total_spent' => 2000000.00,
            'country'     => 'Indonesia',
            'city'        => 'Semarang',
        ]);

        $customer3 = User::create([
            'name'              => 'Bob Buyer',
            'username'          => 'bob',
            'email'             => 'bob@marketplace.com',
            'password'          => $password,
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => $now,
        ]);
        UserProfile::create([
            'user_id'     => $customer3->id,
            'balance'     => 1500000.00,
            'total_spent' => 500000.00,
            'country'     => 'Indonesia',
            'city'        => 'Yogyakarta',
        ]);


        // ─── 3. Seeding Services & Packages ──────────────────────────────────
        $this->command->info('Creating Services & Service Packages...');

        // Fetch subcategories
        $webCat = Category::where('slug', 'like', 'pengembangan-web%')->first() ?? Category::first();
        $uiuxCat = Category::where('slug', 'like', 'uiux-design%')->first() ?? Category::first();
        $videoCat = Category::where('slug', 'like', 'editing-video%')->first() ?? Category::first();
        $copyCat = Category::where('slug', 'like', 'copywriting-iklan%')->first() ?? Category::first();

        // Service 1: Web Development (Provider 1 - John)
        $service1 = Service::create([
            'provider_id'   => $provider1->id,
            'category_id'   => $webCat->id,
            'title'         => 'Jasa Pembuatan Website Laravel Custom Cepat & Responsif',
            'slug'          => 'jasa-pembuatan-website-laravel-custom-cepat-responsif',
            'description'   => "Apakah Anda memerlukan website profesional untuk bisnis Anda? Saya siap membangun website kustom berkualitas tinggi dengan framework Laravel!\n\nLayanan ini mencakup:\n- Desain modern dan responsif di semua perangkat.\n- Kode clean, aman, dan mudah dipelihara.\n- Kecepatan memuat halaman yang optimal.\n- Integrasi database, payment gateway, dan fitur custom sesuai kebutuhan bisnis Anda.\n\nSilakan diskusikan kebutuhan proyek Anda dengan saya terlebih dahulu.",
            'tags'          => ['Laravel', 'Website', 'PHP', 'Backend'],
            'status'        => 'active',
            'avg_rating'    => 5.00,
            'total_reviews' => 1,
            'total_orders'  => 1,
        ]);

        ServicePackage::create([
            'service_id'    => $service1->id,
            'package_type'  => 'basic',
            'name'          => 'Landing Page Dasar',
            'description'   => 'Satu halaman landing page responsif, HTML/CSS/JS standar, file source code lengkap.',
            'price'         => 250000.00,
            'delivery_days' => 3,
            'revisions'     => 1,
            'features'      => ['1 Halaman Landing Page', 'Desain Responsif', 'HTML & CSS', 'File Source Code'],
        ]);

        $pkg1Standard = ServicePackage::create([
            'service_id'    => $service1->id,
            'package_type'  => 'standard',
            'name'          => 'Website Company Profile',
            'description'   => 'Hingga 5 halaman dinamis, admin panel sederhana, integrasi database, free hosting setup.',
            'price'         => 1500000.00,
            'delivery_days' => 7,
            'revisions'     => 3,
            'features'      => ['Hingga 5 Halaman', 'Desain Responsif', 'Integrasi Database', 'Admin Panel Sederhana', 'File Source Code'],
        ]);

        ServicePackage::create([
            'service_id'    => $service1->id,
            'package_type'  => 'premium',
            'name'          => 'Aplikasi Web Custom / E-Commerce',
            'description'   => 'Website full-featured, e-commerce lengkap, checkout & keranjang belanja, integrasi payment gateway, unlimited revisions.',
            'price'         => 4500000.00,
            'delivery_days' => 14,
            'revisions'     => -1,
            'features'      => ['Halaman Produk Tak Terbatas', 'Keranjang Belanja & Checkout', 'Integrasi Payment Gateway', 'Fitur Admin Lengkap', 'Garansi Error 1 Bulan'],
        ]);

        ServiceImage::create(['service_id' => $service1->id, 'image_path' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80', 'is_cover' => true, 'sort_order' => 1]);

        // Service 2: UI/UX Figma (Provider 2 - Sarah)
        $service2 = Service::create([
            'provider_id'   => $provider2->id,
            'category_id'   => $uiuxCat->id,
            'title'         => 'Desain UI/UX Mobile App & Website di Figma',
            'slug'          => 'desain-uiux-mobile-app-website-di-figma',
            'description'   => "Saya menawarkan jasa desain UI/UX untuk aplikasi mobile (Android/iOS) dan website menggunakan Figma.\n\nFokus saya adalah menciptakan desain yang estetik, fungsional, dan berpusat pada kepuasan pengguna (user-centered design).\n\nApa yang akan Anda dapatkan:\n- Desain wireframe (Low Fidelity) & visual design (High Fidelity).\n- Prototype interaktif di Figma.\n- Asset siap ekspor untuk developer.\n- Konsultasi UX gratis.",
            'tags'          => ['Figma', 'UIUX', 'Mobile Design', 'Web Design'],
            'status'        => 'active',
            'avg_rating'    => 0.00,
            'total_reviews' => 0,
            'total_orders'  => 0,
        ]);

        ServicePackage::create([
            'service_id'    => $service2->id,
            'package_type'  => 'basic',
            'name'          => '1 Halaman Landing Page UI',
            'description'   => 'UI Desain untuk 1 halaman website landing page, file figma disertakan.',
            'price'         => 300000.00,
            'delivery_days' => 2,
            'revisions'     => 2,
            'features'      => ['1 Halaman UI Desain', 'Mockup Resolusi Tinggi', 'Akses File Figma Original'],
        ]);

        $pkg2Standard = ServicePackage::create([
            'service_id'    => $service2->id,
            'package_type'  => 'standard',
            'name'          => 'Web App UI (5 Halaman)',
            'description'   => 'Desain UI/UX responsif untuk 5 halaman website aplikasi, prototype dasar.',
            'price'         => 1200000.00,
            'delivery_days' => 5,
            'revisions'     => 4,
            'features'      => ['5 Halaman UI Desain', 'Desain Responsif', 'Prototyping Interaktif', 'Akses File Figma Original'],
        ]);

        ServicePackage::create([
            'service_id'    => $service2->id,
            'package_type'  => 'premium',
            'name'          => 'Full Mobile App UI (15 Halaman)',
            'description'   => 'Desain UI/UX lengkap untuk aplikasi mobile maksimal 15 screens, wireframing, full interactive prototype.',
            'price'         => 3000000.00,
            'delivery_days' => 10,
            'revisions'     => -1,
            'features'      => ['Hingga 15 Halaman UI', 'User Flow & Wireframe', 'Prototyping Interaktif Penuh', 'Asset Ekspor Siap Pakai', 'Akses File Figma Original'],
        ]);

        ServiceImage::create(['service_id' => $service2->id, 'image_path' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=600&q=80', 'is_cover' => true, 'sort_order' => 1]);

        // Service 3: Video Editing (Provider 3 - Alex)
        $service3 = Service::create([
            'provider_id'   => $provider3->id,
            'category_id'   => $videoCat->id,
            'title'         => 'Video Editing Profesional untuk YouTube, TikTok & Reels',
            'slug'          => 'video-editing-profesional-untuk-youtube-tiktok-reels',
            'description'   => "Ubah video mentah Anda menjadi konten visual yang memikat dan berdurasi tinggi!\n\nSaya melayani editing video profesional untuk berbagai platform:\n- Video YouTube (vlog, edukasi, gaming).\n- Iklan media sosial & Company Profile.\n- Konten berdurasi pendek (TikTok, Instagram Reels, Shorts).\n\nDilengkapi dengan color grading sinematik, efek suara, teks pop-up dinamis, dan musik bebas royalti.",
            'tags'          => ['Video Edit', 'Reels', 'TikTok', 'Premiere'],
            'status'        => 'active',
            'avg_rating'    => 0.00,
            'total_reviews' => 0,
            'total_orders'  => 0,
        ]);

        $pkg3Basic = ServicePackage::create([
            'service_id'    => $service3->id,
            'package_type'  => 'basic',
            'name'          => 'TikTok/Reels/Shorts Edit',
            'description'   => '1 Video berdurasi maksimal 1 menit dengan penambahan teks/subtitle menarik, cut halus, sound effect.',
            'price'         => 100000.00,
            'delivery_days' => 1,
            'revisions'     => 2,
            'features'      => ['Durasi Maksimal 1 Menit', 'Penambahan Subtitle / Teks Menarik', 'Color Grading Dasar', 'Sound Effect Standar'],
        ]);

        ServicePackage::create([
            'service_id'    => $service3->id,
            'package_type'  => 'standard',
            'name'          => 'YouTube Video Edit (10 Menit)',
            'description'   => 'Editing video Youtube berdurasi maksimal 10 menit, cut, transisi, b-roll, color grading standard, text pop-up.',
            'price'         => 400000.00,
            'delivery_days' => 3,
            'revisions'     => 3,
            'features'      => ['Durasi Maksimal 10 Menit', 'Cut & Transition Halus', 'Penambahan B-roll & Sound Effect', 'Motion Graphics Dasar', 'Color Grading Profesional'],
        ]);

        ServicePackage::create([
            'service_id'    => $service3->id,
            'package_type'  => 'premium',
            'name'          => 'Premium Corporate Video',
            'description'   => 'Editing video profil perusahaan / iklan premium durasi maks 5 menit, mixing audio, advanced color grading, full motion tracking.',
            'price'         => 1500000.00,
            'delivery_days' => 5,
            'revisions'     => 5,
            'features'      => ['Durasi Maksimal 5 Menit', 'Editing Tingkat Lanjut', 'Sound Design & Mixing Premium', 'Motion Graphics & Tracking Lengkap', 'Color Grading Sinematik'],
        ]);

        ServiceImage::create(['service_id' => $service3->id, 'image_path' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?auto=format&fit=crop&w=600&q=80', 'is_cover' => true, 'sort_order' => 1]);


        // ─── 3.5 Seeding Vouchers ───────────────────────────────────────────
        $this->command->info('Creating Promo Vouchers...');

        \App\Models\Voucher::insert([
            [
                'code'         => 'DISKON50K',
                'type'         => 'fixed',
                'value'        => 50000.00,
                'min_purchase' => 100000.00,
                'max_discount' => null,
                'quota'        => 100,
                'used_count'   => 0,
                'valid_until'  => $now->copy()->addMonths(1),
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'code'         => 'MANTAP10',
                'type'         => 'percentage',
                'value'        => 10.00,
                'min_purchase' => 500000.00,
                'max_discount' => 100000.00,
                'quota'        => 50,
                'used_count'   => 0,
                'valid_until'  => $now->copy()->addMonths(1),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]
        ]);

        // ─── 4. Seeding Orders, Payments, Transactions, Reviews ──────────────
        $this->command->info('Creating Orders, Payments, Transactions & Reviews...');

        // --- Scenario 1: Completed Order (Tech Website) ---
        $order1 = Order::create([
            'order_number'              => 'ORD-SMX90321',
            'customer_id'               => $customer1->id,
            'provider_id'               => $provider1->id,
            'service_id'                => $service1->id,
            'package_id'                => $pkg1Standard->id,
            'price'                     => 1500000.00,
            'tax_fee'                   => 150000.00,
            'discount'                  => 0,
            'grand_total'               => 1650000.00,
            'status'                    => 'completed',
            'delivery_deadline'         => $now->copy()->subDays(3),
            'notes'                     => 'Tolong buatkan website company profile untuk klinik gigi saya.',
            'requirements'              => 'Logo klinik, daftar dokter, dan daftar layanan klinik.',
            'requirements_submitted_at' => $now->copy()->subDays(10),
            'delivery_file'             => 'deliveries/company_profile_klinik.zip',
            'delivery_message'          => 'Halo, berikut adalah website company profile klinik gigi Anda yang sudah selesai dibuat secara responsif dan rapi. File zip source code terlampir. Terima kasih!',
            'delivered_at'              => $now->copy()->subDays(3),
            'completed_at'              => $now->copy()->subDays(2),
        ]);

        $payment1 = Payment::create([
            'order_id'               => $order1->id,
            'user_id'                => $customer1->id,
            'amount'                 => 1650000.00,
            'payment_method'         => 'balance',
            'status'                 => 'success',
            'paid_at'                => $now->copy()->subDays(10),
            'gateway_transaction_id' => 'WALLET-' . $order1->order_number,
        ]);

        PaymentLog::create([
            'payment_id' => $payment1->id,
            'event'      => 'payment_wallet_success',
            'payload'    => ['balance_before' => 6500000.00, 'balance_after' => 5000000.00],
        ]);

        // Debit Customer Wallet for Payment
        Transaction::create([
            'user_id'        => $customer1->id,
            'payment_id'     => $payment1->id,
            'type'           => 'payment',
            'amount'         => -1650000.00,
            'balance_before' => 6650000.00,
            'balance_after'  => 5000000.00,
            'description'    => "Pembayaran saldo untuk Order #{$order1->order_number}",
            'reference_id'   => $order1->order_number,
        ]);

        // Credit Provider Wallet for Completed Earning
        Transaction::create([
            'user_id'        => $provider1->id,
            'payment_id'     => $payment1->id,
            'type'           => 'earning',
            'amount'         => 1500000.00,
            'balance_before' => 900000.00,
            'balance_after'  => 2400000.00,
            'description'    => "Earning untuk Order #{$order1->order_number}",
            'reference_id'   => $order1->order_number,
        ]);

        // Fee log for Admin
        Transaction::create([
            'user_id'        => $provider1->id,
            'payment_id'     => $payment1->id,
            'type'           => 'fee',
            'amount'         => 150000.00,
            'balance_before' => 0,
            'balance_after'  => 0,
            'description'    => "Platform fee (10%) untuk Order #{$order1->order_number}",
            'reference_id'   => $order1->order_number,
        ]);

        // Review
        Review::create([
            'order_id'       => $order1->id,
            'customer_id'    => $customer1->id,
            'provider_id'    => $provider1->id,
            'service_id'     => $service1->id,
            'rating'         => 5,
            'comment'        => 'Sangat puas dengan hasilnya! Pelayanan ramah, respon cepat, dan website berjalan dengan sempurna. Direkomendasikan sekali!',
            'provider_reply' => 'Terima kasih banyak atas kepercayaannya! Sukses selalu untuk kliniknya.',
            'replied_at'     => $now->copy()->subDays(1),
            'is_visible'     => true,
        ]);


        // --- Scenario 2: In Progress Order (Figma UI/UX) ---
        $order2 = Order::create([
            'order_number'              => 'ORD-SMX45012',
            'customer_id'               => $customer2->id,
            'provider_id'               => $provider2->id,
            'service_id'                => $service2->id,
            'package_id'                => $pkg2Standard->id,
            'price'                     => 1200000.00,
            'tax_fee'                   => 120000.00,
            'discount'                  => 0,
            'grand_total'               => 1320000.00,
            'status'                    => 'in_progress',
            'delivery_deadline'         => $now->copy()->addDays(2),
            'notes'                     => 'Desain UI/UX untuk aplikasi mobile e-learning anak-anak.',
            'requirements'              => 'Gaya warna pastel cerah, logo perusahaan, wireframe coretan kertas terlampir.',
            'requirements_submitted_at' => $now->copy()->subDays(3),
        ]);

        $payment2 = Payment::create([
            'order_id'               => $order2->id,
            'user_id'                => $customer2->id,
            'amount'                 => 1320000.00,
            'payment_method'         => 'midtrans',
            'status'                 => 'success',
            'paid_at'                => $now->copy()->subDays(3),
            'gateway_transaction_id' => 'MIDTRANS-SNAP-' . strtoupper(Str::random(10)),
        ]);

        Transaction::create([
            'user_id'        => $customer2->id,
            'payment_id'     => $payment2->id,
            'type'           => 'payment',
            'amount'         => -1320000.00,
            'balance_before' => 4820000.00,
            'balance_after'  => 3500000.00,
            'description'    => "Pembayaran Midtrans untuk Order #{$order2->order_number}",
            'reference_id'   => $order2->order_number,
        ]);


        // --- Scenario 3: Delivered Order (Video Editing) ---
        $order3 = Order::create([
            'order_number'              => 'ORD-SMX10492',
            'customer_id'               => $customer3->id,
            'provider_id'               => $provider3->id,
            'service_id'                => $service3->id,
            'package_id'                => $pkg3Basic->id,
            'price'                     => 100000.00,
            'tax_fee'                   => 10000.00,
            'discount'                  => 0,
            'grand_total'               => 110000.00,
            'status'                    => 'delivered',
            'delivery_deadline'         => $now->copy()->subHours(12),
            'notes'                     => 'Tolong edit video traveling 1 menit ini untuk Reels Instagram.',
            'requirements'              => 'Link google drive berisi 5 file mentah video, lagu background ceria bebas royalti.',
            'requirements_submitted_at' => $now->copy()->subDays(2),
            'delivery_file'             => 'deliveries/reels_traveling_bali.mp4',
            'delivery_message'          => 'Halo! Ini video reels traveling Bali yang sudah selesai saya edit dengan transisi estetik, efek sinkronisasi beat, dan color grading hangat sesuai permintaan. Silakan diperiksa kembali. Terima kasih!',
            'delivered_at'              => $now->copy()->subDays(1),
        ]);

        $payment3 = Payment::create([
            'order_id'               => $order3->id,
            'user_id'                => $customer3->id,
            'amount'                 => 110000.00,
            'payment_method'         => 'balance',
            'status'                 => 'success',
            'paid_at'                => $now->copy()->subDays(2),
            'gateway_transaction_id' => 'WALLET-' . $order3->order_number,
        ]);

        Transaction::create([
            'user_id'        => $customer3->id,
            'payment_id'     => $payment3->id,
            'type'           => 'payment',
            'amount'         => -110000.00,
            'balance_before' => 1610000.00,
            'balance_after'  => 1500000.00,
            'description'    => "Pembayaran saldo untuk Order #{$order3->order_number}",
            'reference_id'   => $order3->order_number,
        ]);

        // --- Scenario 4: Auto-Complete Candidate (Delivered > 3 days ago) ---
        $order4 = Order::create([
            'order_number'              => 'ORD-SMX99999',
            'customer_id'               => $customer2->id,
            'provider_id'               => $provider1->id,
            'service_id'                => $service1->id,
            'package_id'                => $pkg1Standard->id,
            'price'                     => 1500000.00,
            'tax_fee'                   => 150000.00,
            'discount'                  => 0,
            'grand_total'               => 1650000.00,
            'status'                    => 'delivered',
            'delivery_deadline'         => $now->copy()->subDays(5),
            'notes'                     => 'Tolong buatkan landing page sederhana saja.',
            'requirements'              => 'Data lengkap terlampir',
            'requirements_submitted_at' => $now->copy()->subDays(10),
            'delivery_file'             => 'deliveries/landing_page_demo.zip',
            'delivery_message'          => 'Halo Kak, pesanan sudah saya selesaikan dan lampirkan.',
            'delivered_at'              => $now->copy()->subDays(4), // 4 days ago
        ]);
        Payment::create([
            'order_id'               => $order4->id,
            'user_id'                => $customer2->id,
            'amount'                 => 1650000.00,
            'payment_method'         => 'balance',
            'status'                 => 'success',
            'paid_at'                => $now->copy()->subDays(10),
            'gateway_transaction_id' => 'WALLET-' . $order4->order_number,
        ]);

        // --- Scenario 5: Disputed Order ---
        $order5 = Order::create([
            'order_number'              => 'ORD-SMX88888',
            'customer_id'               => $customer1->id,
            'provider_id'               => $provider2->id,
            'service_id'                => $service2->id,
            'package_id'                => $pkg2Standard->id,
            'price'                     => 1200000.00,
            'tax_fee'                   => 120000.00,
            'discount'                  => 0,
            'grand_total'               => 1320000.00,
            'status'                    => 'disputed',
            'delivery_deadline'         => $now->copy()->subDays(2),
            'notes'                     => 'UI UX harus mirip referensi Dribbble.',
            'requirements'              => 'Warna utama biru dan putih',
            'requirements_submitted_at' => $now->copy()->subDays(5),
            'delivery_file'             => 'deliveries/figma_design.zip',
            'delivery_message'          => 'Berikut hasil desainnya.',
            'delivered_at'              => $now->copy()->subDays(1),
        ]);
        Payment::create([
            'order_id'               => $order5->id,
            'user_id'                => $customer1->id,
            'amount'                 => 1320000.00,
            'payment_method'         => 'balance',
            'status'                 => 'success',
            'paid_at'                => $now->copy()->subDays(5),
            'gateway_transaction_id' => 'WALLET-' . $order5->order_number,
        ]);

        $dispute = Dispute::create([
            'order_id'    => $order5->id,
            'opened_by'   => $customer1->id,
            'reason'      => 'Desain tidak mirip dengan referensi sama sekali',
            'description' => 'Warna yang digunakan hijau dan kuning, bukan biru putih.',
            'status'      => 'open',
            'created_at'  => $now->copy()->subHours(12),
        ]);

        \App\Models\DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id'    => $customer1->id,
            'type'       => 'text',
            'message'    => 'Saya sangat kecewa, hasil desainnya tidak sesuai dengan brief awal yang meminta dominasi biru putih.',
            'created_at' => $now->copy()->subHours(12),
        ]);

        \App\Models\DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id'    => $provider2->id,
            'type'       => 'refund_proposal',
            'message'    => 'Mohon maaf atas kesalahan ini, saya bersedia mengembalikan dana sebesar 50% jika kakak berkenan.',
            'refund_percentage' => 50,
            'created_at' => $now->copy()->subHours(10),
        ]);


        // ─── 5. Seeding Real Live Conversations & Chat Messages ──────────────
        $this->command->info('Creating Chat Messages (Live & History)...');

        $conv = Conversation::create([
            'customer_id'     => $customer1->id,
            'provider_id'     => $provider1->id,
            'service_id'      => $service1->id,
            'last_message_at' => $now,
        ]);

        // Historical messages
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $customer1->id,
            'message_text'    => 'Halo, saya tertarik dengan jasa pembuatan website Laravel. Apakah Anda bisa membuat custom API untuk integrasi?',
            'created_at'      => $now->copy()->subDays(8),
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $provider1->id,
            'message_text'    => 'Halo! Tentu saja bisa. Saya sering membuat integrasi API untuk berbagai keperluan seperti payment gateway, kurir logistik, dan sistem internal. Ada spesifikasi khusus?',
            'created_at'      => $now->copy()->subDays(8)->addMinutes(15),
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $customer1->id,
            'message_text'    => 'Bagus sekali. Saya berencana membangun company profile dengan sistem booking jadwal. Apakah paket standard sudah cukup?',
            'created_at'      => $now->copy()->subDays(7),
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $provider1->id,
            'message_text'    => 'Untuk company profile dan sistem booking sederhana, paket Standard sudah sangat mencukupi! Anda akan mendapatkan admin panel untuk mengelola jadwal booking juga.',
            'created_at'      => $now->copy()->subDays(7)->addMinutes(30),
        ]);

        $customOffer = \App\Models\CustomOffer::create([
            'conversation_id' => $conv->id,
            'provider_id'     => $provider1->id,
            'customer_id'     => $customer1->id,
            'service_id'      => $service1->id,
            'title'           => 'Website Company Profile Lengkap (Custom)',
            'description'     => 'Website 5 halaman, termasuk panel admin, sistem booking custom dengan konfirmasi WA.',
            'price'           => 1800000.00,
            'delivery_days'   => 10,
            'status'          => 'pending',
            'created_at'      => \Carbon\Carbon::create(2026, 5, 11, 15, 0, 0),
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $provider1->id,
            'custom_offer_id' => $customOffer->id,
            'message_text'    => null,
            'created_at'      => \Carbon\Carbon::create(2026, 5, 11, 15, 0, 0),
        ]);

        // Exact match with User Screenshot for live/recent feel
        // Message 5: Customer "ok" (11 May 15:37)
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $customer1->id,
            'message_text'    => 'ok',
            'created_at'      => \Carbon\Carbon::create(2026, 5, 11, 15, 37, 0),
        ]);

        // Message 6: Customer "jancok" (19 May 10:38)
        $msg6 = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $customer1->id,
            'message_text'    => 'jancok',
            'created_at'      => \Carbon\Carbon::create(2026, 5, 19, 10, 38, 0),
        ]);

        // Message 7: Provider Demo "Ip kon, gendeng a kon?" (19 May 10:39) - Merespon Message 6
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $provider1->id,
            'message_text'    => 'Ip kon, gendeng a kon?',
            'reply_to_id'     => $msg6->id,
            'created_at'      => \Carbon\Carbon::create(2026, 5, 19, 10, 39, 0),
        ]);

        // Message 8: Provider Demo "Cuplikan layar 2025-07-16 123030.png" Attachment (19 May 10:39)
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $provider1->id,
            'message_text'    => '',
            'attachment_path' => 'attachments/sample_screenshot.png',
            'attachment_name' => 'Cuplikan layar 2025-07-16 123030.png',
            'created_at'      => \Carbon\Carbon::create(2026, 5, 19, 10, 39, 5),
        ]);


        // ─── 6. Seeding CS Conversations & CS Messages ──────────────────────
        $this->command->info('Creating Support Tickets...');

        $csConv = CsConversation::create([
            'user_id'   => $customer1->id,
            'agent_id'  => $admin->id,
            'subject'   => 'Tanya Cara Tarik Saldo',
            'status'    => 'human',
            'created_at'=> $now->copy()->subDays(2),
        ]);

        CsMessage::create([
            'conversation_id' => $csConv->id,
            'sender_id'       => $customer1->id,
            'sender_type'     => 'user',
            'message'         => 'Halo CS, bagaimana cara menarik saldo di platform ini?',
            'created_at'      => $now->copy()->subDays(2),
        ]);

        CsMessage::create([
            'conversation_id' => $csConv->id,
            'sender_id'       => null,
            'sender_type'     => 'ai',
            'message'         => 'Halo! Jika Anda sebagai Penyedia Jasa (Provider), Anda dapat masuk ke halaman Dompet dan klik "Tarik Saldo". Saldo akan ditransfer ke rekening bank Anda setelah disetujui Admin.',
            'created_at'      => $now->copy()->subDays(2)->addSeconds(5),
        ]);

        CsMessage::create([
            'conversation_id' => $csConv->id,
            'sender_id'       => $customer1->id,
            'sender_type'     => 'user',
            'message'         => 'Baik, terima kasih infonya.',
            'created_at'      => $now->copy()->subDays(2)->addMinutes(10),
        ]);


        // ─── 7. Seeding Withdraw Requests ───────────────────────────────────
        $this->command->info('Creating Withdraw Requests...');

        // Approved withdraw
        WithdrawRequest::create([
            'provider_id'       => $provider1->id,
            'amount'            => 1000000.00,
            'method'            => 'bank_transfer',
            'account_details'   => [
                'bank_name'      => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'Provider Demo',
            ],
            'status'            => 'approved',
            'notes'             => 'Dana dikirim ke rekening BCA.',
            'processed_by'      => $admin->id,
            'processed_at'      => $now->copy()->subDays(2),
        ]);

        // Pending withdraw
        WithdrawRequest::create([
            'provider_id'       => $provider2->id,
            'amount'            => 500000.00,
            'method'            => 'gopay',
            'account_details'   => [
                'phone_number'   => '081234567890',
                'account_holder' => 'Sarah Designer',
            ],
            'status'            => 'pending',
        ]);


        // ─── 8. Seeding TopUps ──────────────────────────────────────────────
        $this->command->info('Creating Wallet TopUps...');

        TopUp::create([
            'user_id'          => $customer1->id,
            'order_id'         => 'TOPUP-' . $customer1->id . '-' . time(),
            'amount'           => 2000000.00,
            'status'           => 'success',
            'payment_type'     => 'bank_transfer',
            'paid_at'          => $now->copy()->subDays(10),
        ]);

        TopUp::create([
            'user_id'          => $customer2->id,
            'order_id'         => 'TOPUP-' . $customer2->id . '-' . (time() + 10),
            'amount'           => 1000000.00,
            'status'           => 'pending',
        ]);


        // ─── 9. Seeding Favorites ───────────────────────────────────────────
        $this->command->info('Creating Favorites...');

        Favorite::create(['user_id' => $customer1->id, 'service_id' => $service2->id, 'created_at' => $now]);
        Favorite::create(['user_id' => $customer2->id, 'service_id' => $service1->id, 'created_at' => $now]);


        // ─── 10. Seeding Notifications ──────────────────────────────────────
        $this->command->info('Creating App Notifications...');

        AppNotification::create([
            'user_id'   => $provider1->id,
            'type'      => 'new_order',
            'title'     => 'Pesanan Baru Diterima',
            'message'   => "Anda menerima pesanan baru #{$order1->order_number}.",
            'data'      => ['order_id' => $order1->id],
            'action_url'=> route('provider.orders.show', $order1->id),
            'read_at'   => $now,
        ]);

        AppNotification::create([
            'user_id'   => $customer1->id,
            'type'      => 'payment_success',
            'title'     => 'Pembayaran Berhasil',
            'message'   => "Pembayaran sukses untuk pesanan #{$order1->order_number}.",
            'data'      => ['order_id' => $order1->id],
            'action_url'=> route('customer.orders.show', $order1->id),
            'read_at'   => $now,
        ]);

        $this->command->info('Database Seeded Successfully! All tables fully populated with high-quality mock data.');
    }
}
