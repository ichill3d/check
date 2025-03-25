<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['content', 'is_done', 'checklist_id'];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}
