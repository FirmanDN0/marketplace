<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Order;
use App\Models\Review;
use App\Models\UserProfile;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // ── Categories ─────────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development', 'description' => 'Custom websites, web apps, and online platforms'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development', 'description' => 'iOS and Android mobile applications'],
            ['name' => 'Graphic Design', 'slug' => 'graphic-design', 'description' => 'Logos, branding, and visual design services'],
            ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'description' => 'SEO, social media, and online marketing'],
            ['name' => 'Writing & Translation', 'slug' => 'writing-translation', 'description' => 'Content writing and language translation'],
            ['name' => 'Video & Animation', 'slug' => 'video-animation', 'description' => 'Video editing, animation, and motion graphics'],
        ];

        // ── Providers ──────────────────────────────────────────────────────────
        $providers = [
            [
                'name'     => 'John Developer',
                'username' => 'johndev',
                'email'    => 'john@devstudio.com',
                'password' => Hash::make('password'),
                'role'     => 'provider',
                'status'   => 'active',
                'provider_setup_step' => 3,
            ],
            [
                'name'     => 'Sarah Designer',
                'username' => 'sarahdesign',
                'email'    => 'sarah@designco.com',
                'password' => Hash::make('password'),
                'role'     => 'provider',
                'status'   => 'active',
                'provider_setup_step' => 3,
            ],
            [
                'name'     => 'Mike Marketing',
                'username' => 'mikemarketing',
                'email'    => 'mike@marketingpro.com',
                'password' => Hash::make('password'),
                'role'     => 'provider',
                'status'   => 'active',
                'provider_setup_step' => 3,
            ],
        ];

        foreach ($providers as $provider) {
            User::firstOrCreate(
                ['email' => $provider['email']],
                $provider
            );
        }

        // ── Customers ──────────────────────────────────────────────────────────
        $customers = [
            [
                'name'     => 'Alice Johnson',
                'username' => 'alicej',
                'email'    => 'alice@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => 'active',
            ],
            [
                'name'     => 'Bob Smith',
                'username' => 'bobsmith',
                'email'    => 'bob@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => 'active',
            ],
            [
                'name'     => 'Carol Williams',
                'username' => 'carolw',
                'email'    => 'carol@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }

        // ── Services ───────────────────────────────────────────────────────────
        $services = [
            [
                'provider_id' => 2, // John Developer
                'category_id' => 1, // Web Development
                'title'       => 'Custom E-commerce Website',
                'slug'        => 'custom-ecommerce-website',
                'description' => 'Professional e-commerce website with payment integration, inventory management, and responsive design.',
                'tags'        => ['ecommerce', 'laravel', 'payment', 'responsive'],
                'status'      => 'active',
                'avg_rating'  => 4.8,
                'total_reviews' => 12,
                'total_orders' => 8,
            ],
            [
                'provider_id' => 2, // John Developer
                'category_id' => 1, // Web Development
                'title'       => 'React Single Page Application',
                'slug'        => 'react-single-page-application',
                'description' => 'Modern React SPA with Redux state management, API integration, and optimized performance.',
                'tags'        => ['react', 'redux', 'spa', 'api'],
                'status'      => 'active',
                'avg_rating'  => 4.9,
                'total_reviews' => 8,
                'total_orders' => 5,
            ],
            [
                'provider_id' => 3, // Sarah Designer
                'category_id' => 3, // Graphic Design
                'title'       => 'Brand Identity Package',
                'slug'        => 'brand-identity-package',
                'description' => 'Complete brand identity including logo, color palette, typography, and brand guidelines.',
                'tags'        => ['logo', 'branding', 'identity', 'guidelines'],
                'status'      => 'active',
                'avg_rating'  => 4.7,
                'total_reviews' => 15,
                'total_orders' => 10,
            ],
            [
                'provider_id' => 4, // Mike Marketing
                'category_id' => 4, // Digital Marketing
                'title'       => 'SEO Optimization Service',
                'slug'        => 'seo-optimization-service',
                'description' => 'Comprehensive SEO audit and optimization to improve search engine rankings and organic traffic.',
                'tags'        => ['seo', 'optimization', 'ranking', 'traffic'],
                'status'      => 'active',
                'avg_rating'  => 4.6,
                'total_reviews' => 20,
                'total_orders' => 15,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }

        // ── Service Packages ───────────────────────────────────────────────────
        $packages = [
            // E-commerce Website packages
            [
                'service_id'    => 1,
                'package_type'  => 'basic',
                'name'          => 'Basic Package',
                'description'   => 'Simple e-commerce site with up to 10 products',
                'price'         => 500.00,
                'delivery_days' => 7,
                'revisions'     => 2,
                'features'      => ['Responsive design', 'Payment integration', 'Basic admin panel'],
                'is_active'     => true,
            ],
            [
                'service_id'    => 1,
                'package_type'  => 'standard',
                'name'          => 'Standard Package',
                'description'   => 'Full-featured e-commerce with unlimited products',
                'price'         => 1200.00,
                'delivery_days' => 14,
                'revisions'     => 3,
                'features'      => ['Everything in Basic', 'Inventory management', 'Order tracking', 'Customer accounts'],
                'is_active'     => true,
            ],
            [
                'service_id'    => 1,
                'package_type'  => 'premium',
                'name'          => 'Premium Package',
                'description'   => 'Advanced e-commerce with custom features',
                'price'         => 2500.00,
                'delivery_days' => 21,
                'revisions'     => 5,
                'features'      => ['Everything in Standard', 'Custom integrations', 'Advanced analytics', 'Multi-language support'],
                'is_active'     => true,
            ],
            // React SPA packages
            [
                'service_id'    => 2,
                'package_type'  => 'basic',
                'name'          => 'Basic SPA',
                'description'   => 'Simple React application with basic features',
                'price'         => 800.00,
                'delivery_days' => 10,
                'revisions'     => 2,
                'features'      => ['React components', 'Basic routing', 'API integration'],
                'is_active'     => true,
            ],
            [
                'service_id'    => 2,
                'package_type'  => 'standard',
                'name'          => 'Full-Featured SPA',
                'description'   => 'Complete React application with advanced features',
                'price'         => 1800.00,
                'delivery_days' => 18,
                'revisions'     => 4,
                'features'      => ['Everything in Basic', 'Redux state management', 'Authentication', 'Dashboard'],
                'is_active'     => true,
            ],
            // Brand Identity packages
            [
                'service_id'    => 3,
                'package_type'  => 'basic',
                'name'          => 'Logo Only',
                'description'   => 'Professional logo design with 2 revisions',
                'price'         => 150.00,
                'delivery_days' => 5,
                'revisions'     => 2,
                'features'      => ['Logo design', '2 revisions', 'High-resolution files'],
                'is_active'     => true,
            ],
            [
                'service_id'    => 3,
                'package_type'  => 'standard',
                'name'          => 'Brand Package',
                'description'   => 'Complete brand identity package',
                'price'         => 500.00,
                'delivery_days' => 10,
                'revisions'     => 3,
                'features'      => ['Logo design', 'Color palette', 'Typography', 'Brand guidelines'],
                'is_active'     => true,
            ],
            // SEO packages
            [
                'service_id'    => 4,
                'package_type'  => 'basic',
                'name'          => 'SEO Audit',
                'description'   => 'Comprehensive SEO audit and recommendations',
                'price'         => 300.00,
                'delivery_days' => 7,
                'revisions'     => 1,
                'features'      => ['Site audit', 'Keyword research', 'Competitor analysis', 'Action plan'],
                'is_active'     => true,
            ],
            [
                'service_id'    => 4,
                'package_type'  => 'standard',
                'name'          => 'SEO Optimization',
                'description'   => 'Complete SEO optimization implementation',
                'price'         => 800.00,
                'delivery_days' => 21,
                'revisions'     => 2,
                'features'      => ['Everything in Audit', 'On-page optimization', 'Content optimization', 'Technical fixes'],
                'is_active'     => true,
            ],
        ];

        foreach ($packages as $package) {
            ServicePackage::firstOrCreate(
                ['service_id' => $package['service_id'], 'package_type' => $package['package_type']],
                $package
            );
        }

        // ── Orders ─────────────────────────────────────────────────────────────
        $orders = [
            [
                'order_number'     => 'ORD-001',
                'customer_id'      => 5, // Alice Johnson
                'provider_id'      => 2, // John Developer
                'service_id'       => 1, // E-commerce Website
                'package_id'       => 1, // Basic Package
                'price'            => 500.00,
                'platform_fee'     => 50.00,
                'provider_earning' => 450.00,
                'status'           => 'completed',
                'delivery_deadline' => now()->addDays(7),
                'notes'            => 'Need the site to be mobile-friendly',
                'delivered_at'     => now()->subDays(2),
                'completed_at'     => now()->subDays(1),
            ],
            [
                'order_number'     => 'ORD-002',
                'customer_id'      => 6, // Bob Smith
                'provider_id'      => 3, // Sarah Designer
                'service_id'       => 3, // Brand Identity
                'package_id'       => 7, // Brand Package
                'price'            => 500.00,
                'platform_fee'     => 50.00,
                'provider_earning' => 450.00,
                'status'           => 'in_progress',
                'delivery_deadline' => now()->addDays(5),
                'notes'            => 'Prefer modern and clean design',
            ],
            [
                'order_number'     => 'ORD-003',
                'customer_id'      => 7, // Carol Williams
                'provider_id'      => 4, // Mike Marketing
                'service_id'       => 4, // SEO Service
                'package_id'       => 9, // SEO Optimization
                'price'            => 800.00,
                'platform_fee'     => 80.00,
                'provider_earning' => 720.00,
                'status'           => 'paid',
                'delivery_deadline' => now()->addDays(21),
                'notes'            => 'Focus on local SEO for our business',
            ],
            [
                'order_number'     => 'ORD-004',
                'customer_id'      => 5, // Alice Johnson
                'provider_id'      => 2, // John Developer
                'service_id'       => 2, // React SPA
                'package_id'       => 4, // Basic SPA
                'price'            => 800.00,
                'platform_fee'     => 80.00,
                'provider_earning' => 720.00,
                'status'           => 'delivered',
                'delivery_deadline' => now()->addDays(10),
                'notes'            => 'Need API integration with our backend',
                'delivered_at'     => now(),
            ],
        ];

        foreach ($orders as $order) {
            Order::firstOrCreate(
                ['order_number' => $order['order_number']],
                $order
            );
        }

        // ── Reviews ────────────────────────────────────────────────────────────
        $reviews = [
            [
                'order_id'     => 1, // ORD-001
                'customer_id'  => 5, // Alice Johnson
                'provider_id'  => 2, // John Developer
                'service_id'   => 1, // E-commerce Website
                'rating'       => 5,
                'comment'      => 'Excellent work! The website looks great and works perfectly. Highly recommend!',
                'is_visible'   => true,
            ],
            [
                'order_id'     => 1, // ORD-001
                'customer_id'  => 5, // Alice Johnson
                'provider_id'  => 2, // John Developer
                'service_id'   => 1, // E-commerce Website
                'rating'       => 4,
                'comment'      => 'Good job on the e-commerce site. Everything works as expected.',
                'is_visible'   => true,
            ],
            [
                'order_id'     => 1, // ORD-001
                'customer_id'  => 5, // Alice Johnson
                'provider_id'  => 2, // John Developer
                'service_id'   => 1, // E-commerce Website
                'rating'       => 5,
                'comment'      => 'Very professional and timely delivery. Will definitely order again.',
                'is_visible'   => true,
            ],
            [
                'order_id'     => 2, // ORD-002
                'customer_id'  => 6, // Bob Smith
                'provider_id'  => 3, // Sarah Designer
                'service_id'   => 3, // Brand Identity
                'rating'       => 5,
                'comment'      => 'Amazing brand identity work! Love the logo and color scheme.',
                'is_visible'   => true,
            ],
            [
                'order_id'     => 4, // ORD-004
                'customer_id'  => 5, // Alice Johnson
                'provider_id'  => 2, // John Developer
                'service_id'   => 2, // React SPA
                'rating'       => 4,
                'comment'      => 'Great React application. Clean code and good performance.',
                'provider_reply' => 'Thank you for the feedback! Glad you liked the application.',
                'replied_at'   => now()->subHours(2),
                'is_visible'   => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::firstOrCreate(
                ['order_id' => $review['order_id'], 'customer_id' => $review['customer_id']],
                $review
            );
        }

        // ── User Profiles ──────────────────────────────────────────────────────
        $profiles = [
            [
                'user_id'         => 2, // John Developer
                'bio'             => 'Full-stack web developer with 5+ years of experience in Laravel, React, and modern web technologies.',
                'phone'           => '+1-555-0101',
                'country'         => 'United States',
                'city'            => 'San Francisco',
                'website'         => 'https://johndev.com',
                'skills'          => ['Laravel', 'React', 'Vue.js', 'Node.js', 'MySQL', 'AWS'],
                'languages'       => ['English', 'Spanish'],
                'experience_years' => 5,
                'hourly_rate'     => 75.00,
                'balance'         => 1250.00,
                'pending_balance' => 450.00,
                'total_earned'    => 8500.00,
                'total_spent'     => 0.00,
            ],
            [
                'user_id'         => 3, // Sarah Designer
                'bio'             => 'Creative graphic designer specializing in brand identity and digital marketing materials.',
                'phone'           => '+1-555-0102',
                'country'         => 'United States',
                'city'            => 'New York',
                'website'         => 'https://sarahdesign.com',
                'skills'          => ['Adobe Creative Suite', 'Brand Identity', 'Logo Design', 'UI/UX'],
                'languages'       => ['English'],
                'experience_years' => 4,
                'hourly_rate'     => 60.00,
                'balance'         => 800.00,
                'pending_balance' => 0.00,
                'total_earned'    => 4200.00,
                'total_spent'     => 0.00,
            ],
            [
                'user_id'         => 4, // Mike Marketing
                'bio'             => 'Digital marketing expert helping businesses grow their online presence through SEO and content marketing.',
                'phone'           => '+1-555-0103',
                'country'         => 'United States',
                'city'            => 'Chicago',
                'website'         => 'https://mikemarketing.com',
                'skills'          => ['SEO', 'Google Analytics', 'Content Marketing', 'PPC', 'Social Media'],
                'languages'       => ['English', 'French'],
                'experience_years' => 6,
                'hourly_rate'     => 65.00,
                'balance'         => 950.00,
                'pending_balance' => 720.00,
                'total_earned'    => 6800.00,
                'total_spent'     => 0.00,
            ],
            [
                'user_id'         => 5, // Alice Johnson
                'bio'             => 'Small business owner looking for quality digital services.',
                'phone'           => '+1-555-0201',
                'country'         => 'United States',
                'city'            => 'Los Angeles',
                'skills'          => [],
                'languages'       => ['English'],
                'experience_years' => 0,
                'hourly_rate'     => 0.00,
                'balance'         => 500.00,
                'pending_balance' => 0.00,
                'total_earned'    => 0.00,
                'total_spent'     => 1300.00,
            ],
            [
                'user_id'         => 6, // Bob Smith
                'bio'             => 'Entrepreneur building multiple online businesses.',
                'phone'           => '+1-555-0202',
                'country'         => 'United States',
                'city'            => 'Austin',
                'skills'          => [],
                'languages'       => ['English'],
                'experience_years' => 0,
                'hourly_rate'     => 0.00,
                'balance'         => 300.00,
                'pending_balance' => 0.00,
                'total_earned'    => 0.00,
                'total_spent'     => 500.00,
            ],
            [
                'user_id'         => 7, // Carol Williams
                'bio'             => 'Marketing manager for a local retail company.',
                'phone'           => '+1-555-0203',
                'country'         => 'United States',
                'city'            => 'Seattle',
                'skills'          => [],
                'languages'       => ['English'],
                'experience_years' => 0,
                'hourly_rate'     => 0.00,
                'balance'         => 200.00,
                'pending_balance' => 0.00,
                'total_earned'    => 0.00,
                'total_spent'     => 800.00,
            ],
        ];

        foreach ($profiles as $profile) {
            UserProfile::firstOrCreate(
                ['user_id' => $profile['user_id']],
                $profile
            );
        }

        // ── Payments ───────────────────────────────────────────────────────────
        $payments = [
            [
                'order_id'          => 1, // ORD-001
                'user_id'           => 5, // Alice Johnson
                'amount'            => 500.00,
                'currency'          => 'USD',
                'payment_method'    => 'stripe',
                'status'            => 'success',
                'gateway_transaction_id' => 'txn_123456789',
                'paid_at'           => now()->subDays(10),
            ],
            [
                'order_id'          => 2, // ORD-002
                'user_id'           => 6, // Bob Smith
                'amount'            => 500.00,
                'currency'          => 'USD',
                'payment_method'    => 'midtrans',
                'status'            => 'success',
                'gateway_transaction_id' => 'txn_987654321',
                'paid_at'           => now()->subDays(5),
            ],
            [
                'order_id'          => 3, // ORD-003
                'user_id'           => 7, // Carol Williams
                'amount'            => 800.00,
                'currency'          => 'USD',
                'payment_method'    => 'manual',
                'status'            => 'success',
                'gateway_transaction_id' => 'txn_456789123',
                'paid_at'           => now()->subDays(1),
            ],
            [
                'order_id'          => 4, // ORD-004
                'user_id'           => 5, // Alice Johnson
                'amount'            => 800.00,
                'currency'          => 'USD',
                'payment_method'    => 'balance',
                'status'            => 'success',
                'gateway_transaction_id' => 'txn_789123456',
                'paid_at'           => now()->subDays(3),
            ],
        ];

        foreach ($payments as $payment) {
            Payment::firstOrCreate(
                ['order_id' => $payment['order_id']],
                $payment
            );
        }
    }
}
