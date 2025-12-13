<?php

namespace Winter\Mall\Models;

use Winter\User\Models\User as UserBase;

class User extends UserBase
{
    public $hasOne = [
        'customer' => Customer::class,
    ];
    public $belongsTo  =[
        'customer_group' => [CustomerGroup::class, 'key' => 'winter_mall_customer_group_id'],
    ];
    public $with = ['customer_group'];
    public $rules = [
        'email'                 => 'required|between:6,255|email',
        'avatar'                => 'nullable|image|max:4000',
        'password'              => 'required:create|between:4,255|confirmed',
        'password_confirmation' => 'required_with:password|between:4,255',
    ];
}
