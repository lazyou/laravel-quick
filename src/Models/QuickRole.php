<?php


namespace Lazyou\Quick\Models;

class QuickRole extends QuickBase
{
    protected $table = 'quick_role';

    protected $fillable = [
        'user_id',
        'name',
        'remark',
    ];

    // TODO: 单模型才支持 setAppends() ?
    protected $appends = [
        'permission_ids',
    ];

    public function permissions()
    {
        return $this->hasMany(QuickRolePermission::class, 'role_id');
    }

    public function getPermissionIdsAttribute()
    {
        return $this->permissions->pluck('permission_id');
    }
}
