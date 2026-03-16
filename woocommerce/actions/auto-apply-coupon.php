// Auto-apply a coupon when a specific product is in the cart
add_action( 'woocommerce_before_calculate_totals', function () {
$target_product_id = 123; // change to your product ID
$coupon_code = 'SCONTO10';

foreach ( WC()->cart->get_cart() as $item ) {
if ( $item['product_id'] == $target_product_id ) {
if ( ! WC()->cart->has_discount( $coupon_code ) ) {
WC()->cart->apply_coupon( $coupon_code );
}
return;
}
}
} );