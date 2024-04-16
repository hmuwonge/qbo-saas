<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoSyncActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_id',
        'msg_category',
        'message',
        'server_response',
    ];

    protected $casts = [
        'server_response' => 'json',
    ];

    protected $appends = ['error_message', 'activity_time', 'short_server_response', 'full_server_response'];

    public function autoSync()
    {
        return $this->belongsTo(AutoSync::class, 'sync_id', 'id');
    }

    public function getSyncCategoryAttribute()
    {
        if (isset($this->autoSync)) {
            return $this->autoSync->sync_category;
        }

        return null;
    }

    public function getActivityTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getErrorMessageAttribute()
    {
        return $this->message."<br/><badge class='bg-green-700 rounded-sm p-1'>".strtoupper($this->sync_category).'</badge>';
    }

    public function getShortServerResponseAttribute()
    {
        //        dd($this->server_response);
        if (isset($this->server_response) && (! is_string($this->server_response))) {
            return $this->server_response['status']['returnMessage'];
        }

        // if (isset($this->server_response) && (is_string($this->server_response))) {
        //     $decode = json_decode($this->server_response,true);
        //     return $decode['status']['returnMessage'];
        // }
        if (isset($this->server_response) && is_string($this->server_response)) {
            $decode = json_decode($this->server_response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($decode['status']['returnMessage'])) {
                    return $decode['status']['returnMessage'];
                } else {
                    // Handle the case where 'returnMessage' key is missing
                    return "returnMessage key not found in JSON response";
                }
            } else {
                // Handle JSON decoding error
                return "JSON decoding error: " . json_last_error_msg();
            }
        } else {
            // Handle the case where $this->server_response is null or not a string
            return "Invalid server response";
        }

        return null;

    }

    public function getFullServerResponseAttribute()
    {
        if (isset($this->server_response) && (! is_string($this->server_response))) {
            return $this->server_response['status']['returnMessage']."<br/><badge class='bg-red-500 rounded-sm p-1'>ERROR CODE: ".$this->server_response['status']['returnCode'].'</badge>';
        }

        return null;

    }
}
