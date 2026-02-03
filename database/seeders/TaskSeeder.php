<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Developer IDs: 3 = Rina Wijaya, 4 = Bambang Irawan, 5 = Siti Rahayu
        $developers = [3, 4, 5];

        // Project 1 - Website Company Profile - Milestone 1 (Desain UI/UX)
        $task = Task::create([
            'milestone_id' => 1,
            'user_id' => 3,
            'name' => 'Membuat wireframe website',
            'description' => 'Membuat wireframe untuk setiap halaman website',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 1,
            'user_id' => 3,
            'name' => 'Desain mockup UI',
            'description' => 'Membuat desain mockup UI untuk semua halaman',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 1,
            'user_id' => 3,
            'name' => 'Design system dan guidelines',
            'description' => 'Membuat design system dan guidelines untuk konsistensi',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 4]);

        // Project 1 - Website Company Profile - Milestone 2 (Development Frontend)
        $task = Task::create([
            'milestone_id' => 2,
            'user_id' => 3,
            'name' => 'Setup project dan dependencies',
            'description' => 'Setup React project dengan Tailwind CSS dan dependencies lainnya',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 2,
            'user_id' => 3,
            'name' => 'Buat komponen reusable',
            'description' => 'Membuat komponen button, card, input, navbar dll',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 2,
            'user_id' => 3,
            'name' => 'Implementasi halaman home',
            'description' => 'Membuat halaman home dengan hero, featured projects, testimonial',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 2,
            'user_id' => 3,
            'name' => 'Implementasi halaman portfolio',
            'description' => 'Membuat halaman portfolio dengan filter dan modal detail',
            'is_completed' => true,
            'order' => 4,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 2,
            'user_id' => 3,
            'name' => 'Implementasi halaman blog',
            'description' => 'Membuat halaman blog dengan list artikel dan detail artikel',
            'is_completed' => true,
            'order' => 5,
        ]);
        $task->contributors()->sync([3, 4]);

        // Project 1 - Website Company Profile - Milestone 3 (Development Backend)
        $task = Task::create([
            'milestone_id' => 3,
            'user_id' => 3,
            'name' => 'Setup Laravel project dan database',
            'description' => 'Setup Laravel dengan database MySQL',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 3,
            'user_id' => 3,
            'name' => 'Buat API endpoints untuk portfolio',
            'description' => 'Membuat REST API untuk CRUD portfolio',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 3,
            'user_id' => 3,
            'name' => 'Buat API endpoints untuk blog',
            'description' => 'Membuat REST API untuk CRUD artikel blog',
            'is_completed' => false,
            'order' => 3,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 3,
            'user_id' => 3,
            'name' => 'Implementasi authentication dan authorization',
            'description' => 'Membuat sistem login admin dan permission management',
            'is_completed' => false,
            'order' => 4,
        ]);
        $task->contributors()->sync([3, 4]);

        // Project 1 - Website Company Profile - Milestone 4 (Testing & Deployment)
        $task = Task::create([
            'milestone_id' => 4,
            'user_id' => 3,
            'name' => 'Unit testing backend',
            'description' => 'Membuat unit test untuk API endpoints',
            'is_completed' => false,
            'order' => 1,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 4,
            'user_id' => 3,
            'name' => 'Testing dan bug fixing',
            'description' => 'Melakukan testing di berbagai browser dan device',
            'is_completed' => false,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 4,
            'user_id' => 3,
            'name' => 'Setup hosting dan domain',
            'description' => 'Konfigurasi server, SSL, dan domain',
            'is_completed' => false,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 4,
            'user_id' => 3,
            'name' => 'Deployment ke production',
            'description' => 'Deploy aplikasi ke server production',
            'is_completed' => false,
            'order' => 4,
        ]);
        $task->contributors()->sync([4]);

        // Project 3 - E-Commerce Platform - Milestone 1 (Requirement Analysis)
        $task = Task::create([
            'milestone_id' => 5,
            'user_id' => 4,
            'name' => 'Gather requirements dari client',
            'description' => 'Melakukan meeting dan diskusi dengan client untuk memahami requirements',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 5,
            'user_id' => 4,
            'name' => 'Dokumentasi requirements',
            'description' => 'Membuat dokumentasi lengkap requirements dalam requirement document',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([4]);

        $task = Task::create([
            'milestone_id' => 5,
            'user_id' => 4,
            'name' => 'User story dan acceptance criteria',
            'description' => 'Membuat user story dan acceptance criteria untuk setiap fitur',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([4, 5]);

        // Project 3 - E-Commerce Platform - Milestone 2 (Database Design)
        $task = Task::create([
            'milestone_id' => 6,
            'user_id' => 4,
            'name' => 'Analisis data requirements',
            'description' => 'Menganalisis data yang dibutuhkan untuk e-commerce',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 6,
            'user_id' => 4,
            'name' => 'Desain database schema',
            'description' => 'Membuat ERD dan database schema design',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 6,
            'user_id' => 5,
            'name' => 'Optimasi database dan indexing',
            'description' => 'Melakukan optimasi database dan membuat index yang diperlukan',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([5]);

        // Project 3 - E-Commerce Platform - Milestone 3 (Payment Gateway Integration)
        $task = Task::create([
            'milestone_id' => 7,
            'user_id' => 4,
            'name' => 'Research payment gateway providers',
            'description' => 'Riset payment gateway yang sesuai (Stripe, Midtrans, dll)',
            'is_completed' => false,
            'order' => 1,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 7,
            'user_id' => 4,
            'name' => 'Implementasi payment gateway API',
            'description' => 'Integrasi payment gateway ke sistem',
            'is_completed' => false,
            'order' => 2,
        ]);
        $task->contributors()->sync([4]);

        $task = Task::create([
            'milestone_id' => 7,
            'user_id' => 5,
            'name' => 'Testing payment flow',
            'description' => 'Testing transaksi pembayaran dari A-Z',
            'is_completed' => false,
            'order' => 3,
        ]);
        $task->contributors()->sync([4, 5]);

        // Project 4 - Mobile App Delivery - Milestone 1 (Design & Prototype)
        $task = Task::create([
            'milestone_id' => 8,
            'user_id' => 3,
            'name' => 'Wireframing mobile app',
            'description' => 'Membuat wireframe untuk mobile app delivery',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 8,
            'user_id' => 3,
            'name' => 'High-fidelity mockup',
            'description' => 'Membuat high-fidelity mockup untuk semua screen',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 8,
            'user_id' => 3,
            'name' => 'Interactive prototype',
            'description' => 'Membuat interactive prototype menggunakan Figma atau prototype tool',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 5]);

        // Project 4 - Mobile App Delivery - Milestone 2 (Development)
        $task = Task::create([
            'milestone_id' => 9,
            'user_id' => 3,
            'name' => 'Setup Flutter project',
            'description' => 'Setup Flutter project dengan dependencies yang diperlukan',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 9,
            'user_id' => 3,
            'name' => 'Implementasi authentication',
            'description' => 'Membuat sistem login dan registration',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 9,
            'user_id' => 3,
            'name' => 'Implementasi fitur order',
            'description' => 'Membuat fitur order dan tracking pesanan',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 9,
            'user_id' => 3,
            'name' => 'Implementasi payment integration',
            'description' => 'Integrasi payment gateway ke aplikasi mobile',
            'is_completed' => true,
            'order' => 4,
        ]);
        $task->contributors()->sync([3]);

        // Project 4 - Mobile App Delivery - Milestone 3 (Testing)
        $task = Task::create([
            'milestone_id' => 10,
            'user_id' => 3,
            'name' => 'Unit testing',
            'description' => 'Membuat unit test untuk setiap module',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 10,
            'user_id' => 3,
            'name' => 'Integration testing',
            'description' => 'Testing integrasi antara berbagai module',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 10,
            'user_id' => 3,
            'name' => 'User acceptance testing',
            'description' => 'Testing dengan client untuk mendapatkan feedback',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 5]);

        // Project 4 - Mobile App Delivery - Milestone 4 (Launch)
        $task = Task::create([
            'milestone_id' => 11,
            'user_id' => 3,
            'name' => 'Build APK dan IPA',
            'description' => 'Build release APK untuk Android dan IPA untuk iOS',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([3]);

        $task = Task::create([
            'milestone_id' => 11,
            'user_id' => 3,
            'name' => 'Submit ke app store',
            'description' => 'Submit aplikasi ke Google Play Store dan App Store',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([3, 5]);

        $task = Task::create([
            'milestone_id' => 11,
            'user_id' => 3,
            'name' => 'App launch dan monitoring',
            'description' => 'Launch aplikasi dan monitoring performance di app store',
            'is_completed' => true,
            'order' => 3,
        ]);
        $task->contributors()->sync([3, 5]);

        // Project 6 - Client Portal Revamp - Milestone 1 (Discovery & Planning)
        $task = Task::create([
            'milestone_id' => 12,
            'user_id' => 4,
            'name' => 'Audit existing portal',
            'description' => 'Review alur dan temukan pain points pada portal saat ini',
            'is_completed' => true,
            'order' => 1,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 12,
            'user_id' => 5,
            'name' => 'Define feature scope',
            'description' => 'Menentukan scope fitur dan prioritas rilis',
            'is_completed' => true,
            'order' => 2,
        ]);
        $task->contributors()->sync([4, 5]);

        // Project 6 - Client Portal Revamp - Milestone 2 (UI Redesign)
        $task = Task::create([
            'milestone_id' => 13,
            'user_id' => 3,
            'name' => 'Design new layout',
            'description' => 'Membuat layout baru dan komponen UI utama',
            'is_completed' => false,
            'order' => 1,
        ]);
        $task->contributors()->sync([3, 4]);

        $task = Task::create([
            'milestone_id' => 13,
            'user_id' => 3,
            'name' => 'Build UI components',
            'description' => 'Implementasi komponen UI di frontend',
            'is_completed' => false,
            'order' => 2,
        ]);
        $task->contributors()->sync([3]);

        // Project 6 - Client Portal Revamp - Milestone 3 (Reporting Module)
        $task = Task::create([
            'milestone_id' => 14,
            'user_id' => 4,
            'name' => 'Define reporting metrics',
            'description' => 'Menentukan metrik dan data yang ditampilkan di report',
            'is_completed' => false,
            'order' => 1,
        ]);
        $task->contributors()->sync([4, 5]);

        $task = Task::create([
            'milestone_id' => 14,
            'user_id' => 4,
            'name' => 'Implement report API',
            'description' => 'Membuat endpoint API untuk data laporan',
            'is_completed' => false,
            'order' => 2,
        ]);
        $task->contributors()->sync([4]);
    }
}
