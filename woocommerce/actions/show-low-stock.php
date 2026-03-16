// Show stock count only when low (e.g. < 5 items left) add_filter( 'woocommerce_get_availability' , function (
    $availability, $product ) { if ( $product->managing_stock() && $product->get_stock_quantity() < 5 && $product->
        get_stock_quantity() > 0 ) {
        $availability['availability'] = '⚠️ Solo ' . $product->get_stock_quantity() . ' rimasti!';
        $availability['class'] = 'low-stock';
        }
        return $availability;
        }, 10, 2 );