<?php

namespace Winter\Mall\Models;

use Model;
use Session;

class FeedSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'winter_mall_settings';
    public $settingsFields = '$/winter/mall/models/settings/fields_feeds.yaml';

    public function filterFields()
    {
        if (FeedSettings::get('google_merchant_key') === null) {
            FeedSettings::set('google_merchant_key', str_random(12));
        }
    }
}
