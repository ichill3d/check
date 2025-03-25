<?php

namespace App\Jobs;

use App\Mail\TeamInvitationMail;
use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTeamInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TeamInvitation $invitation;
    /**
     * Create a new job instance.
     */
    public function __construct(TeamInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->invitation->invited_email)
            ->send(new TeamInvitationMail($this->invitation));

        $this->invitation->update([
            'sent_on' => now(),
        ]);
    }
}
