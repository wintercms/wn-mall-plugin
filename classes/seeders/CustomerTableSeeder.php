<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Illuminate\Support\Facades\Schema;
use Winter\Mall\Models\Address;
use Winter\Mall\Models\Customer;
use Winter\Mall\Models\CustomerGroup;
use Winter\Location\Models\Country;
use Winter\Location\Models\State;
use Winter\User\Models\User;

class CustomerTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $legacyTables = Schema::hasTable('offline_mall_customers');

            if ($legacyTables) {
                Customer::extend(function () {
                    $this->setTable('offline_mall_customers');
                }, true);
                CustomerGroup::extend(function () {
                    $this->setTable('offline_mall_customer_groups');
                }, true);
                Address::extend(function () {
                    $this->setTable('offline_mall_addresses');
                    $this->rules['customer_id'] = 'required|exists:offline_mall_customers,id';
                }, true);
            }

            try {
                config()->set('rainlab.user::minPasswordLength', 8);
                $this->createUser('test@test.com', CustomerGroup::first()->id, $legacyTables);
                $this->createUser('test2@test2.com', null, $legacyTables);
            } finally {
                if ($legacyTables) {
                    Customer::extend(function () {
                        $this->setTable('winter_mall_customers');
                    }, true);
                    CustomerGroup::extend(function () {
                        $this->setTable('winter_mall_customer_groups');
                    }, true);
                    Address::extend(function () {
                        $this->setTable('winter_mall_addresses');
                        $this->rules['customer_id'] = 'required|exists:winter_mall_customers,id';
                    }, true);
                }
            }
        }
    }

    protected function createUser($email, $customerGroupId = null, bool $legacyTables = false)
    {
        $user                                 = new User();
        $user->name                           = 'Float';
        $user->surname                        = 'McFloatface';
        $user->email                          = $email;
        $user->password                       = '12345678';
        $user->password_confirmation          = '12345678';
        $user->is_activated                   = true;
        if ($legacyTables) {
            $user->offline_mall_customer_group_id = $customerGroupId;
        } else {
            $user->winter_mall_customer_group_id = $customerGroupId;
        }
        $user->save();

        $customer            = new Customer();
        $customer->firstname = 'Float';
        $customer->lastname  = 'McFloatface';
        $customer->user_id   = $user->id;
        $customer->save();

        $shippingAddress             = new Address();
        $shippingAddress->name       = 'Float McFloatface';
        $shippingAddress->lines      = 'Street 12';
        $shippingAddress->zip        = '6000';
        $shippingAddress->city       = 'Lucerne';
        $shippingAddress->state_id   = State::where('name', 'Luzern')->first()->id;
        $shippingAddress->country_id = Country::where('code', 'CH')->first()->id;

        $customer->addresses()->save($shippingAddress);

        $billingAddress             = new Address();
        $billingAddress->name       = 'Float McFloatface';
        $billingAddress->lines      = 'Billing 12';
        $billingAddress->zip        = '6000';
        $billingAddress->city       = 'Lucerne';
        $billingAddress->state_id   = State::where('name', 'Luzern')->first()->id;
        $billingAddress->country_id = Country::where('code', 'CH')->first()->id;

        $customer->addresses()->save($billingAddress);
    }
}
