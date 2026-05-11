<?php

namespace Zizaco\Entrust\Traits;

/**
 * This file is part of Entrust,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Zizaco\Entrust
 */

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

trait EntrustPermissionTrait
{
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.permission_role_table'), Config::get('entrust.permission_foreign_key'), Config::get('entrust.role_foreign_key'));
    }

    /**
     * Boot the permission model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the permission model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootEntrustPermissionTrait()
    {
        static::deleting(function ($permission) {
            if (! static::entrustUsesSoftDeletes($permission)) {
                $permission->roles()->sync([]);
            }

            return true;
        });
    }

    protected static function entrustUsesSoftDeletes(object $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model::class), true);
    }
}
