<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = Notification::visibleTo(Auth::user())->latest();

        if ($filter === 'unread') {
            $notifications = $query->where('is_read', false)->get();
        } else {
            $notifications = $query->get(); // ✅ no pagination here
        }

        // Time references
        $now = now();
        $twoHoursAgo = $now->copy()->subHours(2);
        $oneDayAgo   = $now->copy()->subDay();

        // ✅ Group by time
        $new = $notifications->filter(fn($n) =>
            $n->created_at->greaterThanOrEqualTo($twoHoursAgo)
        );

        $today = $notifications->filter(fn($n) =>
            $n->created_at->lessThan($twoHoursAgo) &&
            $n->created_at->greaterThanOrEqualTo($oneDayAgo)
        );

        $earlier = $notifications->filter(fn($n) =>
            $n->created_at->lessThan($oneDayAgo)
        );

        return view('notifications.index', [
            'notifications'        => $notifications,
            'newNotifications'     => $new,
            'todayNotifications'   => $today,
            'earlierNotifications' => $earlier,
            'filter'               => $filter,
        ]);
    }
    public function dropdown(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query  = Notification::visibleTo(Auth::user())->latest();

        $notifications = $filter === 'unread'
            ? $query->where('is_read', false)->take(20)->get()
            : $query->take(20)->get();

        $unreadCount = Notification::visibleTo(Auth::user())
            ->where('is_read', false)
            ->count();

        // Time references
        $now = now();
        $twoHoursAgo = $now->copy()->subHours(2);
        $oneDayAgo   = $now->copy()->subDay();

        // ✅ Group by time
        $new = $notifications->filter(fn($n) =>
            $n->created_at->greaterThanOrEqualTo($twoHoursAgo)
        );

        $today = $notifications->filter(fn($n) =>
            $n->created_at->lessThan($twoHoursAgo) &&
            $n->created_at->greaterThanOrEqualTo($oneDayAgo)
        );

        $earlier = $notifications->filter(fn($n) =>
            $n->created_at->lessThan($oneDayAgo)
        );

        return view('notifications.partials.dropdown', [
            'newNotifications'     => $new,
            'todayNotifications'   => $today,
            'earlierNotifications' => $earlier,
            'filter'               => $filter,
            'unreadCount'          => $unreadCount,
        ]);
    }
    public function markAsRead(Notification $notification)
    {
        // Make sure the user can actually see this notification
        if ($notification->user_id === Auth::id() || 
            ($notification->user_id === null && Auth::user()->can($notification->required_permission))) 
        {
            $notification->update(['is_read' => true]);

            // Always redirect to the correct destination based on type
            return redirect($notification->url ?? route('notifications.index'));
        }

        return back();
    }

}