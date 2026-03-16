// Remove WooCommerce's default generator meta tag
remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

// Remove WooCommerce styles on non-WooCommerce pages
add_action( 'wp_enqueue_scripts', function () {
if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
wp_dequeue_style( 'woocommerce-general' );
wp_dequeue_style( 'woocommerce-layout' );
wp_dequeue_style( 'woocommerce-smallscreen' );
}
} );

// Remove all default dashboard widgets (clean admin for clients)
add_action( 'wp_dashboard_setup', function () {
global $wp_meta_boxes;
$wp_meta_boxes['dashboard'] = [];
} );

// Remove emoji scripts/styles (saves ~20kb per page)
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Remove all junk "rel" tags
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Limit post revisions (default is unlimited — kills DB over time)
define( 'WP_POST_REVISIONS', 5 );

// Move autosave to every 5 min instead of 60 seconds
define( 'AUTOSAVE_INTERVAL', 300 );

// Trash emptied automatically after 7 days
define( 'EMPTY_TRASH_DAYS', 7 );