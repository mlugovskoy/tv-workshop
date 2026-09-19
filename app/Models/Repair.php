<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repair extends Model
{
    protected $fillable = [
        'client_id',
        'device_id',
        'status',
        'problem_description',
        'diagnosis',
        'repair_description',
        'estimated_price',
        'final_price',
        'received_at',
        'completed_at',
        'issued_at',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    protected function casts(): array
    {
        return [
            'status' => RepairStatus::class,
            'estimated_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'received_at' => 'datetime',
            'completed_at' => 'datetime',
            'issued_at' => 'datetime',
        ];
    }
}
