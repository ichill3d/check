<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    protected $fillable = ['team_id', 'inviter_id', 'invited_email',  'message', 'token', 'accepted_at', 'declined_at', 'sent_on'];
    public function team() {
        return $this->belongsTo(Team::class);
    }
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
