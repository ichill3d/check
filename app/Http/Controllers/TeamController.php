<?php

namespace App\Http\Controllers;

use App\Jobs\SendTeamInvitationEmail;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index() {
        $user = auth()->user();
        $teams['owned'] = $user->ownedTeams;
        $teams['member'] = $user->memberTeams;
        return Inertia::render('Teams/Index', [
            'teams' => $teams,
        ]);
    }
    public function create() {
        return Inertia::render('Teams/Create');
    }
    public function show ($id) {
        $team = Team::findOrFail($id);
        $user = auth()->user();
        abort_unless($user->id === $team->owner_id || $team->users->contains($user), 403);

        $user->update([
            'current_team_id' => $team->id,
        ]);

        $invitations = $user->id === $team->owner_id ? $team->invitations->groupBy(function ($invitation) {
            if (is_null($invitation->accepted_at) && is_null($invitation->declined_at)) {
                return 'pending';
            }
            return 'unknown';
        }) : [];
        $members = $team->users;
        $owner = $team->owner;
        return Inertia::render('Teams/Show', [
            'team' => $team,
            'invitations' => $invitations,
            'members' => $members,
            'owner' => $owner,
        ]);
    }
    public function store()
    {
        $validated = request()->validate([
            'name' => 'required|max:255',
            'members' => 'nullable|array',
            'members.*' => 'email|max:255',
        ]);
        $team = auth()->user()->ownedTeams()->create([
            'name' => $validated['name'],
        ]);

        $members = $validated['members'] ?? [];
        foreach ($members as $email) {
            $invitation = TeamInvitation::create([
                'invited_email' => $email,
                'team_id' => $team->id,
                'inviter_id' => auth()->id(),
                'token' => Str::random(40),
                'message' => '', // or fill from request if needed
            ]);
            SendTeamInvitationEmail::dispatch($invitation);
        }

        return redirect()->route('teams.index')->with('success', 'Team created.');
    }

    public function removeMember($teamId, $memberId)
    {
        $team = Team::findOrFail($teamId);

        // Authorization: only owner can remove
        abort_unless(auth()->id() === $team->owner_id, 403);

        $team->users()->detach($memberId);

        return back()->with('success', 'Member removed.');
    }
    public function sendInvitation($teamId)
    {

        $team = Team::findOrFail($teamId);

        logger(auth()->id());
        abort_unless(auth()->id() === $team->owner_id, 403);

        $validated = request()->validate([
            'invited_email' => 'required|email|max:255',
        ]);

        logger($validated);




        if ($team->users()->where('email', $validated['invited_email'])->exists()) {
            return back()->withErrors(['invited_email' => 'This user is already a team member.']);
        }

        $existing = $team->invitations()->where('invited_email', $validated['invited_email'])->whereNull('accepted_at')->first();
        if ($existing) {
            return back()->withErrors(['invited_email' => 'An invitation has already been sent to this email.']);
        }
        logger($teamId);

        $invitation = $team->invitations()->create([
            'invited_email' => $validated['invited_email'],
            'inviter_id' => auth()->id(),
            'token' => Str::random(40),
            'message' => '', // optional
        ]);
        return back()->with('success', 'Invitation sent!');
    }
}
