// Disable XML-RPC entirely (common attack vector)
add_filter( 'xmlrpc_enabled', '__return_false' );

// Remove WordPress version from everywhere
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Hide login errors (don't tell attackers if username exists)
add_filter( 'login_errors', function () {
return 'Credenziali non valide.';
} );

// Disable file editing from WP admin (wp-config.php alternative)
define( 'DISALLOW_FILE_EDIT', true );

// Block user enumeration via ?author=1 scans
add_action( 'init', function () {
if ( ! is_admin() && isset( $_GET['author'] ) ) {
wp_redirect( home_url(), 301 );
exit;
}
} );

// Limit login attempts natively (without plugin)
add_action( 'wp_login_failed', function ( $username ) {
$ip = $_SERVER['REMOTE_ADDR'];
$key = 'failed_login_' . md5( $ip );
$attempts = (int) get_transient( $key );

if ( $attempts >= 5 ) return; // already locked

set_transient( $key, $attempts + 1, 15 * MINUTE_IN_SECONDS );
} );

add_filter( 'authenticate', function ( $user, $username, $password ) {
$ip = $_SERVER['REMOTE_ADDR'];
$key = 'failed_login_' . md5( $ip );

if ( (int) get_transient( $key ) >= 5 ) {
return new WP_Error( 'too_many_retries', 'Troppi tentativi. Riprova tra 15 minuti.' );
}
return $user;
}, 30, 3 );