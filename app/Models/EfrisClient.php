<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EfrisClient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'contact_person_name',
        'address',
        'contact_person_name',
        'contact_person_telephone',
        'email',
        'subscription_start_date',
        'subscription_end_date',
        'safe',
        'alternative_email',
        'tin',
        'branches',
        'accounting_software',
    ];

    /**
     * Get the accountingSoftware that owns the EfrisClient
     *
     * @return BelongsTo
     */
    public function accountingSoftware()
    {
        return $this->belongsTo(AccountingSoftwares::class, 'id');
    }
}
