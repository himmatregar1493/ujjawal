<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'applications';

    // Specify the primary key
    protected $primaryKey = 'id';

    // Indicate that the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the key type if it's not the default integer
    protected $keyType = 'int';

    // Enable timestamps
    public $timestamps = true;

    // Customize the names of the timestamp columns if they differ from the defaults
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'course_id',
        'temp_application_submittion',
    ];
}