<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    protected $table = 'company_info';

    protected $fillable = [
        'CompanyName',
        'LegalName',
        'CustomerCommunicationAddr',
        'LegalAddr',
        'CustomerCommunicationEmailAddr',
        'PrimaryPhone',
        'Email',
        'CompanyStartDate',
        'Country',
        'SupportedLanguages',
        'NameValue',
        'domain',
        'MetaData',
        'FiscalYearStartMonth',
        'CompanyId',
        // 'WebAddr'
    ];

    protected $casts = [
        'CompanyAddr' => 'array',
        'CustomerCommunicationAddr' => 'array',
        'LegalAddr' => 'array',
        'CustomerCommunicationEmailAddr' => 'array',
        'PrimaryPhone' => 'array',
        'Email' => 'array',
        'MetaData' => 'array',
    ];
}
