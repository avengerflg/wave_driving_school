<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read');
    }
    
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }
    
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted');
    }
    
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        
        return redirect()->back()->with('success', 'All notifications deleted');
    }
    
    public function redirect($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // Extract the URL from the notification data
        $url = $notification->data['link'] ?? '/dashboard';
        
        return redirect($url);
    }
}