<?php

namespace Winter\Mall\Classes\Registration;

use App;
use Backend\Widgets\Form;
use Illuminate\Support\Facades\Event;
use Winter\Mall\Models\Address;
use Winter\Mall\Models\Customer;
use Winter\Mall\Models\CustomerGroup;
use Winter\Mall\Models\Tax;
use Winter\Mall\Models\User as MallUser;
use Winter\Location\Models\Country as WinterCountry;
use Winter\User\Controllers\Users as WinterUsersController;
use Winter\User\Models\User as WinterUser;
use System\Classes\PluginManager;

trait BootExtensions
{
    protected function registerExtensions()
    {
        if (PluginManager::instance()->exists('Winter.Location')) {
            $this->extendWinterCountry();
        }
        if (PluginManager::instance()->exists('Winter.User')) {
            $this->extendWinterUser();
        }
    }

    protected function extendWinterCountry()
    {
        WinterCountry::extend(function ($model) {
            $model->belongsToMany['taxes'] = [
                Tax::class,
                'table'    => 'winter_mall_country_tax',
                'key'      => 'country_id',
                'otherKey' => 'tax_id',
            ];
        });
    }

    protected function extendWinterUser()
    {
        // Use custom user model
        App::singleton('user.auth', function () {
            return \Winter\Mall\Classes\Customer\AuthManager::instance();
        });

        WinterUser::extend(function ($model) {
            $model->hasOne['customer']          = Customer::class;
            $model->belongsTo['customer_group'] = [CustomerGroup::class, 'key' => 'winter_mall_customer_group_id'];
            $model->hasManyThrough['addresses']        = [
                Address::class,
                'key'        => 'user_id',
                'through'    => Customer::class,
                'throughKey' => 'id',
            ];
            $model->rules['surname']            = 'required';
            $model->rules['name']               = 'required';
        });

        WinterUsersController::extend(function (WinterUsersController $users) {
            if (!isset($users->relationConfig)) {
                $users->addDynamicProperty('relationConfig');
            }
            $myConfigPath = '$/winter/mall/controllers/users/config_relation.yaml';
            $users->relationConfig = $users->mergeConfig(
                $users->relationConfig,
                $myConfigPath
            );
            // Extend the Users controller with the Relation behaviour that is needed
            // to display the addresses relation widget above.
            if (!$users->isClassExtendedWith('Backend.Behaviors.RelationController')) {
                $users->extendClassWith(\Backend\Behaviors\RelationController::class);
            }
        });

        MallUser::extend(function ($model) {
            $model->rules['surname'] = 'required';
            $model->rules['name']    = 'required';
        });

        // Add Customer Groups menu entry to Winter.User
        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems('Winter.User', 'user', [
                'customer_groups' => [
                    'label'       => 'winter.mall::lang.common.customer_groups',
                    'url'         => \Backend::url('winter/mall/customergroups'),
                    'icon'        => 'icon-users',
                    'permissions' => ['winter.mall.manage_customer_groups'],
                ],
            ]);
            $manager->addSideMenuItems('Winter.User', 'user', [
                'customer_addresses' => [
                    'label'       => 'winter.mall::lang.common.addresses',
                    'url'         => \Backend::url('winter/mall/addresses'),
                    'icon'        => 'icon-home',
                    'permissions' => ['winter.mall.manage_customer_addresses'],
                ],
            ]);
        }, 5);

        // Add Customer Groups relation to Winter.User form
        Event::listen('backend.form.extendFields', function (Form $widget) {
            if ( ! $widget->getController() instanceof \Winter\User\Controllers\Users) {
                return;
            }

            if ( ! $widget->model instanceof \Winter\User\Models\User) {
                return;
            }

            $widget->addTabFields([
                'customer_group' => [
                    'label'       => trans('winter.mall::lang.common.customer_group'),
                    'type'        => 'relation',
                    'nameFrom'    => 'name',
                    'emptyOption' => trans('winter.mall::lang.common.none'),
                    'tab'         => 'winter.mall::lang.plugin.name',
                ],
                //
                // This feature is blocked by https://github.com/octobercms/october/issues/2508
                //
                // 'addresses'      => [
                //     'label' => trans('winter.mall::lang.common.addresses'),
                //     'type'  => 'partial',
                //     'path'  => '$/winter/mall/controllers/users/_addresses.htm',
                //     'tab'   => 'winter.mall::lang.plugin.name',
                // ],
            ]);
        }, 5);
    }
}
