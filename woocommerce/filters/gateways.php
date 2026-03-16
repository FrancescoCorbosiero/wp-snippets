// Hide or show gateways based on any condition (cart total, role, location…)
add_filter( 'woocommerce_available_payment_gateways', function ( $gateways ) {

// Example: hide COD if cart total > €150
if ( WC()->cart && WC()->cart->total > 150 ) {
unset( $gateways['cod'] );
}

// Example: hide COD for non-local shipping postcodes
$postcode = WC()->customer ? WC()->customer->get_shipping_postcode() : '';
if ( ! empty( $postcode ) && $postcode !== '76121' ) {
unset( $gateways['cod'] );
}

// Example: show a gateway only to specific user roles
if ( ! current_user_can( 'wholesale_customer' ) ) {
unset( $gateways['your_gateway_id'] );
}

return $gateways;
} );