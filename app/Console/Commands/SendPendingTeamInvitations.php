<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeamInvitation;
use App\Jobs\SendTeamInvitationEmail;

class SendPendingTeamInvitations extends Command
{
    protected $signature = 'team:send-pending-invitations';
    protected $description = 'Queue email jobs for all unsent team invitations';

    public function handle()
    {
        $invitations = TeamInvitation::whereNull('sent_on')->get();

        foreach ($invitations as $invitation) {
            SendTeamInvitationEmail::dispatch($invitation);
            $this->info("Queued invitation for {$invitation->invited_email}");
        }

        $this->info("Done. Total: {$invitations->count()} invitations queued.");
    }
}
