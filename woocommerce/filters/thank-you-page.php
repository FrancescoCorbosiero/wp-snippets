// Different thank-you message depending on how the customer paid
add_filter( 'woocommerce_thankyou_order_received_text', function ( $text, $order ) {
if ( ! $order ) return $text;

if ( $order->get_payment_method() === 'cod' ) {
return 'Grazie! Riceverai la merce entro 24h. Il pagamento avverrà alla consegna.';
}

if ( $order->get_payment_method() === 'stripe' ) {
return 'Grazie! Il pagamento è stato ricevuto. Ti invieremo una conferma via email.';
}

return $text;
}, 10, 2 );