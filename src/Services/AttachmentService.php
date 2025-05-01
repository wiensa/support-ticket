<?php

namespace Wiensa\SupportTicket\Services;

use Wiensa\SupportTicket\Models\Attachment;
use Wiensa\SupportTicket\Events\AttachmentUploaded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AttachmentService
{
    /**
     * Dosya yükleme 
     * 
     * @param UploadedFile $file
     * @param Model $attachableModel
     * @param Model|null $user
     * @return Attachment
     */
    public function uploadFile(UploadedFile $file, Model $attachableModel, ?Model $user = null): Attachment
    {
        // Kullanıcı bilgisini belirle
        $user = $user ?? (Auth::check() ? Auth::user() : null);
        
        // Yapılandırma ayarlarını kontrol et
        $this->validateFileUpload($file);
        
        // Dosyayı kaydet
        $filePath = $file->store(
            config('supportticket.attachments.storage_path', 'ticket_attachments'), 
            config('supportticket.attachments.storage_disk', 'public')
        );
        
        // Attachment oluştur
        $attachment = new Attachment([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
        
        // Kullanıcı ilişkisi
        if ($user) {
            $attachment->user()->associate($user);
        }
        
        // İlişkiyi kaydet
        $attachableModel->attachments()->save($attachment);
        
        // Event'i config'e göre tetikle
        if (config('supportticket.events.attachment_uploaded', true)) {
            event(new AttachmentUploaded($attachment, $attachableModel));
        }
        
        return $attachment;
    }
    
    /**
     * Dosya yükleme işlemini doğrula
     * 
     * @param UploadedFile $file
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateFileUpload(UploadedFile $file): void
    {
        // Dosya türü kontrolü
        if (!$this->isAllowedFileType($file->getMimeType())) {
            throw new \InvalidArgumentException(
                "File type not allowed. Allowed types: " . implode(', ', $this->getAllowedExtensions())
            );
        }
        
        // Dosya boyutu kontrolü
        if ($file->getSize() > $this->getMaxFileSize()) {
            throw new \InvalidArgumentException(
                "File size exceeds maximum allowed size of " . 
                (config('supportticket.attachments.max_size', 5)) . "MB"
            );
        }
    }
    
    /**
     * Birden fazla dosya yükle
     * 
     * @param array $files
     * @param Model $attachableModel
     * @param Model|null $user
     * @return array
     */
    public function uploadFiles(array $files, Model $attachableModel, ?Model $user = null): array
    {
        $attachments = [];
        
        foreach ($files as $file) {
            $attachments[] = $this->uploadFile($file, $attachableModel, $user);
        }
        
        return $attachments;
    }
    
    /**
     * Dosyayı sil
     * 
     * @param Attachment $attachment
     * @return bool
     */
    public function deleteFile(Attachment $attachment): bool
    {
        // Dosyayı diskten sil
        Storage::disk(config('supportticket.attachments.storage_disk', 'public'))
               ->delete($attachment->file_path);
        
        // Veritabanı kaydını sil
        return $attachment->delete();
    }
    
    /**
     * Dosya türünün izin verilen dosya türlerinden olup olmadığını kontrol et
     * 
     * @param string $fileType
     * @return bool
     */
    public function isAllowedFileType(string $fileType): bool
    {
        $allowedMimes = $this->getAllowedMimeTypes();
        
        return in_array($fileType, $allowedMimes);
    }
    
    /**
     * İzin verilen dosya türlerinin MIME tiplerini al
     * 
     * @return array
     */
    public function getAllowedMimeTypes(): array
    {
        $allowedExtensions = $this->getAllowedExtensions();
        $mimeTypes = [];
        
        // Uzantıları MIME tiplerine çevir
        foreach ($allowedExtensions as $ext) {
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $mimeTypes[] = 'image/jpeg';
                    break;
                case 'png':
                    $mimeTypes[] = 'image/png';
                    break;
                case 'gif':
                    $mimeTypes[] = 'image/gif';
                    break;
                case 'pdf':
                    $mimeTypes[] = 'application/pdf';
                    break;
                case 'doc':
                    $mimeTypes[] = 'application/msword';
                    break;
                case 'docx':
                    $mimeTypes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                case 'xls':
                    $mimeTypes[] = 'application/vnd.ms-excel';
                    break;
                case 'xlsx':
                    $mimeTypes[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    break;
                case 'zip':
                    $mimeTypes[] = 'application/zip';
                    $mimeTypes[] = 'application/x-zip-compressed';
                    break;
                case 'txt':
                    $mimeTypes[] = 'text/plain';
                    break;
            }
        }
        
        return $mimeTypes;
    }
    
    /**
     * İzin verilen dosya uzantılarını al
     * 
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        // Ayarlardan dosya türleri ayarını al ya da varsayılan değeri kullan
        $allowedTypes = explode(',', config('supportticket.attachments.allowed_types', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,txt'));
        
        return array_map('trim', $allowedTypes);
    }
    
    /**
     * İzin verilen maksimum dosya boyutunu al (bayt cinsinden)
     * 
     * @return int
     */
    public function getMaxFileSize(): int
    {
        // Ayarlardan max boyut ayarını al ya da varsayılan olarak 5MB kullan
        $maxSizeMB = config('supportticket.attachments.max_size', 5);
        
        return $maxSizeMB * 1024 * 1024;
    }
} 