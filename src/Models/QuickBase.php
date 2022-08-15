<?php


namespace Lazyou\Quick\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickBase extends Model
{
    use HasFactory;
    use SoftDeletes;

    // 是否允许
    public const ALLOW_NO = 1;

    public const ALLOW_YES = 2;

    public const ALLOW_OPTIONS = [
        self::ALLOW_YES => '允许',
        self::ALLOW_NO => '不允许',
    ];

    // 是否允许
    public const IS_NO = 1;

    public const IS_YES = 2;

    public const IS_OPTIONS = [
        self::IS_YES => '是',
        self::IS_NO => '否',
    ];

    public const STATUS_ENABLE = 1;

    public const STATUS_DISABLE = 2;

    public const STTUS_OPTIONS = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用',
    ];

    // 公用状态 -- 常用于Command任务
    public const COMMON_STATUS_WAIT = 1;

    public const COMMON_STATUS_ING = 2;

    public const COMMON_STATUS_DONE = 3;

    public const COMMON_STATUS_ERROR = 4;

    public const COMMON_STATUS_OPTIONS = [
        self::COMMON_STATUS_WAIT => '未开始',
        self::COMMON_STATUS_ING => '进行中',
        self::COMMON_STATUS_DONE => '已完成',
        self::COMMON_STATUS_ERROR => '出错了',
    ];

    // element-ui tag
    public const TAG_DEFAULT = '';         // 蓝

    public const TAG_SUCCESS = 'success'; // 绿

    public const TAG_INFO = 'info';        //灰

    public const TAG_WARNING = 'warning'; // 黄

    public const TAG_DANGER = 'danger';    // 红

    // 除了id都允许填充
    protected $guarded = ['id'];

    // 日期格式化
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
