<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairStatusHistory extends Model
{
    public const null UPDATED_AT = null;

    protected $fillable = [
        'repair_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RepairStatus::class,
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }
}
