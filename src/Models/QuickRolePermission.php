<?php


namespace Lazyou\Quick\Models;

class QuickRolePermission extends QuickBase
{
    protected $table = 'quick_role_permission';

    protected $fillable = [
        'role_id',
        'permission_id',
    ];
}
