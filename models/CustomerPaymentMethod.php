<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\SoftDelete;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Traits\HashIds;

class CustomerPaymentMethod extends Model
{
    use Validation;
    use HashIds;
    use SoftDelete;

    public $table = 'winter_mall_customer_payment_methods';
    public $rules = [
        'payment_method_id' => 'required|exists:winter_mall_payment_methods,id',
        'customer_id'       => 'required|exists:winter_mall_customers,id',
    ];
    public $belongsTo = [
        'payment_method' => PaymentMethod::class,
        'customer'       => Customer::class,
    ];
    public $casts = [
        'is_default' => 'boolean',
    ];
    public $fillable = [
        'payment_method_id',
        'customer_id',
        'data',
        'name',
        'is_default',
    ];
    public $jsonable = ['data'];

    public function beforeSave()
    {
        $hasDefault = self::where('customer_id', $this->customer_id)->where('is_default', 1)->count() > 0;
        if ( ! $hasDefault) {
            $this->is_default = true;
        }
    }

}
