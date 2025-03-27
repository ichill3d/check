<?php

namespace App\Jobs;

use App\Models\Checklist;
use App\Models\ChecklistItemActivity;
use App\Models\User;
use App\Notifications\ChecklistCreatedByTeamMember;
use App\Notifications\ChecklistItemSummaryNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendCheckedItemSummary implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $checklistsWithPending = ChecklistItemActivity::where('notified', false)
            ->where('action', 'checked')
            ->with(['checklistItem', 'checklistItem.checklist'])
            ->get()
            ->groupBy('checklist_id');

        if ($checklistsWithPending->isEmpty()) {
            return;
        }

        $checklists = Checklist::whereIn('id', $checklistsWithPending->keys())
            ->get()
            ->keyBy('id');

        foreach ($checklistsWithPending as $checklistId => $activities) {
            $checklist = $checklists->get($checklistId);

            if (! $checklist || $activities->isEmpty()) {
                continue;
            }

            $users = $checklist->team->allUsersQuery()
                ->with('notificationPreferences.channel')
                ->get();

            if ($users->isEmpty()) {
                continue;
            }

            Notification::send($users, new ChecklistItemSummaryNotification($checklist, $activities));
            ChecklistItemActivity::whereIn('id', $activities->pluck('id'))->update(['notified' => true]);
        }
    }

}
