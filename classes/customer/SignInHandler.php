<?php


namespace Winter\Mall\Classes\Customer;

use Winter\Mall\Models\User;

interface SignInHandler
{
    public function handle(array $postData): ?User;
}
