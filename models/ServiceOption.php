<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\SoftDelete;
use Winter\Storm\Database\Traits\Sortable;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Traits\PriceAccessors;

class ServiceOption extends Model
{
    use Validation;
    use Sortable;
    use PriceAccessors;
    use SoftDelete;

    const MORPH_KEY = 'mall.service_option';

    public $table = 'winter_mall_service_options';
    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $fillable = [
        'name',
        'description',
        'service_id',
    ];
    public $rules = [
        'name' => 'required',
    ];
    public $translatable = [
        'name',
        'description',
    ];
    public $morphMany = [
        'prices' => [Price::class, 'name' => 'priceable', 'conditions' => 'price_category_id is null'],
    ];
    public $belongsTo = [
        'service' => [Service::class],
    ];

    public function afterDelete()
    {
        $this->prices()->delete();
    }

    public function toArray()
    {
        $base = [
            'id'              => $this->id,
            'name'            => $this->name,
            'price'           => $this->price,
            'price_formatted' => $this->price()->string,
            'sort_order'      => $this->sort_order,
            'service'         => [
                'id'       => $this->service->id,
                'name'     => $this->service->name,
                'required' => $this->service->required,
                'code'     => $this->service->code,
            ],
        ];

        $base['price'] = $this->prices->mapWithKeys(function ($price) {
            return [$price->currency->code => $price];
        });

        return $base;
    }
}
