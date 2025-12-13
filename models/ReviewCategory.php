<?php namespace Winter\Mall\Models;

use Model;
use Winter\Storm\Database\Traits\Sluggable;
use Winter\Storm\Database\Traits\Validation;

class ReviewCategory extends Model
{
    use Validation;
    use Sluggable;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name',
    ];
    public $fillable = [
        'name',
    ];
    public $table = 'winter_mall_review_categories';
    public $slugs = [
        'slug' => 'name',
    ];
    public $rules = [
        'name' => 'required',
    ];
    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table' => 'winter_mall_category_review_category',
        ]
    ];
}
