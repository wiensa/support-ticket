<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class SettingPolicy
{
    use HandlesAuthorization;
    
    /**
     * Tüm ayarlara erişim
     *
     * @param Model $user
     * @return bool
     */
    public function viewAny(Model $user): bool
    {
        return $this->isAdmin($user);
    }
    
    /**
     * Bir ayarı görüntüleme
     *
     * @param Model $user
     * @param Setting $setting
     * @return bool
     */
    public function view(Model $user, Setting $setting): bool
    {
        // Public ayarsa herkes görebilir
        if ($setting->is_public) {
            return true;
        }
        
        return $this->isAdmin($user);
    }
    
    /**
     * Ayarları güncelleme
     *
     * @param Model $user
     * @return bool
     */
    public function update(Model $user): bool
    {
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
        if (method_exists($user, 'can') && $user->can('manage settings')) {
            return true;
        }
        
        return false;
    }
} 