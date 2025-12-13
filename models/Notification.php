<?php namespace Winter\Mall\Models;

use Illuminate\Support\Facades\Cache;
use Model;
use Schema;
use Winter\Storm\Database\Traits\Sortable;
use Winter\Storm\Database\Traits\Validation;

class Notification extends Model
{
    use Validation;
    use Sortable;

    const CACHE_KEY = 'mall.enabled.notifications';
    public $table = 'winter_mall_notifications';
    public $rules = [
        'name'     => 'required',
        'code'     => 'required|unique:winter_mall_notifications,code',
        'template' => 'required',
    ];
    public $casts = [
        'enabled' => 'boolean',
    ];
    public $fillable = [
        'enabled',
        'code',
        'name',
        'description',
        'template',
    ];

    public static function getEnabled()
    {
        if ( ! Schema::hasTable('winter_mall_notifications')) {
            return collect([]);
        }

        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Notification::where('enabled', true)->get()->pluck('template', 'code');
        });
    }

    public function afterSave()
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function beforeDeleting()
    {
        throw new \LogicException('Winter.Mall: Notifications cannot be deleted.');
    }
}
