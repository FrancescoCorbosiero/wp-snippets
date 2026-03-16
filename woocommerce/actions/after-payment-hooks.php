// Fires when order status moves TO "processing" (payment received)
add_action( 'woocommerce_order_status_processing', function ( $order_id ) {
$order = wc_get_order( $order_id );
if ( $order->get_payment_method() === 'cod' ) {
// e.g. send a custom notification, reduce stock manually, etc.
}
} );

// Fires when payment is complete (covers all gateways)
add_action( 'woocommerce_payment_complete', function ( $order_id ) {
$order = wc_get_order( $order_id );
// React to any successful payment
} );

// Add a custom field to the order based on payment method chosen
add_action( 'woocommerce_checkout_create_order', function ( $order, $data ) {
if ( $data['payment_method'] === 'cod' ) {
$order->update_meta_data( '_is_local_cod', true );
}
}, 10, 2 );