// Show estimated delivery date on product page
add_action( 'woocommerce_single_product_summary', function () {
$days = 2; // your average delivery days
$date = date_i18n( 'l j F', strtotime( "+{$days} weekdays" ) );
echo '<p style="color:#2e7d32;font-size:.9em;">
    📦 Consegna stimata entro: <strong>' . $date . '</strong>
</p>';
}, 25 );