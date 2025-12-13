<?php


namespace Winter\Mall\Classes\Customer;

use Winter\Mall\Models\User;

interface SignUpHandler
{
    public function handle(array $postData, bool $asGuest = false): ?User;
}
