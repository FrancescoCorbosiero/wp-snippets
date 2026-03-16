/**
* Override the COD payment method description directly via WooCommerce filter.
* This is the most reliable approach — no output buffering needed.
*/
add_filter( 'woocommerce_gateway_description', function ( $description, $payment_id ) {
if ( $payment_id === 'cod' ) {
$description = 'Pagamento (anche in contanti) esclusivo per i clienti locali (76121 Barletta)';
}
return $description;
}, 10, 2 );