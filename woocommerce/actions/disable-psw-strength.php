// Disable password strength meter on checkout (less friction)
add_action( 'wp_print_scripts', function () {
if ( is_checkout() ) {
wp_dequeue_script( 'wc-password-strength-meter' );
}
} );