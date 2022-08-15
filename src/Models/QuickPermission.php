<?php


namespace Lazyou\Quick\Models;

class QuickPermission extends QuickBase
{
    public const TYPE_MENU = 1;

    public const TYPE_PERMISSION = 2;

    public const TYPE_OPTIONS = [
        self::TYPE_MENU => '菜单',
        self::TYPE_PERMISSION => '权限',
    ];

    protected $table = 'quick_permission';

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'url',
        'icon',
        'as',
        'controller',
        'type',
        'deep', // 深度
        'sort',
        'status',
    ];

    /**
     * 父级菜单.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parent()
    {
        return $this->belongsTo(QuickPermission::class, 'parent_id', 'id');
    }
}
