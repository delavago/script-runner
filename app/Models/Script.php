<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    protected $fillable = [
        'script_name',
        'description',
        'file_name',
        'file_type',
        'file_path',
        'active',
    ];
}
