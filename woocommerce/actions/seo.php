// Canonical redirect — remove ?replytocom and other junk params
add_action( 'template_redirect', function () {
if ( isset( $_GET['replytocom'] ) ) {
wp_redirect( get_permalink(), 301 );
exit;
}
} );

// Disable search entirely if you don't use it (blocks SEO spam)
add_action( 'parse_query', function ( $query ) {
if ( $query->is_search && ! is_admin() ) {
wp_redirect( home_url(), 301 );
exit;
}
} );

// Auto noindex paginated pages beyond page 2 (prevents thin content)
add_action( 'wp_head', function () {
if ( get_query_var( 'paged' ) > 2 ) {
echo '
<meta name="robots" content="noindex, follow">' . PHP_EOL;
}
} );