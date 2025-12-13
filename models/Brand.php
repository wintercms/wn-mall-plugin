<?php namespace Winter\Mall\Models;

use Illuminate\Support\Facades\Queue;
use Model;
use Winter\Storm\Database\Traits\Sluggable;
use Winter\Storm\Database\Traits\Sortable;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Jobs\BrandChangeUpdate;
use System\Models\File;

class Brand extends Model
{
    use Validation;
    use Sortable;
    use Sluggable;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name',
        'description',
        'website',
    ];
    public $slugs = [
        'slug' => 'name',
    ];
    public $rules = [
        'name'    => 'required',
        'website' => 'url',
    ];
    public $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'sort_order',
    ];
    public $table = 'winter_mall_brands';
    public $attachOne = [
        'logo' => File::class,
    ];
    public $hasMany = [
        'products' => Product::class,
    ];

    public function afterDelete()
    {
        Product::orderBy('id')
               ->where('brand_id', $this->id)
               ->chunk(100, function ($products) {
                   $data = [
                       'ids' => $products->pluck('id'),
                   ];
                   Queue::push(BrandChangeUpdate::class, $data);
               });
    }

    public function toArray()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
