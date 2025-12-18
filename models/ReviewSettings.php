<?php

namespace Winter\Mall\Models;

use Model;
use Session;

class ReviewSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'winter_mall_review_settings';
    public $settingsFields = '$/winter/mall/models/settings/fields_reviews.yaml';

    public function initSettingsData()
    {
        $this->enabled         = true;
        $this->moderated       = false;
        $this->allow_anonymous = false;
    }
}
