<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'hostname',
        'rustdesk_id',
        'ip_address',
        'mac_address',
        'operating_system',
        'status',
        'cpu_usage',
        'ram_usage',
        'battery_percentage',
        'last_seen_at',
        'api_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'cpu_usage' => 'float',
            'ram_usage' => 'float',
            'last_seen_at' => 'datetime',
        ];
    }
    protected $hidden = [
        'api_token_hash',
    ];
}
