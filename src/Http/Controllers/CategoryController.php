<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Wiensa\SupportTicket\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Kategorileri listele
     */
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);
        
        $categories = Category::orderBy('sort_order', 'asc')->get();
        
        return view('supportticket::categories.index', compact('categories'));
    }
    
    /**
     * Yeni kategori oluştur
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Category::class);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->color = $request->color;
        $category->icon = $request->icon;
        $category->description = $request->description;
        $category->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
        $category->sort_order = $request->sort_order ?? 0;
        
        $category->save();
        
        return redirect()->route('supportticket.categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }
    
    /**
     * Kategoriyi güncelle
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $category->name = $request->name;
        // Slug'ı sadece değişmişse güncelle
        if ($category->name !== $request->name) {
            $category->slug = Str::slug($request->name);
        }
        $category->color = $request->color;
        $category->icon = $request->icon;
        $category->description = $request->description;
        $category->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
        $category->sort_order = $request->sort_order ?? 0;
        
        $category->save();
        
        return redirect()->route('supportticket.categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }
    
    /**
     * Kategoriyi sil
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        
        // İlişkili talepleri kontrol et
        $hasTickets = $category->tickets()->exists();
        
        if ($hasTickets) {
            return redirect()->route('supportticket.categories.index')
                ->with('error', 'Kategori kullanımda olduğu için silinemez.');
        }
        
        $category->delete();
        
        return redirect()->route('supportticket.categories.index')
            ->with('success', 'Kategori başarıyla silindi.');
    }
} 