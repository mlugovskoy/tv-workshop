<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->created_at)->format('d.m.y h:s')
        );
    }

    public function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->updated_at)->format('d.m.y h:s')
        );
    }

    protected static function booted(): void
    {
        static::saving(fn(Client $client) => $client->name_search = mb_strtolower($client->name));
    }
}
