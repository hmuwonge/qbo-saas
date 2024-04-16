<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_number',
        'sync_category',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Perform operations before creating the model
            $model->ref_number = (new self())->getNextReferenceNumber();
        });

        static::saving(function ($model) {
            // Perform operations before saving the model
            $model->ref_number = $model->getNextReferenceNumber();
        });
    }

    /**
     * @return mixed
     */
    public static function createAutoSync($category)
    {
        //1. First Delete previous records
        AutoSync::where('sync_category', $category)->delete();

        //2. Remove All Activities for previous activities
        $firstrecord = AutoSync::orderBy('id', 'asc')->first();
        AutoSyncActivity::where('sync_id', '<', $firstrecord->id)->delete();

        //3. Create new instance
        $auto = AutoSync::create(['sync_category' => $category]);
        //        $auto->save();

        return $auto->id;
    }

    /**
     * Number of records in this activity
     *
     * @return int
     */
    public function getNumberOfRecords()
    {
        return $this->hasMany(AutoSyncActivity::class, ['sync_id' => $this->id])->count();
    }

    public function getNumberOfErrors(): int
    {
        return $this->hasMany(AutoSyncActivity::class, ['sync_id' => $this->id])
            ->where(['msg_category' => 'ERROR'])
            ->count();
    }

    /**
     * Last ID in the table
     *
     * @return int
     */
    public function getLastId()
    {
        $lastRecord = $this->orderBy('id', 'desc')->first();
        $num = 0;
        if ($lastRecord) {
            $parts = explode('-', $lastRecord->ref_number);
            // Is it today's record?
            if ($parts[1] == date('md')) {
                $num = intval($parts[2]);
            }
        }

        return $num;
    }

    /**
     * Next Reference Number
     *
     * @return string
     */
    public function getNextReferenceNumber()
    {
        return 'AUTO-'.date('md').'-'.str_pad(($this->id + 1), 3, '0', STR_PAD_LEFT);
    }
}
