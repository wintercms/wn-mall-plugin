<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\Sluggable;
use Winter\Storm\Database\Traits\SoftDelete;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Traits\SortableRelation;
use DB;

class Service extends Model
{
    use Validation;
    use Sluggable;
    use SortableRelation;
    use SoftDelete;

    public $table = 'winter_mall_services';
    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $fillable = [
        'name',
        'description',
    ];
    public $rules = [
        'name' => 'required',
    ];
    public $hasMany = [
        'options' => [
            ServiceOption::class,
            'sort'     => 'sort_order ASC',
            'table'    => 'winter_mall_service_options',
            'key'      => 'service_id',
            'otherKey' => 'id',
        ],
    ];
    public $belongsToMany = [
        'products' => [
            Product::class,
            'table'    => 'winter_mall_product_service',
            'key'      => 'service_id',
            'otherKey' => 'product_id',
            'pivot'    => ['required'],
        ],
        'taxes'    => [
            Tax::class,
            'table'    => 'winter_mall_service_tax',
            'key'      => 'service_id',
            'otherKey' => 'tax_id',
        ],
    ];
    public $translatable = [
        'name',
        'description',
    ];
    public $slugs = [
        'code' => 'name',
    ];

    public function afterDelete()
    {
        $this->options->each->delete();
        DB::table('winter_mall_product_service')->where('service_id', $this->id)->delete();
    }
}
