<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStages extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural form of the model name
    protected $table = 'application_stageses'; 

    // Specify which attributes are mass-assignable
    protected $fillable = [
        // List your fillable attributes here
        'name',
        'is_active',
        // Add other attributes as needed
    ];

    // If you want to disable timestamps
    // public $timestamps = false;

    // If you want to use custom timestamps
    // const CREATED_AT = 'creation_date';
    // const UPDATED_AT = 'last_update';
}