<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketPolicy
 * 
 * This policy governs ticket access and management.
 * It can be extended or overridden by the host application.
 */
class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Tüm taleplere erişim
     *
     * @param Model $user
     * @return bool
     */
    public function viewAny(Model $user): bool
    {
        // Admin kontrolü
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        
        // Administrator ya da destek ekibi yetkisine sahip mi?
        return $this->isAdmin($user);
    }
    
    /**
     * Bir talebi görüntüleme
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function view(Model $user, Ticket $ticket): bool
    {
        // Admin her zaman görüntüleyebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcının kendi talebi mi?
        if ($ticket->user_id == $user->getKey() && $ticket->user_type == get_class($user)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Yeni talep oluşturma
     *
     * @param Model $user
     * @return bool
     */
    public function create(Model $user): bool
    {
        // Tüm kullanıcılar talep oluşturabilir 
        return true;
    }
    
    /**
     * Talebi güncelleme
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function update(Model $user, Ticket $ticket): bool
    {
        // Admin her zaman güncelleyebilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendi talebini güncelleyebilir (sadece açıksa)
        if ($ticket->user_id == $user->getKey() && $ticket->user_type == get_class($user)) {
            return $ticket->isOpen();
        }
        
        return false;
    }
    
    /**
     * Talebi silme
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function delete(Model $user, Ticket $ticket): bool
    {
        // Sadece admin silebilir
        return $this->isAdmin($user);
    }
    
    /**
     * Talebi kapatma
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function close(Model $user, Ticket $ticket): bool
    {
        // Admin her zaman kapatabilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendi talebini kapatabilir
        if ($ticket->user_id == $user->getKey() && $ticket->user_type == get_class($user)) {
            return !$ticket->isClosed();
        }
        
        return false;
    }
    
    /**
     * Talebe yanıt yazma
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function reply(Model $user, Ticket $ticket): bool
    {
        // Admin her zaman yanıt yazabilir
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Kullanıcı kendi talebine yanıt yazabilir (kapalı değilse)
        if ($ticket->user_id == $user->getKey() && $ticket->user_type == get_class($user)) {
            return !$ticket->isClosed();
        }
        
        return false;
    }
    
    /**
     * Admin yanıtı yazabilme
     *
     * @param Model $user
     * @param Ticket $ticket
     * @return bool
     */
    public function adminReply(Model $user, Ticket $ticket): bool
    {
        // Sadece admin yanıtı yazabilir
        return $this->isAdmin($user);
    }
    
    /**
     * Kullanıcının admin olup olmadığını kontrol et
     *
     * @param Model $user
     * @return bool
     */
    protected function isAdmin(Model $user): bool
    {
        // Laravel'in built-in bileşenleriyle uyumlu çalışacak şekilde kontroller
        
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