<?php

namespace Guava\SimplePermissions\Models;

use Guava\SimplePermissions\Casts\PermissionCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Permissionable extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'permission',
        'targettable_type',
        'targettable_id',
    ];

    protected $casts = [
        'permission' => PermissionCast::class,
    ];

    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function targettable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTable()
    {
        return config('simple-permissions.tables.permissions', parent::getTable());
    }
}
