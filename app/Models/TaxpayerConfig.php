<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class TaxpayerConfig extends Model
{
    use HasFactory,SoftDeletes;

    // protected $connection = 'config_db';

    protected $fillable = [
        'tin',
        'is_vat_registered',
        'legal_name',
        'business_name',
        'address',
        'mobile_phone',
        'brn',
        'email',
        'taxpayer_id',
        'environments',
        'device_no',
        'efris_middleware_url',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (App::runningInConsole()) {
            $this->tin = 1001302007;
        } else {
            $this->tin = session('TIN');
        }
    }

    /**
     * Finds Taxpayer Config by TIN
     *
     * @param  string  $tin
     * @return static|null
     */
    public function taxpayerConfig()
    {
        return static::where('tin', $this->tin)->first();
    }
}
