<?php namespace Winter\Mall\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\Backend;
use BackendMenu;
use Illuminate\Support\Facades\Redirect;
use Winter\Storm\Support\Facades\Flash;
use Winter\Mall\Models\Review;

class Reviews extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_reviews',
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Winter.Mall', 'mall-catalogue', 'mall-reviews');
    }

    public function onApprove()
    {
        Review::findOrFail(post('id'))->approve();

        $next = Review::orderBy('created_at')->whereNull('approved_at')->first(['id']);

        if ($next) {
            return Redirect::to(Backend::url('winter/mall/reviews/update/' . $next->id));
        }

        Flash::success(trans('winter.mall::lang.reviews.no_more'));

        return Redirect::to(Backend::url('winter/mall/reviews'));
    }
}
