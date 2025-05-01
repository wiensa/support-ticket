<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Wiensa\SupportTicket\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;

class AttachmentController extends Controller
{
    /**
     * Dosya yükle
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5MB
            'attachable_id' => 'required|string',
            'attachable_type' => 'required|string',
        ]);
        
        $file = $request->file('file');
        $user = $request->user();
        
        // İlgili model sınıfını al
        $attachableType = $request->attachable_type;
        $attachableId = $request->attachable_id;
        
        // Model örneğini bul
        $model = $attachableType::findOrFail($attachableId);
        
        // İlgili modeli yetkilendirme
        if (method_exists($this, 'authorizeAttach')) {
            $this->authorizeAttach($model);
        }
        
        // Dosyayı kaydet
        $attachment = new Attachment();
        $attachment->file_name = $file->getClientOriginalName();
        $attachment->file_path = $file->store('ticket_attachments', 'public');
        $attachment->file_type = $file->getMimeType();
        $attachment->file_size = $file->getSize();
        
        // Kullanıcı ve ilişkiyi ekle
        $attachment->user()->associate($user);
        $model->attachments()->save($attachment);
        
        // AJAX isteği ise JSON yanıt
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'attachment' => $attachment
            ]);
        }
        
        return back()->with('success', 'Dosya başarıyla yüklendi.');
    }
    
    /**
     * Dosyayı indir
     */
    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment);
        
        $path = Storage::disk('public')->path($attachment->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'Dosya bulunamadı');
        }
        
        return Response::download($path, $attachment->file_name);
    }
    
    /**
     * Dosyayı sil
     */
    public function destroy(Attachment $attachment): RedirectResponse
    {
        $this->authorize('delete', $attachment);
        
        // Dosyayı diskten sil
        Storage::disk('public')->delete($attachment->file_path);
        
        // Veritabanından sil
        $attachment->delete();
        
        return back()->with('success', 'Dosya başarıyla silindi.');
    }
} 