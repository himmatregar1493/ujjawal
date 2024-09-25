<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class applicationComment extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Define fillable attributes
    protected $fillable = [
        'comment', 
        'sender_id', 
        'sender_name', 
        'application_id', 
        'comment_type', 
        'created_at', 
        'updated_at'
    ];
    public function save(array $options = [])
    {
        // Set default project time
        $projectTime = Carbon::now(); // Adjust this to your project's specific time zone if needed
        
        // Set created_at only if the record is new
        if (!$this->exists) {
            $this->created_at = $projectTime;
        }
        // Always set updated_at
        $this->updated_at = $projectTime;

        return parent::save($options);
    }
}
