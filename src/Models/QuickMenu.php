<?php

namespace Lazyou\Quick\Models;

class QuickMenu extends QuickBase
{
    protected $table = 'quick_menu';

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'url',
        'icon',
        'sort',
    ];
}
