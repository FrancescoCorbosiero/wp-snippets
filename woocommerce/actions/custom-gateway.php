add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
$gateways[] = 'WC_My_Custom_Gateway';
return $gateways;
} );

add_action( 'plugins_loaded', function () {
class WC_My_Custom_Gateway extends WC_Payment_Gateway {

public function __construct() {
$this->id = 'my_custom_gateway';
$this->method_title = 'My Custom Gateway';
$this->method_description = 'Accepts custom payments.';
$this->has_fields = false; // set true if you need a payment form

$this->init_form_fields();
$this->init_settings();

$this->title = $this->get_option( 'title' );
$this->description = $this->get_option( 'description' );

add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
[ $this, 'process_settings' ] );
}

public function init_form_fields() {
$this->form_fields = [
'enabled' => [ 'title' => 'Enable', 'type' => 'checkbox', 'default' => 'yes' ],
'title' => [ 'title' => 'Title', 'type' => 'text', 'default' => 'Custom Payment' ],
'description' => [ 'title' => 'Description', 'type' => 'textarea' ],
];
}

public function process_payment( $order_id ) {
$order = wc_get_order( $order_id );

// Your payment logic here
$order->payment_complete();
$order->add_order_note( 'Payment processed via My Custom Gateway.' );

return [
'result' => 'success',
'redirect' => $this->get_return_url( $order ),
];
}
}
} );
```

---

### 6. The Full Gateway ID Reference

| Gateway | ID |
|---|---|
| Cash on Delivery | `cod` |
| Bank Transfer (BACS) | `bacs` |
| Cheque | `cheque` |
| Stripe | `stripe` |
| PayPal Standard | `paypal` |
| PayPal Payments Pro | `paypal_pro` |
| Klarna | `klarna_payments` |
| Satispay | `satispay` |

> You can always dump available IDs with: `print_r( array_keys(
WC()->payment_gateways()->get_available_payment_gateways() ) );`

---

### Mental Model
```
woocommerce_available_payment_gateways ← control WHICH gateways appear
woocommerce_gateway_title ← control WHAT they're called
woocommerce_gateway_description ← control WHAT they say
woocommerce_gateway_icon ← control HOW they look
woocommerce_payment_complete ← react AFTER payment
process_payment() ← your own gateway LOGIC