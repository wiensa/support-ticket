<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CategoryPolicy
{
    use HandlesAuthorization;
    
    /**
     * Tüm kategorilere erişim
     *
     * @param Model $user
     * @return bool
     */
    public function viewAny(Model $user): bool
    {
        return $this->isAdmin($user);
    }
    
    /**
     * Bir kategoriyi görüntüleme
     *
     * @param Model $user
     * @param Category $category
     * @return bool
     */
    public function view(Model $user, Category $category): bool
    {
        return $this->isAdmin($user);
    }
    
    /**
     * Yeni kategori oluşturma
     *
     * @param Model $user
     * @return bool
     */
    public function create(Model $user): bool
    {
        return $this->isAdmin($user);
    }
    
    /**
     * Kategoriyi güncelleme
     *
     * @param Model $user
     * @param Category $category
     * @return bool
     */
    public function update(Model $user, Category $category): bool
    {
        return $this->isAdmin($user);
    }
    
    /**
     * Kategoriyi silme
     *
     * @param Model $user
     * @param Category $category
     * @return bool
     */
    public function delete(Model $user, Category $category): bool
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
        if (method_exists($user, 'can') && $user->can('manage categories')) {
            return true;
        }
        
        return false;
    }
} 