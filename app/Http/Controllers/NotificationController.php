<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        $this->middleware('auth');
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return back()->with('success', 'Notificación marcada como leída');
        }
        
        return back()->with('error', 'No se encontró la notificación');
    }

    public function delete(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->find($id);
        
        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notificación eliminada');
        }
        
        return back()->with('error', 'No se pudo eliminar la notificación');
    }

    public function markAllAsRead(): RedirectResponse
    {
        $user = auth()->user();
        
        if ($user->unreadNotifications->isNotEmpty()) {
            $user->unreadNotifications->markAsRead();
            return back()->with('success', 'Todas las notificaciones marcadas como leídas');
        }
        
        return back()->with('info', 'No hay notificaciones por marcar como leídas');
    }

    public function clearAll(): RedirectResponse
    {
        $user = auth()->user();
        
        if ($user->notifications->isNotEmpty()) {
            $user->notifications()->delete();
            return back()->with('success', 'Todas las notificaciones eliminadas');
        }
        
        return back()->with('info', 'No hay notificaciones para eliminar');
    }
}