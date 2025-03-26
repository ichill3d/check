<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    public function preferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }
}
