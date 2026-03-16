// Redirect customers to shop (not account) after login
add_filter( 'woocommerce_login_redirect', function ( $redirect, $user ) {
if ( ! user_can( $user, 'manage_options' ) ) {
return wc_get_page_permalink( 'shop' );
}
return $redirect;
}, 10, 2 );