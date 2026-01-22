<?php

namespace App\Listeners;

use App\Events\StockAlertUpdated;
use App\Models\User;
use App\Notifications\StockAlertNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendStockAlertNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  StockAlertUpdated  $event
     * @return void
     */
    public function handle(StockAlertUpdated $event)
    {
        // Obtener el centro de stock y su operación
        $stock = $event->stock;
        $stockcenter = $stock->stockCenter;

        // Si no hay centro de stock, salir
        if (!$stockcenter || !$stockcenter->id_operation) {
            return;
        }

        // Obtener el nombre del permiso de la operación (asumiendo que el permiso se llama igual que la operación)
        $permissionName = $stockcenter->Operation->name; // Ajusta según tu convención de nombres

        // Buscar usuarios que tengan este permiso
        $users = User::permission($permissionName)->get();

        // Si no hay usuarios, salir
        if ($users->isEmpty()) {
            return;
        }

        // Determinar el mensaje según el tipo de actualización
        $message = $this->generateMessage($event);

        // Enviar notificación a los usuarios con permiso
        Notification::send($users, new StockAlertNotification($stock, $message));
    }

    /**
     * Generar el mensaje de la alerta según el tipo de actualización.
     *
     * @param StockAlertUpdated $event
     * @return string
     */
    private function generateMessage(StockAlertUpdated $event): string
    {
        $stock = $event->stock;
        $articleName = $stock->article->name ?? 'Artículo desconocido';
        $stockcenterName = $stock->stockCenter->name ?? 'Centro desconocido';

        if ($event->type === 'alert') {
            return "Se actualizó el límite de alerta para el artículo '{$articleName}' en el centro '{$stockcenterName}'. " .
                   "Nuevo límite: {$event->newAlert}. Stock actual: {$stock->quantity}.";
        } elseif ($event->type === 'quantity') {
            $change = $event->newQuantity - $event->oldQuantity;
            $action = $change > 0 ? 'incrementó' : 'decrementó';
            return "El stock del artículo '{$articleName}' en el centro '{$stockcenterName}' se {$action} en " . abs($change) . ". " .
                   "Stock actual: {$event->newQuantity}. Límite de alerta: {$stock->quantity_alert}.";
        }

        return "Se actualizó el artículo '{$articleName}' en el centro '{$stockcenterName}'.";
    }
}