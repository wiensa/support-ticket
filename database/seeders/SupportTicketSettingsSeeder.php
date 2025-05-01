<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportTicketSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'ticket_auto_close_days',
                'value' => '7',
                'description' => 'Cevapsız destek taleplerinin kaç gün sonra otomatik kapatılacağı',
                'group' => 'general',
                'is_public' => false,
            ],
            [
                'key' => 'notification_new_ticket',
                'value' => 'true',
                'description' => 'Yeni destek talebi oluşturulduğunda bildirim gönder',
                'group' => 'notifications',
                'is_public' => false,
            ],
            [
                'key' => 'notification_ticket_reply',
                'value' => 'true',
                'description' => 'Destek talebine yanıt geldiğinde bildirim gönder',
                'group' => 'notifications',
                'is_public' => true,
            ],
            [
                'key' => 'notification_ticket_status_change',
                'value' => 'true',
                'description' => 'Destek talebi durumu değiştiğinde bildirim gönder',
                'group' => 'notifications',
                'is_public' => true,
            ],
            [
                'key' => 'allow_file_attachments',
                'value' => 'true',
                'description' => 'Destek taleplerine dosya eklenmesine izin ver',
                'group' => 'attachments',
                'is_public' => true,
            ],
            [
                'key' => 'max_attachment_size',
                'value' => '5',
                'description' => 'Maksimum dosya ekleme boyutu (MB)',
                'group' => 'attachments',
                'is_public' => true,
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip',
                'description' => 'İzin verilen dosya türleri (virgülle ayrılmış)',
                'group' => 'attachments',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('support_ticket_settings')->insert(array_merge(
                $setting,
                [
                    'id' => Str::uuid()->toString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ));
        }
    }
} 