<?php namespace Winter\Mall\Models;

use Carbon\Carbon;
use Cookie;
use Illuminate\Support\Collection;
use Model;
use Winter\Storm\Database\Traits\Validation;
use Winter\Mall\Classes\Exceptions\OutOfStockException;
use Winter\Mall\Classes\Totals\TotalsCalculator;
use Winter\Mall\Classes\Totals\TotalsCalculatorInput;
use Winter\Mall\Classes\Traits\HashIds;
use Winter\Mall\Classes\Traits\PDFMaker;
use Winter\Mall\Classes\Traits\ShippingMethods;
use Winter\User\Facades\Auth;
use Winter\User\Models\User;
use Session;

class Wishlist extends Model
{
    use Validation;
    use HashIds;
    use PDFMaker;
    use ShippingMethods;

    public $table = 'winter_mall_wishlists';
    public $rules = [
        'name'        => 'required',
        'session_id'  => 'required_without:customer_id',
        'customer_id' => 'required_without:session_id',
    ];
    public $hasMany = [
        'items' => [WishlistItem::class, 'delete' => true],
    ];
    public $belongsTo = [
        'shipping_method' => [ShippingMethod::class],
        'customer'        => [Customer::class],
    ];
    public $fillable = [
        'name',
        'session_id',
        'customer_id',
    ];
    public $with = [
        'items',
        'shipping_method',
    ];
    /**
     * @var TotalsCalculator
     */
    protected $totalsCached;

    public function getTotalsAttribute(): TotalsCalculator
    {
        if ($this->totalsCached) {
            return $this->totalsCached;
        }

        return $this->totals();
    }

    public function totals(): TotalsCalculator
    {
        return $this->totalsCached = new TotalsCalculator(TotalsCalculatorInput::fromWishlist($this));
    }

    /**
     * Return a PDF instance of this Wishlist.
     *
     * @return \Barryvdh\DomPDF\PDF
     * @throws \Cms\Classes\CmsException
     */
    public function getPDF()
    {
        return $this->makePDFFromDir('wishlist', ['wishlist' => $this]);
    }

    /**
     * Return all wishlists for the currently logged in user or
     * the currently active user session.
     */
    public static function byUser(?User $user = null): Collection
    {
        $sessionId = static::getSessionId();

        return self::where('session_id', $sessionId)
                   ->when($user && $user->customer, function ($q) use ($user) {
                       $q->orWhere('customer_id', $user->customer->id);
                   })
                   ->orderBy('created_at')
                   ->get();
    }

    /**
     * Generate a unique wishlist session id.
     *
     * @return string
     */
    public static function getSessionId(): string
    {
        $sessionId = Session::get('wishlist_session_id') ?? Cookie::get('wishlist_session_id') ?? str_random(100);
        Cookie::queue('wishlist_session_id', $sessionId, 9e6);
        Session::put('wishlist_session_id', $sessionId);

        return $sessionId;
    }

    /**
     * Create a new wishlist for a specified user or the currently active session.
     */
    public static function createForUser(?User $user, string $name = null): self
    {
        $attributes = $user && $user->customer
            ? ['customer_id' => $user->customer->id]
            : ['session_id' => static::getSessionId()];

        $name = $name ?? trans('winter.mall::frontend.wishlist.default_name');

        return Wishlist::create(array_merge($attributes, ['name' => $name]));
    }

    /**
     * Add all products to the specified cart.
     */
    public function addToCart(Cart $cart): bool
    {
        $allInStock = true;
        $this->loadMissing(['items.product', 'items.variant']);
        $this->items->each(function (WishlistItem $item) use ($cart, &$allInStock) {
            try {
                $cart->addProduct($item->product, $item->quantity, $item->variant);
            } catch (OutOfStockException $e) {
                $allInStock = false;
            }
        });

        if ($this->shipping_method_id) {
            $cart->setShippingMethod($this->shipping_method);
        }

        return $allInStock;
    }

    /**
     * Transfer a session attached wishlist to a customer.
     *
     * @param $customer
     */
    public static function transferToCustomer(Customer $customer)
    {
        Wishlist::whereIn('id', self::byUser()->pluck('id'))
                ->update([
                    'customer_id' => $customer->id,
                    'session_id'  => null,
                ]);
    }

    /**
     * Cleanup of old data using Winter.GDPR.
     *
     * @see https://github.com/Winter-GmbH/oc-gdpr-plugin
     *
     * @param Carbon $deadline
     * @param int    $keepDays
     */
    public function gdprCleanup(Carbon $deadline, int $keepDays)
    {
        self::where('updated_at', '<', $deadline)
            ->whereNull('customer_id')
            ->get()
            ->each(function (Wishlist $wishlist) {
                $wishlist->items->each->delete();
                $wishlist->delete();
            });
    }

    public function getAvailableShippingMethods()
    {
        return ShippingMethod::getAvailableByWishlist($this);
    }

    public function getCartCountryId()
    {
        $user = Auth::getUser();
        if ( ! $user || ! $user->customer) {
            return null;
        }

        return optional($user->customer->shipping_address)->country_id;
    }
}
