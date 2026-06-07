<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Order;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Truncate ────────────────────────────────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (['dispute_messages','disputes','custom_offers','cs_messages','cs_conversations','top_ups','withdraw_requests','favorites','notifications','messages','conversations','reviews','orders','service_images','service_packages','services','categories','transactions','user_profiles','users'] as $t) {
            DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('✓ Tabel dibersihkan.');

        // ── Admin ───────────────────────────────────────────────────────────────
        $admin = User::create(['name'=>'Super Admin','username'=>'superadmin','email'=>'admin@servemix.com','password'=>Hash::make('password'),'role'=>'admin','status'=>'active','email_verified_at'=>now()]);
        UserProfile::create(['user_id'=>$admin->id,'balance'=>0,'bio'=>'Administrator Sistem ServeMix']);

        // ── 10 Providers ────────────────────────────────────────────────────────
        $providerNames = ['Andi Pratama','Budi Santoso','Citra Dewi','Dian Rahmawati','Eka Saputra','Fajar Nugroho','Gita Purnama','Hendra Wijaya','Indah Sari','Joko Susilo'];
        $providerRoles = ['Web Developer','UI/UX Designer','Video Editor','Digital Marketer','Content Writer','Illustrator','SEO Specialist','Mobile Developer','Data Analyst','Voice Over'];
        $cities = ['Jakarta','Bandung','Surabaya','Yogyakarta','Semarang','Malang','Denpasar','Medan','Makassar','Solo'];
        $providers = [];

        for ($i = 0; $i < 10; $i++) {
            $p = User::create(['name'=>$providerNames[$i],'username'=>'provider'.($i+1),'email'=>"provider".($i+1)."@servemix.com",'password'=>Hash::make('password'),'role'=>'provider','status'=>'active','email_verified_at'=>now()]);
            UserProfile::create(['user_id'=>$p->id,'bio'=>"Saya {$providerNames[$i]}, seorang {$providerRoles[$i]} profesional dengan pengalaman ".rand(2,10)." tahun.",'country'=>'Indonesia','city'=>$cities[$i],'skills'=>[$providerRoles[$i],'Profesional','Kreatif'],'languages'=>['Bahasa Indonesia','English'],'experience_years'=>rand(2,10),'balance'=>rand(5,50)*100000,'total_earned'=>rand(10,100)*100000,'is_verified_provider'=>$i%2==0]);
            $providers[] = $p;
        }

        // ── 30 Customers ────────────────────────────────────────────────────────
        $customers = [];
        $custNames = ['Rizky Aditya','Sinta Maharani','Taufik Hidayat','Ulfa Ramadhani','Vina Oktaviani','Wahyu Setiawan','Xena Putri','Yusuf Maulana','Zahra Amelia','Arif Rahman','Bella Permata','Cahyo Wibowo','Desi Natalia','Edwin Putra','Fitri Handayani','Galih Prasetyo','Hani Safitri','Irwan Kurniawan','Jasmine Aulia','Kevin Mahardika','Lina Marlina','Mulyadi','Nadia Fitriani','Oscar Tanaka','Putri Rahayu','Qory Sandria','Rendi Firmansyah','Silvia Anggraeni','Toni Sucipto','Umar Hakim'];
        for ($i = 0; $i < 30; $i++) {
            $c = User::create(['name'=>$custNames[$i],'username'=>'customer'.($i+1),'email'=>"customer".($i+1)."@servemix.com",'password'=>Hash::make('password'),'role'=>'customer','status'=>'active','email_verified_at'=>now()]);
            UserProfile::create(['user_id'=>$c->id,'bio'=>'Pelanggan aktif di ServeMix.','balance'=>rand(1,10)*100000,'total_spent'=>rand(5,50)*100000]);
            $customers[] = $c;
        }
        $this->command->info('✓ Users: 1 Admin, 10 Provider, 30 Customer.');

        // ── Categories ──────────────────────────────────────────────────────────
        $cats = [
            ['Web Development','fas fa-code','Pembuatan website dan aplikasi web custom.'],
            ['Mobile Apps','fas fa-mobile-alt','Pengembangan aplikasi mobile Android & iOS.'],
            ['UI/UX Design','fas fa-paint-brush','Desain antarmuka dan pengalaman pengguna.'],
            ['Desain Grafis','fas fa-palette','Logo, banner, ilustrasi, dan branding.'],
            ['Video & Animasi','fas fa-video','Editing video, animasi 2D/3D, motion graphics.'],
            ['Digital Marketing','fas fa-bullhorn','SEO, iklan digital, dan media sosial.'],
            ['Penulisan & Terjemahan','fas fa-pen-nib','Artikel, copywriting, dan terjemahan.'],
            ['Audio & Musik','fas fa-music','Voice over, produksi musik, editing audio.'],
            ['Bisnis & Konsultasi','fas fa-chart-line','Konsultasi bisnis dan perencanaan keuangan.'],
            ['Data & Analitik','fas fa-database','Pengolahan data dan machine learning.'],
        ];
        $categories = [];
        foreach ($cats as $idx => $c) {
            $categories[] = Category::create(['name'=>$c[0],'slug'=>Str::slug($c[0]),'icon'=>$c[1],'description'=>$c[2],'sort_order'=>$idx+1]);
        }
        $this->command->info('✓ 10 Kategori.');

        // ── 60 Services ─────────────────────────────────────────────────────────
        $svcTemplates = [
            [0,'Jasa Pembuatan Website Company Profile',['WordPress','PHP','Website']],
            [0,'Bikin Website Toko Online E-Commerce',['E-Commerce','Laravel','Toko Online']],
            [0,'Landing Page Konversi Tinggi untuk Iklan',['Landing Page','HTML','CSS']],
            [0,'Jasa Pembuatan Sistem Informasi Laravel',['Laravel','PHP','Backend']],
            [1,'Pembuatan Aplikasi Android Native',['Android','Java','Kotlin']],
            [1,'Aplikasi Cross-Platform Flutter',['Flutter','Mobile','App']],
            [2,'Desain UI/UX Aplikasi Mobile di Figma',['UI/UX','Figma','Desain']],
            [2,'Redesign Website Menjadi Lebih Profesional',['Redesign','Website','UI']],
            [3,'Desain Logo Profesional untuk Startup',['Logo','Branding','Desain']],
            [3,'Bikin Feed Instagram Estetik 30 Hari',['Instagram','Feed','Sosmed']],
            [3,'Desain Kartu Nama dan Identitas Merek',['Branding','Kartu Nama','Desain']],
            [4,'Editing Video YouTube Kualitas Cinematic',['Video','YouTube','Premiere']],
            [4,'Video Animasi Explainer 2D Promosi Bisnis',['Animasi','After Effects','Promo']],
            [4,'Editing Reels & TikTok Profesional',['Reels','TikTok','Video']],
            [5,'Optimasi SEO Website Halaman 1 Google',['SEO','Google','Traffic']],
            [5,'Manajemen Iklan Facebook & Instagram Ads',['Ads','Facebook','Marketing']],
            [5,'Kelola Media Sosial Bisnis Anda',['Sosmed','Instagram','Marketing']],
            [6,'Penulisan Artikel SEO 1000 Kata',['Artikel','SEO','Menulis']],
            [6,'Copywriting Landing Page Hipnotik',['Copywriting','Sales','Tulisan']],
            [6,'Terjemahan Inggris-Indonesia Profesional',['Terjemahan','Bahasa','Translate']],
        ];

        $services = [];
        $words = ['Elegan','Premium','Cepat','Responsif','Modern','Terpercaya','Kreatif','Berkualitas','Terbaik','Handal','Profesional','Murah'];
        for ($i = 0; $i < 60; $i++) {
            $tpl = $svcTemplates[$i % count($svcTemplates)];
            $provider = $providers[array_rand($providers)];
            $category = $categories[$tpl[0]];
            $suffix = $words[array_rand($words)] . ' ' . ($i + 1);
            $title = $tpl[1] . ' ' . $suffix;

            $svc = Service::create([
                'provider_id'=>$provider->id,'category_id'=>$category->id,
                'title'=>$title,'slug'=>Str::slug($title),
                'description'=>"Layanan profesional: {$title}.\n\nKami menyediakan layanan berkualitas tinggi dengan harga terjangkau.\n\n**Kenapa memilih kami?**\n- Pengerjaan cepat dan tepat waktu\n- Kualitas premium terjamin\n- Revisi hingga puas\n- Komunikasi responsif 24/7",
                'tags'=>$tpl[2],'status'=>'active','avg_rating'=>0,'total_reviews'=>0,'total_orders'=>0,
                'created_at'=>Carbon::now()->subDays(rand(1,180)),
            ]);
            $services[] = $svc;

            $bp = rand(5,50)*10000;
            ServicePackage::create(['service_id'=>$svc->id,'package_type'=>'basic','name'=>'Paket Ekonomis','description'=>'Solusi hemat untuk kebutuhan dasar.','price'=>$bp,'delivery_days'=>rand(2,5),'revisions'=>1,'features'=>['Fitur Standar','Dukungan Email']]);
            ServicePackage::create(['service_id'=>$svc->id,'package_type'=>'standard','name'=>'Paket Profesional','description'=>'Paket paling laris dengan fitur lengkap.','price'=>$bp*2,'delivery_days'=>rand(4,7),'revisions'=>3,'features'=>['Fitur Standar','Dukungan Email','Fitur Premium','Prioritas']]);
            ServicePackage::create(['service_id'=>$svc->id,'package_type'=>'premium','name'=>'Paket Eksklusif VIP','description'=>'Layanan prioritas tertinggi.','price'=>$bp*4,'delivery_days'=>rand(7,14),'revisions'=>-1,'features'=>['Fitur Standar','Dukungan 24/7','Fitur Premium','Revisi Tak Terbatas','Source Code']]);
        }
        $this->command->info('✓ 60 Services & 180 Packages.');

        // ── 200 Orders + Reviews + Transactions ─────────────────────────────────
        $validStatuses = ['pending_payment','paid','in_progress','delivered','completed','cancelled'];
        $reviewComments = [
            'Hasil kerja sangat memuaskan! Sangat direkomendasikan.',
            'Pengerjaan cepat dan sesuai harapan. Terima kasih!',
            'Komunikasi sangat baik dan responsif. Pasti order lagi.',
            'Kualitasnya luar biasa, melebihi ekspektasi saya.',
            'Bagus dan profesional, saya sangat puas.',
            'Cukup baik, sesuai dengan harga yang ditawarkan.',
            'Revisi ditanggapi dengan cepat. Mantap!',
            'Hasilnya oke, tapi ada sedikit keterlambatan.',
            'Sangat puas! Pasti akan menggunakan jasa ini lagi.',
            'Pekerjaannya rapi dan detail. Top!',
        ];

        for ($i = 0; $i < 200; $i++) {
            $svc = $services[array_rand($services)];
            $cust = $customers[array_rand($customers)];
            $pkg = $svc->packages->random();
            
            // Bias 55% completed
            $status = rand(1,100) <= 55 ? 'completed' : $validStatuses[array_rand($validStatuses)];
            // Avoid customer ordering own service if they happen to be provider
            if ($cust->id === $svc->provider_id) continue;

            $dt = Carbon::now()->subDays(rand(1,150));

            $order = Order::create([
                'order_number'=>'ORD-'.strtoupper(Str::random(10)),
                'customer_id'=>$cust->id,'provider_id'=>$svc->provider_id,
                'service_id'=>$svc->id,'package_id'=>$pkg->id,
                'price'=>$pkg->price,'tax_fee'=>0,'discount'=>0,'grand_total'=>$pkg->price,
                'status'=>$status,
                'requirements'=>in_array($status,['pending_payment']) ? null : 'Kebutuhan project sudah saya jelaskan di chat.',
                'delivery_deadline'=>$dt->copy()->addDays($pkg->delivery_days),
                'delivered_at'=>in_array($status,['delivered','completed']) ? $dt->copy()->addDays($pkg->delivery_days-1) : null,
                'completed_at'=>$status==='completed' ? $dt->copy()->addDays($pkg->delivery_days) : null,
                'cancelled_at'=>$status==='cancelled' ? $dt->copy()->addDay() : null,
                'created_at'=>$dt,'updated_at'=>$dt,
            ]);

            if ($status === 'completed') {
                $svc->increment('total_orders');

                // 80% chance review
                if (rand(1,100) <= 80) {
                    $rating = rand(1,100) <= 85 ? rand(4,5) : rand(2,3);
                    Review::create([
                        'order_id'=>$order->id,'service_id'=>$svc->id,
                        'customer_id'=>$cust->id,'provider_id'=>$svc->provider_id,
                        'rating'=>$rating,
                        'comment'=>$reviewComments[array_rand($reviewComments)],
                        'provider_reply'=>rand(1,100)>50 ? 'Terima kasih atas kepercayaannya! Senang bekerja sama.' : null,
                        'is_visible'=>true,
                        'created_at'=>$order->completed_at?->addDays(rand(1,3)),
                    ]);
                    $svc->total_reviews += 1;
                    $allReviews = Review::where('service_id', $svc->id)->pluck('rating');
                    $svc->avg_rating = $allReviews->avg();
                    $svc->save();
                }

                // Transaction
                Transaction::create([
                    'user_id'=>$cust->id,'type'=>'payment','amount'=>-$order->grand_total,
                    'balance_before'=>0,'balance_after'=>0,
                    'description'=>"Pembayaran #{$order->order_number}",
                    'reference_id'=>$order->order_number,'created_at'=>$order->created_at,
                ]);
                Transaction::create([
                    'user_id'=>$svc->provider_id,'type'=>'earning','amount'=>$order->grand_total*0.9,
                    'balance_before'=>0,'balance_after'=>0,
                    'description'=>"Pendapatan #{$order->order_number}",
                    'reference_id'=>$order->order_number,'created_at'=>$order->completed_at,
                ]);
            }
        }
        $this->command->info('✓ ~200 Orders, Reviews & Transactions.');

        // ── 50 Conversations & Messages ─────────────────────────────────────────
        $chatMessages = ['Halo, saya tertarik dengan layanan Anda.','Terima kasih! Ada yang bisa saya bantu?','Bisa jelaskan lebih detail tentang paketnya?','Tentu, paket basic sudah termasuk...','Oke, saya mau order yang standard ya.','Baik, silakan lanjut order. Terima kasih!','Kapan bisa mulai dikerjakan?','Bisa mulai besok ya.','Sip, ditunggu hasilnya!','Siap, nanti saya kabari progressnya.'];

        for ($i = 0; $i < 50; $i++) {
            $cust = $customers[array_rand($customers)];
            $prov = $providers[array_rand($providers)];
            $dt = Carbon::now()->subDays(rand(1,90));

            $conv = Conversation::create([
                'customer_id'=>$cust->id,'provider_id'=>$prov->id,
                'service_id'=>$services[array_rand($services)]->id,
                'last_message_at'=>$dt,'created_at'=>$dt,'updated_at'=>$dt,
            ]);

            $msgCount = rand(3,8);
            for ($m = 0; $m < $msgCount; $m++) {
                $sender = $m % 2 === 0 ? $cust : $prov;
                Message::create([
                    'conversation_id'=>$conv->id,'sender_id'=>$sender->id,
                    'message_text'=>$chatMessages[array_rand($chatMessages)],
                    'read_at'=>now(),
                    'created_at'=>$dt->copy()->addMinutes($m*5),
                ]);
            }
            $conv->update(['last_message_at'=>$dt->copy()->addMinutes($msgCount*5)]);
        }
        $this->command->info('✓ 50 Percakapan.');

        // ── Favorites ───────────────────────────────────────────────────────────
        foreach ($customers as $cust) {
            $favIds = collect($services)->pluck('id')->random(min(rand(2,6), count($services)));
            foreach ($favIds as $sid) {
                Favorite::firstOrCreate(['user_id'=>$cust->id,'service_id'=>$sid]);
            }
        }
        $this->command->info('✓ Favorit.');

        $this->command->info('');
        $this->command->info('══════════════════════════════════════════════');
        $this->command->info('  ✅ SEEDING SELESAI! Database penuh data.');
        $this->command->info('  Login: admin@servemix.com / password');
        $this->command->info('  Login: provider1@servemix.com / password');
        $this->command->info('  Login: customer1@servemix.com / password');
        $this->command->info('══════════════════════════════════════════════');
    }
}
