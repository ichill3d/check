<?php

namespace App\Http\Controllers;

use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use App\Models\NotificationType;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function settings() {
        $user = auth()->user();
        $notificationTypes = NotificationType::all();
        $notificationChannels = NotificationChannel::all();
        $notificationPreferences = $user->notificationPreferences;
        foreach($notificationPreferences as $notificationPreference) {
            $prefs[$notificationPreference->type->key . "-" . $notificationPreference->channel->key] =
                [
                    'id' => $notificationPreference->id,
                    'enabled' => (bool)$notificationPreference->enabled
                ];
        }
        return inertia('Notifications/Settings', [
            'notificationPrefs' => $prefs ?? [],
            'notificationTypes' => $notificationTypes,
            'notificationChannels' => $notificationChannels,
        ]);
    }
    public function settingsToggle($notificationPreferenceId) {
        $user = auth()->user();
        $notificationPreference = NotificationPreference::find($notificationPreferenceId);
        if($user->id === $notificationPreference->user_id) {
            $notificationPreference->update(['enabled' => !$notificationPreference->enabled]);
        }
        return back();

    }
}
