<?php

namespace App\Notifications;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $stock;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param Stock $stock
     * @param string $message
     */
    public function __construct(Stock $stock, string $message)
    {
        $this->stock = $stock;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // También puedes agregar 'mail' si quieres
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->message)
                    ->action('Ver Stock', url('/stock/' . $this->stock->id))
                    ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'stock_id' => $this->stock->id,
            'article_id' => $this->stock->id_article,
            'article_name' => $this->stock->article->name ?? 'N/A',
            'stockcenter_id' => $this->stock->id_stockcenter,
            'stockcenter_name' => $this->stock->stockCenter->name ?? 'N/A',
            'message' => $this->message,
            'quantity' => $this->stock->quantity,
            'quantity_alert' => $this->stock->quantity_alert,
            'url' => '/stock/' . $this->stock->id,
        ];
    }
}