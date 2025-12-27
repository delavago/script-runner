<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
{

    use HasFactory, HasUuids;

    protected $fillable = [
        'script_name',
        'description',
        'file_type',
        'attachment',
        'active',
        'use_credentials',
        'credential_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachment' => 'array',
        ];
    }

    public function executionLogs()
    {
        return $this->hasMany(ExecutionLog::class);
    }
}
