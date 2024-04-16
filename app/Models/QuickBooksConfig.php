<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickBooksConfig extends Model
{
    use HasFactory;

    //    protected $table = 'config_db.quick_books_configs';
    //    protected $connection = 'config_db';

    protected $fillable = [
        'user_id',
        'company_id',
        'cut_off_date',
        'auth_token',
        'refresh_token',
        'auth_expiry',
        'refresh_token_expiry',
        'exempt',
        'standard',
        'zero_rated',
        'deemed',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
