// Free shipping progress bar message
add_action( 'woocommerce_before_cart', function () {
$threshold = 50;
$total = WC()->cart->subtotal;
$remaining = $threshold - $total;

if ( $remaining > 0 ) {
echo '<div style="background:#fff3cd;padding:10px 16px;border-radius:6px;margin-bottom:16px;">
    🚚 Aggiungi <strong>' . wc_price( $remaining ) . '</strong> per la spedizione gratuita!
</div>';
} else {
echo '<div style="background:#d4edda;padding:10px 16px;border-radius:6px;margin-bottom:16px;">
    ✅ Hai sbloccato la <strong>spedizione gratuita</strong>!
</div>';
}
} );