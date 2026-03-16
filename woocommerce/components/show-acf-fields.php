// Show ACF fields in admin columns for any post type
add_filter( 'manage_posts_columns', function ( $columns ) {
$columns['city'] = 'Città'; // match your ACF field name
return $columns;
} );

add_action( 'manage_posts_custom_column', function ( $column, $post_id ) {
if ( $column === 'city' ) {
echo get_field( 'city', $post_id ) ?: '—';
}
}, 10, 2 );