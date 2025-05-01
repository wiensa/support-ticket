# Laravel Support Ticket System

Bu paket, Laravel uygulamalarınız için tam özellikli bir destek bileti sistemi sunar. Kullanıcılarınızın destek taleplerini kolayca yönetmenize olanak tanır.

## Özellikler

- **Kullanıcı Talepleri**: Kullanıcılar destek talebi oluşturabilir, görüntüleyebilir ve yanıtlayabilir
- **Kategori Sistemi**: Talepleri farklı kategorilere ayırma
- **Dosya Ekleri**: Taleplere ve yanıtlara dosya ekleme desteği
- **Bildirimler**: E-posta bildirimleri ve webhook entegrasyonları
- **Admin Paneli**: Tüm destek taleplerini yönetme
- **Ayarlar Yönetimi**: Sistem ayarlarını admin panelinden değiştirme
- **API Desteği**: RESTful API entegrasyonu
- **Yetkilendirme**: Laravel Gate ve Policy sistemi ile tam entegrasyon
- **Morph İlişkileri**: Herhangi bir kullanıcı modeli ile kullanılabilir
- **Modern UI**: Bootstrap 5 ile responsive tasarım
- **Filtreleme ve Arama**: Gelişmiş arama ve filtreleme özellikleri

## Ekran Görüntüleri

### Kullanıcı Arayüzü

- **Talep Listesi**
  ![Talep Listesi](screenshots/ticket-list.png)

- **Talep Detayı**
  ![Talep Detayı](screenshots/ticket-detail.png)

- **Talep Oluşturma**
  ![Talep Oluşturma](screenshots/create-ticket.png)

### Admin Arayüzü

- **Admin Talep Listesi**
  ![Admin Talep Listesi](screenshots/admin-ticket-list.png)

- **Admin Talep Detayı**
  ![Admin Talep Detayı](screenshots/admin-ticket-detail.png)

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

Temel verileri eklemek için seed komutunu çalıştırın:

```bash
php artisan db:seed --class=Database\\Seeders\\SupportTicketCategorySeeder
php artisan db:seed --class=Database\\Seeders\\SupportTicketSettingsSeeder
```

## Kullanım

### Kullanıcı Modülü Entegrasyonu

Kullanıcı modülünüze destek talepleri sistemini entegre etmek için, projenizin `routes/web.php` dosyasına aşağıdaki rotaları ekleyin:

```php
// Destek talepleri için route'ları include et
Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('support')->name('support.')->group(function () {
        include base_path('vendor/wiensa/support-ticket/routes/web.php');
    });
});
```

Blade şablonlarınıza destek talepleri bağlantısını ekleyin:

```html
<a href="{{ route('supportticket.tickets.index') }}">Destek Taleplerim</a>
```

### Admin Modülü Entegrasyonu

Admin panel yapınıza destek talepleri yönetimini entegre etmek için, admin route yapılandırmanıza:

```php
// Admin için destek talepleri route'larını include et
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::prefix('admin/support')->name('admin.support.')->group(function () {
        include base_path('vendor/wiensa/support-ticket/routes/admin.php');
    });
});
```

Ayrıca, config dosyasında admin middleware'i düzenleyebilirsiniz:

```php
// config/supportticket.php
'admin_middleware' => ['web', 'auth', 'admin'], // Admin middleware
```

### Blade Komponentleri

Paket, kullanmanız için bir dizi Blade komponenti içerir:

```html
<!-- Talep listesi komponenti -->
<x-supportticket::ticket-list :tickets="$tickets" />

<!-- Talep detay komponenti -->
<x-supportticket::ticket-detail :ticket="$ticket" />

<!-- Yanıt form komponenti -->
<x-supportticket::reply-form :ticket="$ticket" />

<!-- Kategori seçici komponenti -->
<x-supportticket::category-selector :selected="$category" />

<!-- Dosya eki yükleyici komponenti -->
<x-supportticket::attachment-uploader :ticketable="$ticket" />
```

### Partial Bileşenleri

Paket, projenize entegre edebileceğiniz partial bileşenler içerir:

```blade
<!-- Bildirimler -->
@include('partials.alerts')

<!-- Kategori Filtreleme -->
@include('partials.category-filter')

<!-- Dosya Eki Formu -->
@include('partials.attachment-form', ['ticketable' => $ticket])
```

### Servis Sınıfları

