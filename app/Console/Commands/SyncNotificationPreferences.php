<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\NotificationType;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use Illuminate\Console\Command;

class SyncNotificationPreferences extends Command
{
    protected $signature = 'sync:notification-preferences {--dry-run : Preview what would be created without saving}';
    protected $description = 'Ensure all users have preferences for every type and channel';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $count = 0;

        $this->info($dryRun ? 'Dry run: No records will be created.' : 'Syncing notification preferences...');

        foreach (User::all() as $user){
            foreach (NotificationType::all() as $type) {
                foreach (NotificationChannel::all() as $channel) {
                    $exists = NotificationPreference::where([
                        'user_id' => $user->id,
                        'notification_type_id' => $type->id,
                        'notification_channel_id' => $channel->id,
                    ])->exists();

                    if (!$exists) {
                        $count++;
                        $this->line("Missing for user #$user->id, type #$type->id, channel #$channel->id");

                        if (!$dryRun) {
                            NotificationPreference::create([
                                'user_id' => $user->id,
                                'notification_type_id' => $type->id,
                                'notification_channel_id' => $channel->id,
                                'enabled' => true,
                            ]);
                        }
                    }
                }
            }
        }

        $this->info($dryRun ? "Dry run complete. {$count} combinations missing." : "Sync complete. {$count} new preferences created.");
    }
}
