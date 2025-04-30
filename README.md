# Laravel Support Ticket System

Bu paket, Laravel uygulamalarınız için tam özellikli bir destek bileti sistemi sunar. Kullanıcılarınızın destek taleplerini kolayca yönetmenize olanak tanır.

## Özellikler

- **Kullanıcı Biletleri**: Kullanıcılar destek bileti oluşturabilir, kendi biletlerini görüntüleyebilir ve bileti kapatabilir
- **Admin Yönetimi**: Yöneticiler tüm biletleri görüntüleyebilir, yanıtlayabilir ve durumunu değiştirebilir
- **Bildirimler**: E-posta ve veritabanı bildirimleri (sıraya alınabilir)
- **Yetkiler**: Laravel'in yerel Gate ve Policy sistemini kullanır
- **Özelleştirilebilir**: Görünümler, yapılandırmalar ve politikalar özelleştirilebilir
- **Çok Dilli**: Dil dosyaları ile çoklu dil desteği
- **Morph İlişkileri**: Herhangi bir kullanıcı modeli ile kullanılabilir

## Kurulum

Composer aracılığıyla paketi yükleyin:

```bash
composer require wiensa/support-ticket
```

Paket yapılandırması, görünümleri ve migrationları yayınlamak için aşağıdaki komutu çalıştırın:

```bash
php artisan supportticket:install
```

Migrationları çalıştırın:

```bash
php artisan migrate
```

## Kullanım

### Rotalar

Paket, aşağıdaki rotaları ekler:

- `yoursite.com/support` - Kullanıcı biletleri ana sayfası
- `yoursite.com/support/tickets/create` - Yeni bilet oluşturma formu
- `yoursite.com/support/tickets/{ticket}` - Bilet detayları ve yanıtlama
- `yoursite.com/admin/support` - Yönetici bileti ana sayfası
- `yoursite.com/admin/support/tickets/{ticket}` - Yönetici bilet detayları ve yanıtlama

Rota önekleri `config/supportticket.php` dosyasında özelleştirilebilir.

### Viewlar

Viewları özelleştirmek için aşağıdaki komutu çalıştırın:

```bash
php artisan vendor:publish --tag=supportticket-views
```

Bu, şu görünümleri yayınlar:
- `resources/views/vendor/supportticket/tickets/` - Kullanıcı görünümleri
- `resources/views/vendor/supportticket/admin/tickets/` - Yönetici görünümleri

### Olaylar

Paket aşağıdaki olayları tetikler:
- `Wiensa\SupportTicket\Events\TicketCreated` - Yeni bilet oluşturulduğunda
- `Wiensa\SupportTicket\Events\TicketReplied` - Bilete yanıt verildiğinde
- `Wiensa\SupportTicket\Events\TicketClosed` - Bilet kapatıldığında

### Yardımcı Fonksiyonlar

Paket aşağıdaki yardımcı fonksiyonları içerir:

```php
// ID'ye göre bilet alma
$ticket = support_ticket('ticket-id');

// Bilet durumu için HTML rozeti alma
echo ticket_status_badge('open');

// Belirli bir duruma sahip biletlerin sayısını alma
$openTickets = support_ticket_count('open');

// Bilet rotası oluşturma
$url = ticket_route('tickets.show', $ticket);
```

### Politikaları Özelleştirme

Paket politikalarını özelleştirmek için `app/Policies/TicketPolicy.php` sınıfını düzenleyin. Bu sınıf, `supportticket:install` komutunu çalıştırdığınızda otomatik olarak oluşturulur.

## Yapılandırma

`config/supportticket.php` dosyasında aşağıdaki ayarları özelleştirebilirsiniz:

- `routes` - Kullanıcı rotaları yapılandırması
- `admin_routes` - Yönetici rotaları yapılandırması
- `permissions` - İzin anahtarları
- `notifications` - Bildirim ayarları
- `events` - Olay tetikleme ayarları

## Lisans

Bu paket MIT lisansı altında lisanslanmıştır. 