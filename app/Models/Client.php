<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'comment'
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    protected static function booted(): void
    {
        static::saving(fn(Client $client) => $client->name_search = mb_strtolower($client->name));
    }
}
