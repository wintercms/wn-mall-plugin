<?php namespace Winter\Mall\Updates;

use Db;
use Schema;
use Winter\Storm\Database\Updates\Migration;
use Winter\Mall\Models\Category;

class RenameTables extends Migration
{
    const MODELS = [
        'Category',
        'OrderState',
        'Property',
        'PropertyValue',
        'PropertyGroup',
    ];

    public function up()
    {
        $query = match(config('database.default')) {
            'sqlite' => 'SELECT name FROM sqlite_master WHERE type="table" AND name LIKE "offline_mall_%"',
            default => 'SELECT table_name FROM information_schema.tables WHERE table_name LIKE "offline_mall_%"', 
        };
        $tables = collect(Db::select($query))->map(fn($table) => head((array)$table))->all();

        foreach ($tables as $old) {
            $new = str_replace('offline_', 'winter_', $old);
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }

        foreach (static::MODELS as $model) {
            $from = 'OFFLINE\Mall\Models\\' . $model;
            $to = $model === 'Category' ? Category::MORPH_KEY : 'Winter\Mall\Models\\' . $model;

            Db::table('system_files')->where('attachment_type', $from)->update(['attachment_type' => $to]);
            Db::table('winter_translate_indexes')->where('model_type', $from)->update(['model_type' => $to]);
            Db::table('winter_translate_attributes')->where('model_type', $from)->update(['model_type' => $to]);
        }
    }

    public function down()
    {
        $query = match(config('database.default')) {
            'sqlite' => 'SELECT name FROM sqlite_master WHERE type="table" AND name LIKE "winter_mall_%"',
            default => 'SELECT table_name FROM information_schema.tables WHERE table_name LIKE "winter_mall_%"', 
        };
        $tables = collect(Db::select($query))->map(fn($table) => head((array)$table))->all();

        foreach ($tables as $old) {
            $new = str_replace('winter_', 'offline_', $old);
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }

        foreach (static::MODELS as $model) {
            $from = $model === 'Category' ? Category::MORPH_KEY : 'Winter\Mall\Models\\' . $model;
            $to = 'OFFLINE\Mall\Models\\' . $model;

            Db::table('system_files')->where('attachment_type', $from)->update(['attachment_type' => $to]);
            Db::table('winter_translate_indexes')->where('model_type', $from)->update(['model_type' => $to]);
            Db::table('winter_translate_attributes')->where('model_type', $from)->update(['model_type' => $to]);
        }
    }
}
