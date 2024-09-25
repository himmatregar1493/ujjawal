<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    // Ensure the 'id' field is cast as a string (for UUID)
    protected $keyType = 'string';
    public $incrementing = false;

    // Automatically generate UUID for the id field
    protected static function boot()
    {
        parent::boot();

        // Set UUID for id field before creating the model
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
