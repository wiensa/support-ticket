<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportTicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Teknik Destek',
                'slug' => 'teknik-destek',
                'color' => '#f44336',
                'icon' => 'fa-solid fa-wrench',
                'description' => 'Teknik konularda yardım için bu kategoriyi kullanın',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Fatura',
                'slug' => 'fatura',
                'color' => '#4caf50',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'description' => 'Faturalama sorunları için bu kategoriyi kullanın',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hesap Yönetimi',
                'slug' => 'hesap-yonetimi',
                'color' => '#2196f3',
                'icon' => 'fa-solid fa-user-gear',
                'description' => 'Hesap ayarları ve yönetimi için bu kategoriyi kullanın',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Öneriler',
                'slug' => 'oneriler',
                'color' => '#ff9800',
                'icon' => 'fa-solid fa-lightbulb',
                'description' => 'Yeni özellikler veya iyileştirmeler önerisi için bu kategoriyi kullanın',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Diğer',
                'slug' => 'diger',
                'color' => '#9e9e9e',
                'icon' => 'fa-solid fa-circle-question',
                'description' => 'Diğer tüm konular için bu kategoriyi kullanın',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('support_ticket_categories')->insert(array_merge(
                $category,
                [
                    'id' => Str::uuid()->toString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ));
        }
    }
} 