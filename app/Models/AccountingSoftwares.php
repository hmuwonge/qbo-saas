<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingSoftwares extends Model
{
    use HasFactory;

    protected $fillable = [
        'software_name',
    ];

    /**
     * Get the clients that owns the AcoountingSoftwares
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function efris_clients()
    {
        return $this->hasMany(EfrisClient::class, 'accounting_software');
    }
}
