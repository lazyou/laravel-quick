<?php

namespace Lazyou\Quick\Models;

class QuickOperationLog extends QuickBase
{
    protected $table = 'quick_operation_log';

    protected $fillable = [
        'user_id',
        'as',
        'ip',
        'permission_id',
        'url',
        'method',
        'body',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'body_format',
    ];

    /**
     * 操作人.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(QuickUser::class, 'user_id', 'id');
    }

    /**
     * 权限.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function permission()
    {
        return $this->belongsTo(QuickPermission::class, 'permission_id', 'id');
    }

    public function getBodyFormatAttribute()
    {
        if ($this->body) {
            return json_encode(json_decode($this->body), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return '';
    }
}
