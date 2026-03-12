<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@marketplace.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);
        UserProfile::create(['user_id' => $admin->id]);

        // ── Sample Provider ────────────────────────────────────────────────────
        $provider = User::create([
            'name'     => 'John Provider',
            'username' => 'john_provider',
            'email'    => 'provider@marketplace.com',
            'password' => Hash::make('password'),
            'role'     => 'provider',
            'status'   => 'active',
        ]);
        UserProfile::create([
            'user_id'          => $provider->id,
            'bio'              => 'Professional freelancer with 5 years of experience.',
            'skills'           => ['PHP', 'Laravel', 'Vue.js'],
            'experience_years' => 5,
            'hourly_rate'      => 25.00,
        ]);

        // ── Sample Customer ────────────────────────────────────────────────────
        $customer = User::create([
            'name'     => 'Jane Customer',
            'username' => 'jane_customer',
            'email'    => 'customer@marketplace.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);
        UserProfile::create([
            'user_id'     => $customer->id,
            'balance'     => 500000.00,
            'total_spent' => 0.00,
        ]);

        // ── Root Categories ────────────────────────────────────────────────────
        $design = Category::create([
            'name'        => 'Design & Creative',
            'slug'        => 'design-creative',
            'description' => 'Graphic design, UI/UX, branding and creative services.',
            'sort_order'  => 1,
            'is_active'   => true,
        ]);

        $development = Category::create([
            'name'        => 'Programming & Tech',
            'slug'        => 'programming-tech',
            'description' => 'Web development, mobile apps, software engineering.',
            'sort_order'  => 2,
            'is_active'   => true,
        ]);

        $marketing = Category::create([
            'name'        => 'Digital Marketing',
            'slug'        => 'digital-marketing',
            'description' => 'SEO, social media, content marketing, and advertising.',
            'sort_order'  => 3,
            'is_active'   => true,
        ]);

        $writing = Category::create([
            'name'        => 'Writing & Translation',
            'slug'        => 'writing-translation',
            'description' => 'Copywriting, translation, proofreading, and content creation.',
            'sort_order'  => 4,
            'is_active'   => true,
        ]);

        // ── Sub-categories: Design ─────────────────────────────────────────────
        foreach ([
            ['Logo Design',        'logo-design',        1],
            ['UI/UX Design',       'ui-ux-design',       2],
            ['Illustration',       'illustration',       3],
            ['Video & Animation',  'video-animation',    4],
        ] as [$name, $slug, $order]) {
            Category::create([
                'parent_id'  => $design->id,
                'name'       => $name,
                'slug'       => $slug,
                'sort_order' => $order,
                'is_active'  => true,
            ]);
        }

        // ── Sub-categories: Development ────────────────────────────────────────
        foreach ([
            ['Web Development',    'web-development',    1],
            ['Mobile Apps',        'mobile-apps',        2],
            ['WordPress',          'wordpress',          3],
            ['Database Design',    'database-design',    4],
        ] as [$name, $slug, $order]) {
            Category::create([
                'parent_id'  => $development->id,
                'name'       => $name,
                'slug'       => $slug,
                'sort_order' => $order,
                'is_active'  => true,
            ]);
        }

        // ── Sub-categories: Marketing ──────────────────────────────────────────
        foreach ([
            ['SEO',                'seo',                1],
            ['Social Media',       'social-media',       2],
            ['Email Marketing',    'email-marketing',    3],
        ] as [$name, $slug, $order]) {
            Category::create([
                'parent_id'  => $marketing->id,
                'name'       => $name,
                'slug'       => $slug,
                'sort_order' => $order,
                'is_active'  => true,
            ]);
        }

        // ── Sub-categories: Writing ────────────────────────────────────────────
        foreach ([
            ['Copywriting',        'copywriting',        1],
            ['Translation',        'translation',        2],
            ['Proofreading',       'proofreading',       3],
        ] as [$name, $slug, $order]) {
            Category::create([
                'parent_id'  => $writing->id,
                'name'       => $name,
                'slug'       => $slug,
                'sort_order' => $order,
                'is_active'  => true,
            ]);
        }
    }
}
