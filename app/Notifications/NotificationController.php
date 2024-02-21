<?php

namespace App\Notifications;

use Illuminate\Http\Request;

class NotificationController 
{
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
    
        if ($notification) {
            $notification->delete();
            return redirect()->back()->with('success', 'Notificación eliminada correctamente.');
        } else {
            return redirect()->back()->with('error', 'La notificación no pudo ser encontrada.');
        }
    }


}