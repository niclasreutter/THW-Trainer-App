<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::with('user')->latest();
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }
        
        // Filter by hermine contact
        if ($request->filled('hermine')) {
            $query->where('hermine_contact', true);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('vorname', 'like', "%{$search}%")
                  ->orWhere('nachname', 'like', "%{$search}%");
            });
        }
        
        $messages = $query->paginate(20)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'today' => ContactMessage::whereDate('created_at', today())->count(),
            'this_week' => ContactMessage::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
        
        return view('admin.contact-messages.index', compact('messages', 'stats'));
    }
    
    /**
     * Display the specified message.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read
        if (!$contactMessage->is_read) {
            $contactMessage->markAsRead();
        }
        
        return view('admin.contact-messages.show', compact('contactMessage'));
    }
    
    /**
     * Mark message as read.
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();
        
        return back()->with('success', '✅ Nachricht als gelesen markiert.');
    }
    
    /**
     * Mark message as unread.
     */
    public function markAsUnread(ContactMessage $contactMessage)
    {
        $contactMessage->update([
            'is_read' => false,
            'read_at' => null,
        ]);
        
        return back()->with('success', '✅ Nachricht als ungelesen markiert.');
    }
    
    /**
     * Delete the specified message.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        
        return back()->with('success', '✅ Nachricht wurde gelöscht.');
    }
    
    /**
     * Bulk delete messages.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_messages,id',
        ]);
        
        ContactMessage::whereIn('id', $request->ids)->delete();
        
        return back()->with('success', '✅ ' . count($request->ids) . ' Nachrichten wurden gelöscht.');
    }
}
