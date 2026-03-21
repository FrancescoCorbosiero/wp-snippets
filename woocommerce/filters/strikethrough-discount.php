/**
 * Fix: show strikethrough regular price on variable products
 * that have only one (or all) discounted variation(s).
 *
 * WooCommerce normally skips the <del> tag when all variants
 * share the same sale price, because the range min === max.
 * This filter rebuilds the HTML correctly in that case.
 *
 * Hook: woocommerce_variable_price_html
 */
add_filter( 'woocommerce_variable_price_html', 'resell_variable_sale_price_html', 10, 2 );

function resell_variable_sale_price_html( $price, $product ) {
    // Active sale price range (what the customer pays)
    $min_sale    = (float) $product->get_variation_price( 'min', true );
    $max_sale    = (float) $product->get_variation_price( 'max', true );

    // Regular price range (before any discount)
    $min_regular = (float) $product->get_variation_regular_price( 'min', true );
    $max_regular = (float) $product->get_variation_regular_price( 'max', true );

    // No sale active at all — return default
    if ( $min_sale === $min_regular && $max_sale === $max_regular ) {
        return $price;
    }

    // All variants share the exact same sale + regular price
    // → single clean strikethrough, no "A partire da"
    if ( $min_sale === $max_sale && $min_regular === $max_regular ) {
        return sprintf(
            '<del aria-hidden="true">%s</del> <ins>%s</ins>',
            wc_price( $min_regular ),
            wc_price( $min_sale )
        );
    }

    // Mixed: some variants on sale, some not
    // → show range with lowest sale price and highest regular price
    return sprintf(
        '<del aria-hidden="true">%s</del> %s',
        wc_price( $max_regular ),
        sprintf(
            /* translators: %s: price */
            __( 'A partire da %s', 'woocommerce' ),
            wc_price( $min_sale )
        )
    );
}