Paketi programatik olarak kullanmak için aşağıdaki servis sınıflarını kullanabilirsiniz:

```php
use Wiensa\SupportTicket\Services\TicketService;
use Wiensa\SupportTicket\Services\AttachmentService;
use Wiensa\SupportTicket\Services\NotificationService;

// Ticket Service
$ticketService = app(TicketService::class);
$ticket = $ticketService->createTicket([
    'subject' => 'Konu',
    'message' => 'Mesaj',
    'priority' => 'medium',
    'category' => $categoryId,
], $user);

// Attachment Service
$attachmentService = app(AttachmentService::class);
$attachment = $attachmentService->uploadFile($file, $ticket, $user);

// Notification Service
$notificationService = app(NotificationService::class);
$notificationService->sendNewTicketNotifications($ticket);
```

### Olaylar ve Dinleyiciler

Paket, aşağıdaki olayları tetikler:

- `Wiensa\SupportTicket\Events\TicketCreated` - Yeni talep oluşturulduğunda
- `Wiensa\SupportTicket\Events\TicketReplied` - Talebe yanıt verildiğinde
- `Wiensa\SupportTicket\Events\TicketClosed` - Talep kapatıldığında
- `Wiensa\SupportTicket\Events\TicketReopened` - Talep yeniden açıldığında
- `Wiensa\SupportTicket\Events\TicketStatusChanged` - Talep durumu değiştiğinde
- `Wiensa\SupportTicket\Events\AttachmentUploaded` - Dosya yüklendiğinde

Uygulamanızda bu olayları dinlemek için event listener'lar oluşturabilirsiniz.

### API Kullanımı

API endpointleri şunlardır:

- `GET /api/support/tickets` - Tüm talepleri listeler
- `GET /api/support/tickets/{id}` - Belirli bir talebi görüntüler
- `POST /api/support/tickets` - Yeni talep oluşturur
- `POST /api/support/tickets/{id}/replies` - Talebe yanıt ekler
- `PUT /api/support/tickets/{id}` - Talebi günceller
- `DELETE /api/support/tickets/{id}` - Talebi siler
- `POST /api/support/tickets/{id}/close` - Talebi kapatır
- `POST /api/support/tickets/{id}/reopen` - Talebi yeniden açar
- `POST /api/support/attachments` - Dosya ekler
- `GET /api/support/categories` - Kategorileri listeler

API kullanımı örneği:

```php
// Yeni talep oluşturma
$response = $client->post('/api/support/tickets', [
    'json' => [
        'subject' => 'API Test',
        'message' => 'Bu bir API test talebidir',
        'category' => $categoryId,
        'priority' => 'high',
    ],
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
    ],
]);
```

## Yapılandırma

`config/supportticket.php` dosyasında aşağıdaki ayarları özelleştirebilirsiniz:

- `route_prefix` - Rota öneki
- `middleware` - Rotalarda kullanılacak middleware
- `admin_middleware` - Admin rotalarında kullanılacak middleware
- `events` - Olay yapılandırması
- `attachments` - Dosya eki yapılandırması
  - `max_size` - Maksimum dosya boyutu (MB)
  - `allowed_types` - İzin verilen dosya tipleri
  - `storage_disk` - Depolama diski
  - `storage_path` - Depolama yolu
- `categories` - Kategori yapılandırması
  - `allow_create` - Kullanıcıların kategori oluşturmasına izin ver
  - `default` - Varsayılan kategori ID'si
- `auto_close` - Otomatik kapanma ayarları
  - `enabled` - Aktif/Pasif
  - `days` - Otomatik kapanma günü
- `admin_emails` - Bildirim alacak admin e-postaları

## Özelleştirme

### Views

Viewları özelleştirmek için:

```bash
php artisan vendor:publish --tag=supportticket-views
```

### Config

Yapılandırma dosyasını özelleştirmek için:

```bash
php artisan vendor:publish --tag=supportticket-config
```

### Translations

Dil dosyalarını özelleştirmek için:

```bash
php artisan vendor:publish --tag=supportticket-lang
```

### Assets

CSS ve JS dosyalarını özelleştirmek için:

```bash
php artisan vendor:publish --tag=supportticket-assets
```

## Değişiklik Geçmişi

Değişiklik geçmişi için [CHANGELOG.md](CHANGELOG.md) dosyasına bakın.

## Lisans

Bu paket MIT lisansı altında lisanslanmıştır. 