<?php namespace Winter\Mall\Models;

use Illuminate\Support\Facades\Queue;
use Model;
use Winter\Storm\Database\Traits\Sluggable;
use Winter\Storm\Database\Traits\SoftDelete;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Jobs\PropertyRemovalUpdate;
use Winter\Mall\Classes\Queries\UniquePropertyValuesInCategoriesQuery;
use Winter\Mall\Classes\Traits\HashIds;

class Property extends Model
{
    use Validation;
    use SoftDelete;
    use HashIds;
    use Sluggable;

    protected $dates = ['deleted_at'];
    public $jsonable = ['options'];
    public $rules = [
        'name' => 'required',
        'type' => 'required|in:text,textarea,dropdown,checkbox,checkboxlist,color,image,float,integer,richeditor,switch,datetime,date',
    ];
    public $slugs = [
        'slug' => 'name',
    ];
    public $table = 'winter_mall_properties';
    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name',
        'unit',
        'options',
    ];
    public $fillable = [
        'name',
        'type',
        'unit',
        'slug',
        'options',
    ];
    public $hasMany = [
        'property_values' => PropertyValue::class,
    ];
    public $belongsToMany = [
        'property_groups' => [
            PropertyGroup::class,
            'table'      => 'winter_mall_property_property_group',
            'key'        => 'property_id',
            'otherKey'   => 'property_group_id',
            'pivot'      => ['use_for_variants', 'filter_type', 'sort_order'],
            'pivotModel' => PropertyGroupProperty::class,
        ],
    ];

    public function afterSave()
    {
        if ($this->pivot && ! $this->pivot->use_for_variants) {
            $categories = $this->property_groups->flatMap->getRelatedCategories();

            Product
                ::whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('winter_mall_category_product.category_id', $categories->pluck('id'));
                })
                ->where('group_by_property_id', $this->id)
                ->update(['group_by_property_id' => null]);
        }
    }

    public function afterDelete()
    {
        // Remove the property values from all related products.
        $products = $this->property_values->pluck('product_id')->unique();

        // Chunk the re-indexing since a lot of products and variants might be affected by this change.
        Product::published()
               ->orderBy('id')
               ->whereIn('id', $products)
               ->with('variants')
               ->chunk(25, function ($products) {
                   $data = [
                       'properties' => [$this->id],
                       'products'   => $products->pluck('id'),
                       'variants'   => $products->flatMap->variants->pluck('id'),
                   ];
                   Queue::push(PropertyRemovalUpdate::class, $data);
               });
    }

    public function getSortOrderAttribute()
    {
        return $this->pivot->sort_order;
    }

    public static function getValuesForCategory($categories)
    {
        $raw    = (new UniquePropertyValuesInCategoriesQuery($categories))->query()->get();
        $values = PropertyValue::hydrate($raw->toArray())->load(['property.translations', 'translations']);
        $values = $values->groupBy('property_id')->map(function ($values) {
            // if this property has options make sure to restore the original order
            $firstProp = $values->first()->property;
            if ( ! $firstProp->options) {
                return $values;
            }

            $order = collect($firstProp->options)->flatten()->flip();

            return $values->sortBy(function ($value) use ($order) {
                return $order[$value->value] ?? 0;
            });
        });

        return $values;
    }

    public function getTypeOptions()
    {
        return [
            'text'       => trans('winter.mall::lang.custom_field_options.text'),
            'integer'    => trans('winter.mall::lang.custom_field_options.integer'),
            'float'      => trans('winter.mall::lang.custom_field_options.float'),
            'textarea'   => trans('winter.mall::lang.custom_field_options.textarea'),
            'richeditor' => trans('winter.mall::lang.custom_field_options.richeditor'),
            'dropdown'   => trans('winter.mall::lang.custom_field_options.dropdown'),
            'checkbox'   => trans('winter.mall::lang.custom_field_options.checkbox'),
            'checkboxlist'   => trans('winter.mall::lang.custom_field_options.checkboxlist'),
            'color'      => trans('winter.mall::lang.custom_field_options.color'),
//            'image'    => trans('winter.mall::lang.custom_field_options.image'),
            'datetime'   => trans('winter.mall::lang.custom_field_options.datetime'),
            'date'       => trans('winter.mall::lang.custom_field_options.date'),
            'switch'     => trans('winter.mall::lang.custom_field_options.switch'),
        ];
    }
}
