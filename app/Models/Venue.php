<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Venue extends Model
{
    protected $fillable = ['name', 'address', 'capacity'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}