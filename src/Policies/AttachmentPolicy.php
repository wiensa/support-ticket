<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\Attachment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class AttachmentPolicy
{
    use HandlesAuthorization;
    
    /**
     * Bir dosya ekini görüntüleme
     *
     * @param Model $user
     * @param Attachment $attachment
     * @return bool
     */
    public function view(Model $user, Attachment $attachment): bool
    {
        // Admin her zaman görüntüleyebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcının kendisine ait dosya eki mi?
        if ($attachment->user_id == $user->getKey() && $attachment->user_type == get_class($user)) {
            return true;
        }
        
        // Kullanıcı dosya ekinin bağlı olduğu talebin sahibi mi?
        $attachable = $attachment->attachable;
        if ($attachable && method_exists($attachable, 'user')) {
            $ticketUser = $attachable->user;
            if ($ticketUser && $ticketUser->getKey() == $user->getKey() && get_class($ticketUser) == get_class($user)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Dosya ekini silme
     *
     * @param Model $user
     * @param Attachment $attachment
     * @return bool
     */
    public function delete(Model $user, Attachment $attachment): bool
    {
        // Admin her zaman silebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendisine ait dosya ekini silebilir
        if ($attachment->user_id == $user->getKey() && $attachment->user_type == get_class($user)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Kullanıcının admin olup olmadığını kontrol et
     *
     * @param Model $user
     * @return bool
     */
    protected function isAdmin(Model $user): bool
    {
        // Spatie Permission paketi
        if (method_exists($user, 'hasRole') && $user->hasRole(config('supportticket.admin_role', 'admin'))) {
            return true;
        }
        
        // is_admin özelliği
        if (property_exists($user, 'is_admin') && $user->is_admin) {
            return true;
        }
        
        // admin özelliği
        if (property_exists($user, 'admin') && $user->admin) {
            return true;
        }
        
        // admin_type özelliği
        if (property_exists($user, 'user_type') && $user->user_type === 'admin') {
            return true;
        }
        
        // can metodu
        if (method_exists($user, 'can') && $user->can('manage attachments')) {
            return true;
        }
        
        return false;
    }
} 