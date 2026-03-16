// Remove coupon field from checkout (keeps it in cart only)
add_filter( 'woocommerce_checkout_coupon_message', '__return_false' );
add_filter( 'woocommerce_coupons_enabled', '__return_false' );