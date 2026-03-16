add_filter('woocommerce_variable_price_html', 'custom_variable_price', 10, 2);
// ↑ removed woocommerce_variable_sale_price_html

function custom_variable_price($price, $product) {
$min_price = $product->get_variation_price('min', true);
$max_price = $product->get_variation_price('max', true);

// If all variants share the same price, show it plainly with no label
if ($min_price === $max_price) {
return wc_price($min_price);
}

return sprintf(
__('A partire da %s', 'woocommerce'),
wc_price($min_price)
);
}