<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Wiensa\SupportTicket\Models\Ticket;

class SupportTicketDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori ID'lerini alma
        $categories = DB::table('support_ticket_categories')
            ->select('id', 'name')
            ->get()
            ->keyBy('name')
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        // Örnek destek talepleri
        $tickets = [
            [
                'subject' => 'Web sitesine giriş yapamıyorum',
                'message' => 'Merhaba, şifremi doğru girdiğim halde web sitesine giriş yapamıyorum. Ne yapmam gerekiyor?',
                'status' => Ticket::STATUS_OPEN,
                'priority' => 'high',
                'category' => $categories['Teknik Destek'] ?? null,
                'created_at' => now()->subDays(2),
            ],
            [
                'subject' => 'Faturamdaki hata',
                'message' => 'Son faturamda fazla ücretlendirme yapılmış görünüyor. Faturamı kontrol edip düzeltebilir misiniz?',
                'status' => Ticket::STATUS_PENDING,
                'priority' => 'medium',
                'category' => $categories['Fatura'] ?? null,
                'created_at' => now()->subDays(5),
            ],
            [
                'subject' => 'Hesap bilgilerimi güncellemek istiyorum',
                'message' => 'Adres bilgilerimi güncellemem gerekiyor. Nasıl değiştirebilirim?',
                'status' => Ticket::STATUS_RESOLVED,
                'priority' => 'low',
                'category' => $categories['Hesap Yönetimi'] ?? null,
                'created_at' => now()->subDays(10),
                'closed_at' => now()->subDays(8),
            ],
            [
                'subject' => 'Mobil uygulama önerisi',
                'message' => 'Hizmetlerinize daha kolay erişmek için bir mobil uygulama geliştirmenizi öneriyorum.',
                'status' => Ticket::STATUS_OPEN,
                'priority' => 'low',
                'category' => $categories['Öneriler'] ?? null,
                'created_at' => now()->subDays(1),
            ],
            [
                'subject' => 'Yardım gerekiyor',
                'message' => 'Ürünlerle ilgili daha detaylı bilgi almak istiyorum. Benimle iletişime geçebilir misiniz?',
                'status' => Ticket::STATUS_CLOSED,
                'priority' => 'medium',
                'category' => $categories['Diğer'] ?? null,
                'created_at' => now()->subDays(20),
                'closed_at' => now()->subDays(18),
            ],
        ];

        // Destek taleplerini ve yanıtlarını ekleme
        foreach ($tickets as $ticket) {
            $ticketId = Str::uuid()->toString();
            
            DB::table('support_tickets')->insert([
                'id' => $ticketId,
                'subject' => $ticket['subject'],
                'message' => $ticket['message'],
                'status' => $ticket['status'],
                'priority' => $ticket['priority'],
                'category' => $ticket['category'],
                'created_at' => $ticket['created_at'],
                'updated_at' => $ticket['created_at'],
                'closed_at' => $ticket['closed_at'] ?? null,
            ]);

            // Kapalı veya çözülmüş taleplere yanıt ekle
            if (in_array($ticket['status'], [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])) {
                DB::table('support_ticket_replies')->insert([
                    'id' => Str::uuid()->toString(),
                    'ticket_id' => $ticketId,
                    'message' => 'Merhaba, talebiniz için teşekkürler. Sorunuz çözüldü mü?',
                    'is_admin' => true,
                    'created_at' => $ticket['created_at']->addHours(2),
                    'updated_at' => $ticket['created_at']->addHours(2),
                ]);
                
                DB::table('support_ticket_replies')->insert([
                    'id' => Str::uuid()->toString(),
                    'ticket_id' => $ticketId,
                    'message' => 'Evet, teşekkür ederim. Sorun çözüldü.',
                    'is_admin' => false,
                    'created_at' => $ticket['created_at']->addHours(4),
                    'updated_at' => $ticket['created_at']->addHours(4),
                ]);
            }
            
            // Beklemede olan taleplere sadece admin yanıtı ekle
            if ($ticket['status'] === Ticket::STATUS_PENDING) {
                DB::table('support_ticket_replies')->insert([
                    'id' => Str::uuid()->toString(),
                    'ticket_id' => $ticketId,
                    'message' => 'Talebiniz üzerinde çalışıyoruz. En kısa sürede dönüş yapacağız.',
                    'is_admin' => true,
                    'created_at' => $ticket['created_at']->addHours(3),
                    'updated_at' => $ticket['created_at']->addHours(3),
                ]);
            }
        }
    }
} 