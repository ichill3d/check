<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class TeamInvitationController extends Controller
{
    public function accept($token)
    {
        $invitation = TeamInvitation::where('token', $token)->first();

        if (!$invitation || $invitation->accepted_at || $invitation->declined_at) {
            abort(410, 'This invitation link has already been used or expired.');
        }

        if (!auth()->check()) {
            session(['invite_token' => $token]);
            $checkUser = User::where('email', $invitation->invited_email)->first();
            if($checkUser) {
                return redirect()->route('login');
            } else {
                session(['invited_email' => $invitation->invited_email]);
                return redirect()->route('register');
            }
        }
        $user = auth()->user();
        if($user->email === $invitation->invited_email) {
            $invitation->team->users()->attach($user->id);
            $user->update([
                'current_team_id' => $invitation->team->id,
            ]);
            $invitation->update([
                'accepted_at' => now(),
            ]);
            return redirect()->route('teams.index')->with('success', 'You’ve joined the team!');
        } else {
            abort(403, 'You cannot accept this invitation.');
        }
    }
}
