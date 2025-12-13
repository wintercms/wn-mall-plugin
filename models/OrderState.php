<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\SoftDelete;
use Winter\Storm\Database\Traits\Sortable;
use Winter\Storm\Database\Traits\Validation;

class OrderState extends Model
{
    use Validation;
    use SoftDelete;
    use Sortable;

    public const FLAG_NEW = 'NEW';
    public const FLAG_CANCELLED = 'CANCELLED';
    public const FLAG_COMPLETE = 'COMPLETE';

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    protected $dates = ['deleted_at'];

    public $translatable = [
        'name',
        'description',
    ];
    public $rules = [
        'name' => 'required',
    ];

    public $table = 'winter_mall_order_states';

    public $hasMany = [
        'orders' => Order::class,
    ];

    public function getFlagOptions()
    {
        return [
            self::FLAG_CANCELLED => trans('winter.mall::lang.order_states.flags.cancelled'),
            self::FLAG_COMPLETE  => trans('winter.mall::lang.order_states.flags.complete'),
            self::FLAG_NEW       => trans('winter.mall::lang.order_states.flags.new'),
        ];
    }
}
