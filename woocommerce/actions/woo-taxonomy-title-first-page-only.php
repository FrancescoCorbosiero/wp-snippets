add_action( 'wp', function() {
$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
if ( 1 !== $page ) {
remove_action( 'woocommerce_archive_description', 'shoptimizer_woocommerce_taxonomy_archive_description' );
remove_action( 'woocommerce_archive_description', 'shoptimizer_category_image', 20 );
// If you also want to remove the "Below category content" area:
remove_action( 'woocommerce_after_shop_loop', 'shoptimizer_product_cat_display_details_meta', 40 );
};
}, 20 );