<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class TicketReplyPolicy
{
    use HandlesAuthorization;
    
    /**
     * Bir yanıtı görüntüleme
     *
     * @param Model $user
     * @param TicketReply $reply
     * @return bool
     */
    public function view(Model $user, TicketReply $reply): bool
    {
        // Admin her zaman görüntüleyebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Gizli yanıtları sadece admin görüntüleyebilir
        if ($reply->is_private) {
            return false;
        }
        
        // Kullanıcının kendisine ait yanıt mı?
        if ($reply->user_id == $user->getKey() && $reply->user_type == get_class($user)) {
            return true;
        }
        
        // Kullanıcı yanıtın bağlı olduğu talebin sahibi mi?
        $ticket = $reply->ticket;
        if ($ticket && $ticket->user_id == $user->getKey() && $ticket->user_type == get_class($user)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Yanıtı güncelleme
     *
     * @param Model $user
     * @param TicketReply $reply
     * @return bool
     */
    public function update(Model $user, TicketReply $reply): bool
    {
        // Admin her zaman güncelleyebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendisine ait yanıtı güncelleyebilir (son 30 dakika içinde yazılmışsa)
        if ($reply->user_id == $user->getKey() && $reply->user_type == get_class($user)) {
            // Yanıt son 30 dakika içinde oluşturulmuş mu kontrolü
            return $reply->created_at->diffInMinutes(now()) <= 30;
        }
        
        return false;
    }
    
    /**
     * Yanıtı silme
     *
     * @param Model $user
     * @param TicketReply $reply
     * @return bool
     */
    public function delete(Model $user, TicketReply $reply): bool
    {
        // Admin her zaman silebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendisine ait yanıtı silebilir (son 30 dakika içinde yazılmışsa)
        if ($reply->user_id == $user->getKey() && $reply->user_type == get_class($user)) {
            // Yanıt son 30 dakika içinde oluşturulmuş mu kontrolü
            return $reply->created_at->diffInMinutes(now()) <= 30;
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
        if (method_exists($user, 'can') && $user->can('manage tickets')) {
            return true;
        }
        
        return false;
    }
} 