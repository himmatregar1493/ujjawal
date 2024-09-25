<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApplicationTimeline extends Model
{
    use HasFactory;
    public $timestamps = false;
    // Define the fields that are mass assignable
    protected $fillable = [
        'id',
        'application_id',
        'stage_id',
        'reason',
        'changed_by',
        'created_at',
        'updated_at',
        'type',
        'email_body',
        'email_subject',
        'email_send_to',
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