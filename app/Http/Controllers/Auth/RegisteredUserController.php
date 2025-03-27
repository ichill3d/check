<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($token = session('invite_token')) {
            session()->forget('invite_token');
            return redirect()->route('team.invitations.accept', ['token' => $token]);
        }

        foreach (NotificationType::all() as $type) {
            foreach (NotificationChannel::all() as $channel) {
                $exists = NotificationPreference::where([
                    'user_id' => $user->id,
                    'notification_type_id' => $type->id,
                    'notification_channel_id' => $channel->id,
                ])->exists();

                if (!$exists) {
                    NotificationPreference::create([
                        'user_id' => $user->id,
                        'notification_type_id' => $type->id,
                        'notification_channel_id' => $channel->id,
                        'enabled' => true,
                    ]);

                }
            }
        }

        return redirect(route('checklists.index', absolute: false));
    }
}
