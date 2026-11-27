# Services

Services are additional offerings for a product 
(e. g. extended warranty, on-site installation).

You can attach any number of services to a product on the 
edit product form in the backend (cart section). 

If a product with attached services is added to the cart, a modal appears 
where service options can or must be selected.

Each service needs at least one option. Each option has a price.
A service can be specified to be required when a certain product 
is added to the cart.

By default, only one of the provided options can be selected.
By changing the [type of the `radio` inputs to `checkbox`](https://github.com/wintercms/wn-mall-plugin/components/product/servicemodal.htm#L36), a user
will be able to select multiple options for a single service. 


By default, only one of the options provided can be selected.  
If you wish to allow users to select multiple options, you must override the [services pop-up partial](https://github.com/wintercms/wn-mall-plugin/components/product/servicemodal.htm) in your theme (see [Overriding component partials](https://wintercms.com/docs/develop/docs/cms/components#overriding-component-partials)), replacing the `radio` inputs with ``checkbox``.  
Users will then be able to select multiple options for the same service.