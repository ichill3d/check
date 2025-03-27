<?php

namespace App\Notifications;

use App\Models\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChecklistItemSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $activities;
    public $checklist;

    protected string $notificationTypeKey = 'checkitem_summary';
    protected int $notificationTypeId;

    protected string $message;

    public function __construct($checklist, $activities)
    {
        $this->checklist = $checklist;
        $this->activities = $activities;
        $this->notificationTypeId = $this->getNotificationTypeId();
        $this->message = $this->getMessage();
    }

    private function getMessage(): string {
       $content = "The following checklist items have been completed:";
       foreach ($this->summaryLines() as $line) {
           $content .= $line . "\n";
       }
       return $content;
    }
    private function getNotificationTypeId(): int
    {
        return cache()->rememberForever("notification_type_id:{$this->notificationTypeKey}", function () {
            return NotificationType::where('key', $this->notificationTypeKey)->value('id');
        });
    }

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
        $mail = (new MailMessage)
            ->subject("Checklist '{$this->checklist->title}' has recently checked off items")
            ->line("Checklist '{$this->checklist->title}' has recently checked off items:")
            ->line('');

        foreach ($this->summaryLines() as $line) {
            $mail->line($line);
        }

        $mail->action('View Checklist', route('checklists.show', $this->checklist->id));

        return $mail;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => "{$this->checklist->title} has new completed items.",
            'message' => "{$this->checklist->title} has new completed items.",
            'summary' => $this->summaryLines(),
            'checklist_id' => $this->checklist->id ?? null,
        ];
    }

    private function summaryLines(): array
    {
        return $this->activities->map(function ($activity) {
            return '✅ ' . optional($activity->checklistItem)->content;
        })->toArray();
    }
}
