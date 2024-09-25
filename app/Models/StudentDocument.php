<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id',
        'original_name',
        'stored_name',
        'mime_type',
        'path',
    ];
}
