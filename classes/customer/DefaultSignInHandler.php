<?php

namespace Winter\Mall\Classes\Customer;

use Event;
use Exception;
use Flash;
use Winter\Storm\Auth\AuthException;
use Winter\Storm\Exception\ValidationException;
use Winter\Mall\Models\Cart;
use Winter\Mall\Models\Customer;
use Winter\Mall\Models\User;
use Auth;
use Winter\Mall\Models\Wishlist;
use Redirect;
use Validator;

class DefaultSignInHandler implements SignInHandler
{
    public function handle(array $data): ?User
    {
        try {
            return $this->login($data);
        } catch (ValidationException $ex) {
            throw $ex;
        } catch (AuthException $ex) {
            $error = str_contains($ex->getMessage(), 'not activated')
                ? 'not_activated'
                : 'unknown_user';

            Flash::error(trans('winter.mall::lang.components.signup.errors.' . $error));
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }

        return null;
    }

    /**
     * @throws AuthException
     * @throws ValidationException
     */
    protected function login(array $data)
    {
        $this->validate($data);

        $credentials = [
            'login'    => array_get($data, 'login'),
            'password' => array_get($data, 'password'),
        ];

        Event::fire('rainlab.user.beforeAuthenticate', [$this, $credentials]);
        Event::fire('mall.customer.beforeAuthenticate', [$this, $credentials]);

        $user = Auth::authenticate($credentials, true);

        if ($user->isBanned()) {
            Auth::logout();
            throw new AuthException('rainlab.user::lang.account.banned');
        }

        // If the user doesn't have a Customer model it was created via the backend.
        // Make sure to add the Customer model now
        if ( ! $user->customer && ! $user->is_guest) {
            $customer            = new Customer();
            $customer->firstname = $user->name;
            $customer->lastname  = $user->surname;
            $customer->user_id   = $user->id;
            $customer->is_guest  = false;
            $customer->save();

            $user->customer = $customer;
        }

        if ($user->customer->is_guest) {
            Auth::logout();
            throw new AuthException('winter.mall::lang.components.signup.errors.user_is_guest');
        }

        Cart::transferSessionCartToCustomer($user->customer);
        Wishlist::transferToCustomer($user->customer);

        return $user;
    }

    /**
     * @throws ValidationException
     */
    protected function validate(array $data)
    {
        $minPasswordLength = \Winter\User\Models\User::getMinPasswordLength();
        $rules    = [
            'login'    => 'required|email|between:6,255',
            'password' => sprintf('required|min:%d|max:255', $minPasswordLength),
        ];
        $messages = [
            'login.required'    => trans('winter.mall::lang.components.signup.errors.login.required'),
            'login.email'       => trans('winter.mall::lang.components.signup.errors.login.email'),
            'login.between'     => trans('winter.mall::lang.components.signup.errors.login.between'),
            'password.required' => trans('winter.mall::lang.components.signup.errors.password.required'),
            'password.max'      => trans('winter.mall::lang.components.signup.errors.password.max'),
        ];

        $validation = Validator::make($data, $rules, $messages);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
    }
}
