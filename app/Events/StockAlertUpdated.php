<?php

namespace App\Events;

use App\Models\Stock;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAlertUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stock;
    public $oldQuantity;
    public $newQuantity;
    public $oldAlert;
    public $newAlert;
    public $type; // 'alert' o 'quantity'

    /**
     * Create a new event instance.
     *
     * @param Stock $stock
     * @param int $oldQuantity
     * @param int $newQuantity
     * @param int $oldAlert
     * @param int $newAlert
     * @param string $type
     */
    public function __construct(Stock $stock, $oldQuantity = null, $newQuantity = null, $oldAlert = null, $newAlert = null, $type = 'alert')
    {
        $this->stock = $stock;
        $this->oldQuantity = $oldQuantity;
        $this->newQuantity = $newQuantity;
        $this->oldAlert = $oldAlert;
        $this->newAlert = $newAlert;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}