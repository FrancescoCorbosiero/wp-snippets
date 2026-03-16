add_filter( 'wcpay_elements_appearance', function ( $appearance ) {
$appearance->theme = 'flat';

// Define global variables
$appearance->variables = (object) [
'fontFamily' => 'Inter, sans-serif',
];

// Define styles for various elements
$appearance->rules = (object) [
'.Input' => (object) [
'backgroundColor' => '#FFFFFF',
'border' => '1px solid #ccc',
'height' => '50px',
'padding' => '10px',
'fontFamily' => 'Inter, sans-serif',
'fontSize' => '16px',
],
'.Input-input' => (object) [
'height' => '48px',
'padding' => '10px 12px',
],
];

return $appearance;
} );