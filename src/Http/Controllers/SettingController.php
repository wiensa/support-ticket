<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Wiensa\SupportTicket\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Ayarları görüntüle
     */
    public function index(): View
    {
        $this->authorize('viewAny', Setting::class);
        
        $settings = Setting::orderBy('group')->get()->groupBy('group');
        
        return view('supportticket::settings.index', compact('settings'));
    }
    
    /**
     * Ayarları güncelle
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);
        
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        foreach ($request->input('settings') as $key => $value) {
            Setting::setValue($key, $value);
        }
        
        return redirect()->route('supportticket.settings.index')
            ->with('success', 'Ayarlar başarıyla güncellendi.');
    }
    
    /**
     * Ayar değerini almak için API endpoint'i
     */
    public function getSetting(Request $request, string $key)
    {
        // Sadece public ayarları herkes görebilir, diğerleri için yetki kontrol
        $setting = Setting::where('key', $key)->first();
        
        if (!$setting) {
            return response()->json(['error' => 'Ayar bulunamadı'], 404);
        }
        
        if (!$setting->is_public) {
            $this->authorize('view', $setting);
        }
        
        return response()->json([
            'key' => $setting->key,
            'value' => $setting->value,
            'group' => $setting->group,
        ]);
    }
    
    /**
     * Grup bazında ayarları almak için API endpoint'i
     */
    public function getSettingsByGroup(Request $request, string $group)
    {
        $settings = Setting::where('group', $group)
            ->where(function($query) use ($request) {
                $query->where('is_public', true);
                
                // Eğer yetkili kullanıcı ise tüm ayarları görebilir
                if ($request->user() && $request->user()->can('viewAny', Setting::class)) {
                    $query->orWhere('is_public', false);
                }
            })
            ->get()
            ->pluck('value', 'key');
        
        return response()->json($settings);
    }
} 