<?php

namespace Winter\Mall\Classes\Registration;

use Winter\Mall\Models\User as WinterUser;
use Validator;

trait BootValidation
{
    protected function registerValidationRules()
    {
        Validator::extend('non_existing_user', function ($attribute, $value, $parameters) {
            return WinterUser
                    ::with('customer')
                    ->where('email', $value)
                    ->whereHas('customer', function ($q) {
                        $q->where('is_guest', 0);
                    })->count() === 0;
        });
    }
}
