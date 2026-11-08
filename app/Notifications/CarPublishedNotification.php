<?php

namespace App\Notifications;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CarPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Car $car)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->car->loadMissing(['maker', 'model']);

        $title = $this->car->year.' '.$this->car->maker->name.' '.$this->car->model->name;

        return (new MailMessage)
            ->subject('Your car is now published')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($title.' is now published at a price of '.number_format($this->car->price).'.')
            ->action('View your car', route('car.show', $this->car))
            ->line('Thank you for using '.config('app.name').'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'car_id' => $this->car->id,
        ];
    }
}
