add_action( 'wp', function () {

if ( ! is_product() ) return;

global $product;
$product = wc_get_product( get_the_ID() );

if ( ! $product || ! $product->is_type( 'variable' ) ) return;

$on_sale = [];

foreach ( $product->get_children() as $var_id ) {
$variation = wc_get_product( $var_id );
if ( ! $variation || ! $variation->is_on_sale() ) continue;
foreach ( $variation->get_attributes() as $attr => $value ) {
if ( ! $value ) continue;
$on_sale[ 'attribute_' . $attr ][] = $value;
}
}

if ( empty( $on_sale ) ) return;

$selectors = [];
foreach ( $on_sale as $attr => $values ) {
foreach ( array_unique( $values ) as $value ) {
$selectors[] = sprintf(
'[data-attribute="%s"] button[data-attribute-value="%s"]',
esc_attr( $attr ),
esc_attr( $value )
);
}
}

$selector_str = implode( ', ', $selectors );

add_action( 'wp_head', function () use ( $selector_str ) {

$after_selectors = implode( ', ', array_map(
fn( $s ) => $s . '::after',
explode( ', ', $selector_str )
) );

echo '<style>
    .cgkit-swatch {
        position: relative;
        z-index: 0;
    }

    ' . $selector_str . ' {
        border-color: #e44 !important;
        z-index: 1;
    }

    ' . $after_selectors . ' {
        content: "%";
        position: absolute;
        top: 2px;
        right: 2px;
        background: #e44;
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        line-height: 1;
        padding: 1px 2px;
        border-radius: 2px;
        pointer-events: none;
        z-index: 2;
    }
</style>';

} );

} );