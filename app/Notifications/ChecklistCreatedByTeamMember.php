<?php

namespace App\Notifications;

use App\Models\Checklist;
use App\Models\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistCreatedByTeamMember extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationTypeKey = 'checklist_created';
    protected int $notificationTypeId;

    protected Checklist $checklist;

    /**
     * Create a new notification instance.
     */
    public function __construct($checklist)
    {
        $this->notificationTypeId = $this->getNotificationTypeId();
        $this->checklist = $checklist;
    }

    private function getNotificationTypeId(): int
    {
        return cache()->rememberForever("notification_type_id:{$this->notificationTypeKey}", function () {
            return NotificationType::where('key', $this->notificationTypeKey)->value('id');
        });
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $prefs = $notifiable->notificationPreferences
            ->filter(fn($pref) =>
                $pref->notification_type_id === $this->notificationTypeId &&
                $pref->enabled === 1
            );
        return $prefs->pluck('channel.key')->toArray();

    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('A New Checklist Was Created')
            ->line("{$this->checklist->user->name} created a checklist.")
            ->action('View Checklist', route('checklists.show', $this->checklist->id));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => "{$this->checklist->user->name} created a checklist.",
            'message' => 'You have a new checklist to review.',
            'checklist_id' => $this->checklist->id ?? null,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
