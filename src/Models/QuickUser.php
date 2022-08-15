<?php


namespace Lazyou\Quick\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * config/auth.php 修改 providers 下 users 的 model 为 Lazyou\Quick\Models\QuickUser::class
 *
 * Class QuickUser
 * @package Lazyou\Quick\Models
 */
class QuickUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const STATUS_ENABLE = 1;

    public const STATUS_DISABLE = 2;

    public const STTUS_OPTIONS = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用',
    ];

    /**
     * 当前角色权限.
     */
    public static array $rolePermissions = [];

    protected $table = 'quick_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'status',
        'is_admin',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin === 1;
    }

    /**
     * 权限判断.
     * @param $as
     * @return bool
     */
    public function hasPermission($as): bool
    {
        if ($this->isAdmin() || empty($as)) {
            return true;
        }

        if (empty(self::$rolePermissions)) {
            $permissionIds = QuickRolePermission::query()
                ->where('role_id', $this->role_id)
                ->get()
                ->pluck('permission_id')
                ->toArray();

            if (empty($permissionIds)) {
                return false;
            }

            self::$rolePermissions = QuickPermission::query()
                ->whereIn('id', $permissionIds)
                ->where('type', QuickPermission::TYPE_PERMISSION)
                ->get()
                ->pluck('as')
                ->toArray();
        }

        return in_array($as, self::$rolePermissions);
    }
}
