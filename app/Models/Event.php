<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = ['venue_id', 'title', 'description', 'starts_at'];

    protected $casts = ['starts_at' => 'datetime'];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}