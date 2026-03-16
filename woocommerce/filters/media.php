// Stop WordPress generating every image size (saves disk space)
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
unset( $sizes['medium_large'] );
unset( $sizes['1536x1536'] );
unset( $sizes['2048x2048'] );
return $sizes;
} );

// Auto-add loading="lazy" to ALL images sitewide (native, no plugin)
add_filter( 'the_content', function ( $content ) {
return str_replace( '<img ', ' <img loading="lazy" ', $content );
} );

// Set max upload size for non-admins (protects your server)
add_filter( ' upload_size_limit', function ( $size ) { return current_user_can( 'manage_options' ) ? $size : 2 *
    MB_IN_BYTES; } );