// Gateway TITLE (shown next to the radio button)
add_filter( 'woocommerce_gateway_title', function ( $title, $payment_id ) {
if ( $payment_id === 'cod' ) {
$title = 'Pagamento alla consegna';
}
return $title;
}, 10, 2 );

// Gateway DESCRIPTION (the small text below the title)
add_filter( 'woocommerce_gateway_description', function ( $description, $payment_id ) {
if ( $payment_id === 'cod' ) {
$description = 'Pagamento (anche in contanti) esclusivo per i clienti locali (76121 Barletta)';
}
return $description;
}, 10, 2 );

// Gateway ICON (the image/logo next to the title)
add_filter( 'woocommerce_gateway_icon', function ( $icon, $payment_id ) {
if ( $payment_id === 'cod' ) {
$icon = '<img src="' . get_template_directory_uri() . '/img/cod-icon.svg" alt="COD" />';
}
return $icon;
}, 10, 2 );