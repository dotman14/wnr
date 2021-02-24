<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAuthUser extends Model
{
    use HasFactory;

    protected $table = 'external_auth';
    public $incrementing = false;
//    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'provider',
        'provider_id',
        'avatar',
        'id',
    ];

}
