<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\Sluggable;
use Winter\Storm\Database\Traits\Sortable;
use Winter\Storm\Database\Traits\Validation;
use DB;

class PriceCategory extends Model
{
    use Validation;
    use Sortable;
    use Sluggable;

    const OLD_PRICE_CATEGORY_ID = 1;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $table = 'winter_mall_price_categories';
    public $translatable = ['name'];
    public $rules = [
        'name' => 'required',
        'code' => 'required',
    ];
    public $fillable = [
        'name',
        'code',
    ];
    public $slugs = [
        'code' => 'name',
    ];
    public $hasMany = [
        'prices' => [Price::class, 'key' => 'price_category_id'],
    ];

    public function afterDelete()
    {
        DB::table('winter_mall_prices')->where('price_category_id', $this->id)->delete();
    }
}
