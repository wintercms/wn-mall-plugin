<?php

namespace Winter\Mall\Classes\Customer;

use Winter\Storm\Auth\AuthException;
use Winter\Mall\Models\User;

class AuthManager extends \Winter\User\Classes\AuthManager
{
    protected $userModel = User::class;

    public function findUserByCredentials(array $credentials)
    {
        $user = parent::findUserByCredentials($credentials);
        if (optional($user->customer)->is_guest === 1) {
            throw new AuthException('A user was not found with the given credentials.');
        }

        return $user;
    }
}
