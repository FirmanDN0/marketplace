<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ─── 1. Pemrograman & Teknologi ──────────────────────────────────────
            [
                'name'        => 'Pemrograman & Teknologi',
                'icon'        => 'fa-code',
                'description' => 'Layanan pengembangan software, web, mobile, dan infrastruktur teknologi.',
                'sort_order'  => 1,
                'children'    => [
                    ['name' => 'Pengembangan Web',        'icon' => 'fa-globe',           'description' => 'Website, landing page, CMS, dan portal web.'],
                    ['name' => 'Aplikasi Mobile',         'icon' => 'fa-mobile-alt',       'description' => 'Aplikasi Android, iOS, dan cross-platform.'],
                    ['name' => 'Backend & API',            'icon' => 'fa-server',           'description' => 'REST API, microservices, dan integrasi sistem.'],
                    ['name' => 'Frontend & UI',            'icon' => 'fa-laptop-code',      'description' => 'React, Vue, Angular, dan pengembangan antarmuka.'],
                    ['name' => 'Database & Cloud',         'icon' => 'fa-database',         'description' => 'Desain database, migrasi, dan cloud deployment.'],
                    ['name' => 'DevOps & Server',          'icon' => 'fa-cogs',             'description' => 'CI/CD, Docker, Kubernetes, dan manajemen server.'],
                    ['name' => 'Keamanan Siber',           'icon' => 'fa-shield-alt',       'description' => 'Penetration testing, audit keamanan, dan konsultasi.'],
                    ['name' => 'Kecerdasan Buatan & ML',  'icon' => 'fa-brain',            'description' => 'Machine learning, deep learning, dan AI automation.'],
                    ['name' => 'Scraping & Otomasi',      'icon' => 'fa-robot',            'description' => 'Web scraping, bot Telegram/Discord, dan otomasi proses.'],
                    ['name' => 'Testing & QA',             'icon' => 'fa-bug',              'description' => 'Unit testing, QA manual, dan automated testing.'],
                ],
            ],

            // ─── 2. Desain & Kreatif ────────────────────────────────────────────
            [
                'name'        => 'Desain & Kreatif',
                'icon'        => 'fa-palette',
                'description' => 'Desain grafis, branding, ilustrasi, dan konten visual.',
                'sort_order'  => 2,
                'children'    => [
                    ['name' => 'UI/UX Design',            'icon' => 'fa-object-group',     'description' => 'Desain antarmuka dan pengalaman pengguna.'],
                    ['name' => 'Logo & Branding',          'icon' => 'fa-trademark',        'description' => 'Logo, identitas merek, dan panduan brand.'],
                    ['name' => 'Desain Grafis',            'icon' => 'fa-vector-square',    'description' => 'Poster, banner, infografis, dan materi cetak.'],
                    ['name' => 'Ilustrasi & Seni Digital', 'icon' => 'fa-paint-brush',      'description' => 'Ilustrasi karakter, komik, dan seni digital.'],
                    ['name' => 'Desain Presentasi',        'icon' => 'fa-file-powerpoint',  'description' => 'Pitch deck, PowerPoint, dan Keynote profesional.'],
                    ['name' => 'Desain Media Sosial',      'icon' => 'fa-share-alt',        'description' => 'Template feed Instagram, stories, dan thumbnail YouTube.'],
                    ['name' => 'Desain Produk & Kemasan',  'icon' => 'fa-box-open',         'description' => 'Desain packaging, label produk, dan mockup.'],
                    ['name' => 'Desain Kartu Nama & Cetak','icon' => 'fa-id-card',          'description' => 'Kartu nama, brosur, flyer, dan katalog cetak.'],
                    ['name' => 'Desain 3D & Modeling',     'icon' => 'fa-cube',             'description' => 'Model 3D, rendering, dan visualisasi arsitektur.'],
                ],
            ],

            // ─── 3. Video & Animasi ─────────────────────────────────────────────
            [
                'name'        => 'Video & Animasi',
                'icon'        => 'fa-film',
                'description' => 'Produksi video, editing, animasi, dan konten visual gerak.',
                'sort_order'  => 3,
                'children'    => [
                    ['name' => 'Editing Video',            'icon' => 'fa-cut',              'description' => 'Editing video YouTube, TikTok, dan Reels.'],
                    ['name' => 'Animasi 2D',               'icon' => 'fa-magic',            'description' => 'Animasi explainer, karakter, dan motion 2D.'],
                    ['name' => 'Animasi 3D',               'icon' => 'fa-film',             'description' => 'Animasi 3D, VFX, dan visualisasi produk.'],
                    ['name' => 'Video Promosi & Iklan',    'icon' => 'fa-bullhorn',         'description' => 'Video iklan, promo, dan company profile.'],
                    ['name' => 'Motion Graphics',          'icon' => 'fa-play-circle',      'description' => 'Intro/outro channel, lower third, dan teks animasi.'],
                    ['name' => 'Subtitle & Transkripsi',   'icon' => 'fa-closed-captioning','description' => 'Penambahan subtitle, terjemahan, dan transkripsi video.'],
                    ['name' => 'Thumbnail & Miniatur',     'icon' => 'fa-image',            'description' => 'Desain thumbnail menarik untuk YouTube dan media sosial.'],
                ],
            ],

            // ─── 4. Audio & Musik ───────────────────────────────────────────────
            [
                'name'        => 'Audio & Musik',
                'icon'        => 'fa-music',
                'description' => 'Produksi musik, voice over, podcast, dan audio branding.',
                'sort_order'  => 4,
                'children'    => [
                    ['name' => 'Voice Over',               'icon' => 'fa-microphone',       'description' => 'Dubbing, narasi, dan suara karakter profesional.'],
                    ['name' => 'Produksi Musik',           'icon' => 'fa-headphones',       'description' => 'Komposisi musik, beat, dan aransemen lagu.'],
                    ['name' => 'Mixing & Mastering',       'icon' => 'fa-sliders-h',        'description' => 'Mixing audio profesional dan mastering lagu.'],
                    ['name' => 'Jingle & Iklan Radio',     'icon' => 'fa-broadcast-tower',  'description' => 'Pembuatan jingle merek dan iklan audio.'],
                    ['name' => 'Podcast Editing',          'icon' => 'fa-podcast',          'description' => 'Editing podcast, penghilangan noise, dan mastering.'],
                    ['name' => 'Sound Design',             'icon' => 'fa-volume-up',        'description' => 'Efek suara, ambient, dan audio untuk game/video.'],
                ],
            ],

            // ─── 5. Penulisan & Konten ──────────────────────────────────────────
            [
                'name'        => 'Penulisan & Konten',
                'icon'        => 'fa-pen-nib',
                'description' => 'Copywriting, artikel, SEO content, dan layanan penulisan kreatif.',
                'sort_order'  => 5,
                'children'    => [
                    ['name' => 'Artikel & Blog',           'icon' => 'fa-newspaper',        'description' => 'Penulisan artikel SEO-friendly, blog, dan editorial.'],
                    ['name' => 'Copywriting & Iklan',      'icon' => 'fa-ad',               'description' => 'Teks iklan, landing page, dan email marketing.'],
                    ['name' => 'Penulisan Teknis',         'icon' => 'fa-file-alt',         'description' => 'Dokumentasi API, SOP, manual pengguna, dan whitepaper.'],
                    ['name' => 'Penulisan Kreatif',        'icon' => 'fa-feather-alt',      'description' => 'Cerpen, novel, puisi, dan naskah drama.'],
                    ['name' => 'Terjemahan',               'icon' => 'fa-language',         'description' => 'Terjemahan dokumen, subtitle, dan konten web.'],
                    ['name' => 'Proofreading & Editing',   'icon' => 'fa-spell-check',      'description' => 'Koreksi ejaan, tata bahasa, dan penyuntingan naskah.'],
                    ['name' => 'Konten Media Sosial',      'icon' => 'fa-hashtag',          'description' => 'Caption Instagram, thread Twitter, dan konten TikTok.'],
                    ['name' => 'CV & Surat Lamaran',       'icon' => 'fa-file-user',        'description' => 'Pembuatan CV profesional, portofolio, dan cover letter.'],
                ],
            ],

            // ─── 6. Pemasaran Digital ───────────────────────────────────────────
            [
                'name'        => 'Pemasaran Digital',
                'icon'        => 'fa-chart-line',
                'description' => 'SEO, iklan berbayar, media sosial, dan strategi pertumbuhan digital.',
                'sort_order'  => 6,
                'children'    => [
                    ['name' => 'SEO & Optimasi Website',   'icon' => 'fa-search',           'description' => 'On-page SEO, backlink building, dan audit teknis.'],
                    ['name' => 'Iklan Google & Meta Ads',  'icon' => 'fa-bullseye',         'description' => 'Manajemen Google Ads, Facebook Ads, dan TikTok Ads.'],
                    ['name' => 'Manajemen Media Sosial',   'icon' => 'fa-thumbs-up',        'description' => 'Pengelolaan akun Instagram, TikTok, dan LinkedIn.'],
                    ['name' => 'Email Marketing',          'icon' => 'fa-envelope-open-text','description' => 'Desain newsletter, automasi email, dan copywriting.'],
                    ['name' => 'Influencer & KOL',         'icon' => 'fa-user-tie',         'description' => 'Strategi kolaborasi influencer dan Key Opinion Leader.'],
                    ['name' => 'Analitik & Laporan',       'icon' => 'fa-chart-bar',        'description' => 'Google Analytics, data visualisasi, dan laporan performa.'],
                    ['name' => 'Marketplace & Tokopedia',  'icon' => 'fa-store',            'description' => 'Optimasi toko Tokopedia, Shopee, dan Lazada.'],
                ],
            ],

            // ─── 7. Bisnis & Konsultasi ─────────────────────────────────────────
            [
                'name'        => 'Bisnis & Konsultasi',
                'icon'        => 'fa-briefcase',
                'description' => 'Konsultasi bisnis, keuangan, hukum, dan pengembangan strategi.',
                'sort_order'  => 7,
                'children'    => [
                    ['name' => 'Konsultasi Bisnis',        'icon' => 'fa-handshake',        'description' => 'Strategi bisnis, model bisnis, dan analisis pasar.'],
                    ['name' => 'Akuntansi & Keuangan',     'icon' => 'fa-calculator',       'description' => 'Pembukuan, laporan keuangan, dan perencanaan pajak.'],
                    ['name' => 'Konsultasi Legal',         'icon' => 'fa-balance-scale',    'description' => 'Kontrak, perizinan usaha, dan konsultasi hukum bisnis.'],
                    ['name' => 'Riset & Analisis Pasar',   'icon' => 'fa-search-dollar',    'description' => 'Survei, analisis kompetitor, dan studi kelayakan.'],
                    ['name' => 'HR & Rekrutmen',           'icon' => 'fa-users',            'description' => 'Rekrutmen, onboarding, dan pengembangan SDM.'],
                    ['name' => 'Perencanaan Proyek',       'icon' => 'fa-project-diagram',  'description' => 'Manajemen proyek, Scrum, dan Agile coaching.'],
                ],
            ],

            // ─── 8. Pendidikan & Pelatihan ──────────────────────────────────────
            [
                'name'        => 'Pendidikan & Pelatihan',
                'icon'        => 'fa-graduation-cap',
                'description' => 'Les privat, kursus online, dan pengembangan materi pembelajaran.',
                'sort_order'  => 8,
                'children'    => [
                    ['name' => 'Les Privat & Tutoring',    'icon' => 'fa-chalkboard-teacher','description' => 'Bimbingan belajar, les matematika, sains, dan bahasa.'],
                    ['name' => 'Kursus Online',             'icon' => 'fa-laptop',           'description' => 'Pembuatan kursus e-learning dan konten LMS.'],
                    ['name' => 'Pelatihan Bahasa',          'icon' => 'fa-comments',         'description' => 'Bahasa Inggris, Mandarin, Jepang, dan bahasa asing lain.'],
                    ['name' => 'Bimbingan Akademik',        'icon' => 'fa-book-open',        'description' => 'Bimbingan skripsi, tesis, dan karya ilmiah.'],
                    ['name' => 'Pelatihan Korporat',        'icon' => 'fa-building',         'description' => 'Workshop, training SDM, dan soft skill perusahaan.'],
                ],
            ],

            // ─── 9. Gaya Hidup & Kreatif Lainnya ───────────────────────────────
            [
                'name'        => 'Gaya Hidup & Lainnya',
                'icon'        => 'fa-heart',
                'description' => 'Layanan unik, gaya hidup, dan kategori lain yang tidak masuk di atas.',
                'sort_order'  => 9,
                'children'    => [
                    ['name' => 'Fotografi',                'icon' => 'fa-camera',           'description' => 'Editing foto, retouching, dan jasa fotografi.'],
                    ['name' => 'Konsultasi Gizi & Fitness', 'icon' => 'fa-dumbbell',        'description' => 'Program diet, meal plan, dan pelatihan kebugaran.'],
                    ['name' => 'Astrologi & Spiritual',    'icon' => 'fa-star-and-crescent','description' => 'Tarot, numerologi, dan konseling spiritual.'],
                    ['name' => 'Kerajinan & DIY',          'icon' => 'fa-tools',            'description' => 'Tutorial kerajinan tangan, merajut, dan keterampilan praktis.'],
                    ['name' => 'Layanan Virtual Lainnya',  'icon' => 'fa-laptop-house',     'description' => 'Asisten virtual, riset, dan layanan administrasi jarak jauh.'],
                ],
            ],
        ];

        $sortOrder = 1;
        foreach ($categories as $catData) {
            $parent = Category::create([
                'parent_id'   => null,
                'name'        => $catData['name'],
                'slug'        => Str::slug($catData['name']),
                'description' => $catData['description'],
                'icon'        => $catData['icon'],
                'sort_order'  => $catData['sort_order'],
                'is_active'   => true,
            ]);

            $childSort = 1;
            foreach ($catData['children'] as $child) {
                Category::create([
                    'parent_id'   => $parent->id,
                    'name'        => $child['name'],
                    'slug'        => Str::slug($child['name']) . '-' . Str::slug($catData['name']),
                    'description' => $child['description'],
                    'icon'        => $child['icon'],
                    'sort_order'  => $childSort++,
                    'is_active'   => true,
                ]);
            }
        }
    }
}
