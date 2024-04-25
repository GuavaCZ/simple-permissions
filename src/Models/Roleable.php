<?php

namespace Guava\SimplePermissions\Models;

use Guava\SimplePermissions\Casts\RoleCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Roleable extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'role',
        'targettable_type',
        'targettable_id',
    ];

    protected $casts = [
        'role' => RoleCast::class,
    ];

    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    public function targettable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTable()
    {
        return config('simple-permissions.tables.roles', parent::getTable());
    }
}
