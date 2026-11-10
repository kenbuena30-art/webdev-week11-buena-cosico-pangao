<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $fillable = ['notable_id', 'notable_type', 'body'];

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}