import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "Winter.Mall documentation - WinterCMS e-commerce plugin",
    description: "The all-inclusive e-commerce solution for WinterCMS.",
    base: '/wn-mall-plugin/',
    vite: {
        server: {
            port: 3003
        }
    },

    // https://vitepress.dev/reference/default-theme-config
    themeConfig: {
        logo: {
            light: 'mall-logo-light.svg',
            dark: 'mall-logo-dark.svg'
        },
        siteTitle: false,
        editLink: {
            pattern: 'https://github.com/wintercms/wn-mall-plugin/tree/develop/docs/:path',
            text: 'Edit this page on GitHub'
        },
        lastUpdated: {
            text: 'Last updated'
        },
        externalLinkIcon: true,
        search: {
            provider: 'local'
        },
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
            { text: 'Development', link: '/development/', activeMatch: '/development/' }
        ],
        sidebar: {
            '/guide/': [
                {
                    text: 'Introduction',
                    collapsed: false,
                    items: [
                        { text: 'Getting Started', link: '/guide/' },
                        { text: 'Installation', link: '/guide/introduction/installation' },
                        { text: 'Configuration', link: '/guide/introduction/configuration' }
                    ]
                },
                {
                    text: 'Usage',
                    collapsed: false,
                    items: [
                        { text: 'Properties', link: '/guide/usage/properties' },
                        { text: 'Categories', link: '/guide/usage/categories' },
                        { text: 'Products & Variants', link: '/guide/usage/products' },
                        { text: 'Virtual Products', link: '/guide/usage/virtual-products' },
                        { text: 'Reviews', link: '/guide/usage/reviews' },
                        { text: 'Services', link: '/guide/usage/services' },
                        { text: 'Currencies', link: '/guide/usage/currencies' },
                        { text: 'Taxes', link: '/guide/usage/taxes' },
                        { text: 'Payment Methods', link: '/guide/usage/payment-methods' },
                        { text: 'Shipping Methods', link: '/guide/usage/shipping-methods' },
                        { text: 'Analytics', link: '/guide/usage/analytics' },
                        { text: 'Going Live', link: '/guide/usage/going-live' },
                    ]
                },
                {
                    text: 'Components',
                    collapsed: true,
                    items: [
                        { text: 'MallDependencies', link: '/guide/components/mall-dependencies' },
                        { text: 'Product', link: '/guide/components/product' },
                        { text: 'Products', link: '/guide/components/products' },
                        { text: 'ProductsFilter', link: '/guide/components/products-filter' },
                        { text: 'ProductReviews', link: '/guide/components/product-reviews' },
                        { text: 'Cart', link: '/guide/components/cart' },
                        { text: 'DiscountApplier', link: '/guide/components/discount-applier' },
                        { text: 'Checkout', link: '/guide/components/checkout' },
                        { text: 'QuickCheckout', link: '/guide/components/quick-checkout' },
                        { text: 'SignUp', link: '/guide/components/sign-up' },
                        { text: 'Wishlists', link: '/guide/components/wishlists' },
                        { text: 'WishlistButton', link: '/guide/components/wishlist-button' },
                        { text: 'PaymentMethodSelector', link: '/guide/components/payment-method-selector' },
                        { text: 'ShippingMethodSelector', link: '/guide/components/shipping-method-selector' },
                        { text: 'AddressSelector', link: '/guide/components/address-selector' },
                        { text: 'MyAccount', link: '/guide/components/my-account' },
                        { text: 'OrdersList', link: '/guide/components/orders-list' },
                        { text: 'AddressForm', link: '/guide/components/address-form' },
                        { text: 'CurrencyPicker', link: '/guide/components/currency-picker' },
                        { text: 'CustomerProfile', link: '/guide/components/customer-profile' },
                        { text: 'AddressList', link: '/guide/components/address-list' },
                    ]
                }
            ],
            '/development/': [
                {
                    text: 'Working with Mall',
                    collapsed: false,
                    items: [
                        { text: 'Product Model', link: '/development/core/product-model' },
                        { text: 'Variant Model', link: '/development/core/variant-model' },
                        { text: 'Order Model', link: '/development/core/order-model' },
                        { text: 'Cart Model', link: '/development/core/cart-model' },
                        { text: 'Wishlist Model', link: '/development/core/wishlist-model' },
                        { text: 'Payment Providers', link: '/development/core/payment-providers' },
                        { text: 'Pricing Information', link: '/development/core/pricing-information' },
                        // { text: 'Price Bag', link: '/development/core/price-bag' },
                        { text: 'Events', link: '/development/core/events' },
                        { text: 'PDF', link: '/development/core/pdf' },
                        { text: 'Integration', link: '/development/core/integration' },
                        { text: 'Artisan Console', link: '/development/core/console-commands' },
                    ]
                },
                {
                    text: 'Extending Mall',
                    collapsed: false,
                    items: [
                        { text: 'Unit Testing', link: '/development/extend/unit-testing' },
                    ]
                },
            ],
        },
        socialLinks: [
            { 
                icon: 'github', 
                link: 'https://github.com/wintercms/wn-mall-plugin'
            }
        ]
    },

    markdown: {
        lineNumbers: false,
    },

    // Exclude specific source files from being processed by VitePress.
    srcExclude: [
        'drafts/**'
    ]
})
