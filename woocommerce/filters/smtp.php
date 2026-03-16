// Change the "from" name and email on all WP/WC emails
add_filter( 'wp_mail_from', fn() => 'noreply@tuosito.it' );
add_filter( 'wp_mail_from_name', fn() => 'Barletta Shop' );