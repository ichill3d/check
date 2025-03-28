<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItemActivity;
use Illuminate\Http\Request;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Notifications\ChecklistCreatedByTeamMember;

class ChecklistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        if($user->id === $request->user()->id) {
            $currentTeam = $request->user()->currentTeam ?? null;
            $validated['team_id'] = $currentTeam ? $currentTeam->id : null;
            $newList = $request->user()->checklists()->create($validated);
            $newList->load('user', 'team');

            if($currentTeam) {
                $users = $currentTeam->allUsersQuery()
                    ->where('users.id', '!=', $user->id)
                    ->with('notificationPreferences.channel')->get();
                Notification::send($users, new ChecklistCreatedByTeamMember($newList));
            }

            return redirect()->route('checklists.show', $newList->id);
        } else {
            abort(403, 'You cannot create a checklist for another user.');
        }
    }
    public function index()
    {
        //$checklists = Auth::user()->checklists()->latest()->get();

        $user = auth()->user();
        if($user->currentTeam) {
            $checklists = $user->currentTeam->checklists()->latest()->get();
        } else {
            $checklists = $user->checklists()->whereNull('team_id')->latest()->get();
        }

        return Inertia::render('Checklists/Index', [
            'checklists' => $checklists,
        ]);
    }
    public function removeChecklist(int $id)
    {
        $checklist = Checklist::findOrFail($id);
        Gate::authorize('delete', $checklist);
        $checklist->delete();
        return redirect()->route('checklists.index');
    }
    public function update(Request $request, int $id)
    {
        $checklist = Checklist::findOrFail($id);
        Gate::authorize('update', $checklist);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist->update($validated);

        return redirect()->back()->with('success', 'Item added to checklist.');
    }
    public function show(int $id)
    {
        $checklist = Checklist::with('items')->findOrFail($id);
        return Inertia::render('Checklists/Show', [
            'checklist' => $checklist,
        ]);
    }
    public function addItem(Request $request, Checklist $checklist)
    {
        Gate::authorize('toggleChecklistItem', $checklist);
        $validated = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $checklist->items()->create($validated);

        return redirect()->back()->with('success', 'Item added to checklist.');
    }
    public function toggleItem(Request $request, ChecklistItem $item)
    {
        $checklist = $item->checklist;
        Gate::authorize('toggleChecklistItem', $checklist);
        $item->is_done = !$item->is_done;
        if($item->checklist->team && $item->is_done) {
            ChecklistItemActivity::create([
                'user_id' => auth()->id(),
                'checklist_id' => $checklist->id,
                'checklist_item_id' => $item->id,
                'action' => 'checked',
                'created_at' => now(),
            ]);
        }
        $item->save();

        return back()->with('success', 'Item toggled.');
    }
    public function removeItem(ChecklistItem $item) {
        $checklist = $item->checklist;
        Gate::authorize('update', $checklist);
        $item->delete();
        return back()->with('success', 'Item deleted.');
    }
}
