<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Team extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->uuid)) {
                $team->uuid = Str::uuid();
            }
        });
    }

    protected $fillable = ['name', 'owner_id', 'uuid'];
    public function users() {
        return $this->belongsToMany(User::class);
    }
    public function invitations() {
        return $this->hasMany(TeamInvitation::class);
    }
    public function checklists() {
        return $this->hasMany(Checklist::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function hasUser(User $user) {
        return $this->owner_id === $user->id
            || $this->users()->where('users.id', $user->id)->exists();
    }
}
