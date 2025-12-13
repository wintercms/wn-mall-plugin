<?php

namespace Winter\Mall\Models;

use Winter\Storm\Database\Pivot;
use Winter\Storm\Database\Traits\Nullable;

class PropertyGroupProperty extends Pivot
{
    use Nullable;

    public $nullable = ['filter_type'];

    public $casts = [
        'use_for_variants'
    ];

    public static function getFilterTypeOptions($dashes = true)
    {
        return [
            null    => ($dashes ? '-- ' : '') . trans('winter.mall::lang.properties.filter_types.none'),
            'set'   => trans('winter.mall::lang.properties.filter_types.set'),
            'range' => trans('winter.mall::lang.properties.filter_types.range'),
        ];
    }
}
