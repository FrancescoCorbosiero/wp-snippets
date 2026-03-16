/**
* WooCommerce Recensioni Italiane - VERSIONE MIGLIORATA SENZA DUPLICATI
* Risolve il problema di nomi e recensioni duplicate
*/

function aggiungi_recensioni_senza_duplicati() {

// ============================================
// CONFIGURAZIONE
// ============================================
$numero_prodotti = 10;
$recensioni_min = 2;
$recensioni_max = 5;
$percentuale_verificati = 70;

// ============================================
// DATABASE AMPIO DI RECENSIONI REALISTICHE
// Ogni recensione è unica e specifica
// ============================================

$recensioni_5_stelle = array(
'Prodotto eccellente! Sono rimasto davvero colpito dalla qualità. Spedizione rapidissima, arrivato in perfette
condizioni. Consiglio a occhi chiusi!',
'Fantastico acquisto! Utilizzo quotidiano da ormai 3 settimane e non ho riscontrato alcun problema. Qualità-prezzo
imbattibile, davvero soddisfatto.',
'Semplicemente perfetto. Corrisponde al 100% alla descrizione e le foto rendono giustizia al prodotto. Imballaggio
curatissimo. Venditore top!',
'Molto contenta dell\'acquisto! È esattamente ciò che cercavo. Materiali di ottima qualità e finiture impeccabili. Lo
ricomprerei senza dubbio.',
'Super soddisfatto! Arrivato prima del previsto e in condizioni perfette. Qualità superiore alle mie aspettative.
Complimenti al venditore!',
'Eccezionale! Dopo averlo testato per un mese posso dire che è un ottimo investimento. Funziona perfettamente e sembra
molto resistente.',
'Davvero un bel prodotto. La qualità si vede e si sente. Servizio clienti disponibile e professionale. Esperienza
d\'acquisto a 5 stelle!',
'Perfetto in tutto! Dalla qualità costruttiva alla velocità di spedizione. È il terzo ordine che faccio da questo
negozio e mi confermo molto soddisfatto.',
'Acquisto azzeccatissimo! Funziona benissimo e ha superato le mie aspettative. Prezzo giusto per un prodotto di questa
qualità. Consigliatissimo!',
'Ottima scelta! Cercavo proprio qualcosa del genere da tempo. Materiali robusti e design curato. Arrivato in 48 ore.
Tutto perfetto!',
);

$recensioni_4_stelle = array(
'Buon prodotto complessivamente. La qualità è buona, anche se il prezzo potrebbe essere leggermente più competitivo.
Comunque soddisfatto.',
'Tutto ok! Arrivato nei tempi indicati. Il prodotto corrisponde alla descrizione, forse dal vivo sembra leggermente più
piccolo ma va benissimo.',
'Prodotto valido. Fa esattamente quello che deve fare. Unica pecca: le istruzioni potrebbero essere più dettagliate, ma
nulla di grave.',
'Soddisfatto dell\'acquisto. Qualità buona e prezzo onesto. Ha impiegato 4 giorni invece di 2-3 per arrivare ma il
prodotto vale l\'attesa.',
'Bene! Il prodotto funziona correttamente. L\'unico appunto è sulla confezione che era leggermente ammaccata, ma il
contenuto era integro.',
'Discreto acquisto. Nel complesso va bene anche se mi aspettavo finiture leggermente migliori. Per il resto nessun
problema, lo consiglio.',
'Buona qualità generale. Alcune piccole imperfezioni estetiche ma niente che comprometta la funzionalità. Rapporto
qualità-prezzo accettabile.',
'Prodotto come da descrizione. Arrivato integro e funzionante. Avrei gradito un imballaggio più curato ma per il prezzo
pagato va più che bene.',
'Sono soddisfatto. Fa il suo dovere senza troppi fronzoli. Non è il massimo in termini di design ma la sostanza c\'è
tutta.',
'Ok nel complesso. Qualità discreta, niente di eccezionale ma nemmeno deludente. Prezzo in linea con il mercato. Va
bene.',
);

$recensioni_3_stelle = array(
'Nella media. Svolge la sua funzione base ma senza particolari eccellenze. Il prezzo è comunque adeguato a quello che
offre.',
'Accettabile. Non mi ha entusiasmato ma nemmeno deluso. Per l\'uso che ne devo fare va bene così. Spedizione nei
tempi.',
'Prodotto sufficiente. Alcune finiture lasciano un po\' a desiderare ma funziona. Forse mi aspettavo qualcosina in
più.',
'Così così. Va bene per un uso occasionale, ma se serve qualcosa di più professionale forse è meglio guardare altro.
Comunque utilizzabile.',
'Discreto. Corrisponde alla descrizione anche se la qualità costruttiva non è il massimo. Per il prezzo ci può stare.',
);

// ============================================
// DATABASE AMPIO DI NOMI ITALIANI
// Mix realistico di nomi comuni e meno comuni
// ============================================

$nomi_italiani = array(
// Nomi molto comuni
'Marco Rossi', 'Giulia Bianchi', 'Luca Ferrari', 'Francesca Romano',
'Alessandro Russo', 'Martina Esposito', 'Andrea Colombo', 'Sara Ricci',
'Matteo Marino', 'Chiara Greco', 'Davide Costa', 'Federica Conti',
'Simone Giordano', 'Elena Lombardi', 'Giuseppe Moretti', 'Laura Bruno',

// Nomi comuni
'Antonio De Luca', 'Valentina Rizzo', 'Roberto Barbieri', 'Silvia Ferrara',
'Stefano Gallo', 'Alessandra Fontana', 'Michele Santoro', 'Paola Marini',
'Daniele Caruso', 'Monica Villa', 'Fabio Leone', 'Cristina Serra',

// Mix varietà
'Nicola Vitale', 'Anna Pellegrini', 'Francesco Benedetti', 'Giorgia Monti',
'Paolo Marchetti', 'Elisa Rinaldi', 'Riccardo Battaglia', 'Claudia Ferri',
'Alberto Parisi', 'Maria Grazia Sala', 'Emanuele Fabbri', 'Lucia Morelli',

// Più varietà
'Tommaso Piras', 'Eleonora Vitali', 'Gabriele Orlando', 'Serena Rizzi',
'Lorenzo Martinelli', 'Beatrice Gentile', 'Filippo Mariani', 'Veronica Bassi',
'Diego Cattaneo', 'Ilaria Rossetti', 'Giacomo Riva', 'Roberta Mancini',

// Ancora più varietà
'Massimo Bellini', 'Carla Bernardi', 'Vincenzo Pagano', 'Teresa Grassi',
'Enrico Silvestri', 'Daniela Fiorentini', 'Claudio Lombardo', 'Patrizia Neri',
'Gianluca Milani', 'Sabrina Rocca', 'Alessio Caputo', 'Barbara De Santis'
);

// ============================================
// VERIFICA WOOCOMMERCE
// ============================================

if (!class_exists('WooCommerce')) {
echo '<div class="notice notice-error">
    <p><strong>❌ ERRORE:</strong> WooCommerce non è attivo!</p>
</div>';
return;
}

// ============================================
// CERCA PRODOTTI
// ============================================

$product_ids = get_posts(array(
'post_type' => 'product',
'posts_per_page' => $numero_prodotti,
'post_status' => 'publish',
'orderby' => 'rand',
'fields' => 'ids'
));

if (empty($product_ids)) {
echo '<div class="notice notice-error">
    <p><strong>❌ ERRORE:</strong> Nessun prodotto trovato!</p>
</div>';
return;
}

// ============================================
// AGGIUNGI RECENSIONI SENZA DUPLICATI
// ============================================

$statistiche = array(
'prodotti' => 0,
'recensioni' => 0,
'verificati' => 0
);

// Mescola tutti gli array UNA VOLTA all'inizio
shuffle($nomi_italiani);
shuffle($recensioni_5_stelle);
shuffle($recensioni_4_stelle);
shuffle($recensioni_3_stelle);

// Combina tutte le recensioni in un unico array con rating
$tutte_recensioni = array();
foreach ($recensioni_5_stelle as $rec) {
$tutte_recensioni[] = array('content' => $rec, 'rating' => 5);
}
foreach ($recensioni_4_stelle as $rec) {
$tutte_recensioni[] = array('content' => $rec, 'rating' => 4);
}
foreach ($recensioni_3_stelle as $rec) {
$tutte_recensioni[] = array('content' => $rec, 'rating' => 3);
}
shuffle($tutte_recensioni);

// Indici per tracciare l'uso
$indice_nomi = 0;
$indice_recensioni = 0;

foreach ($product_ids as $product_id) {

$product = wc_get_product($product_id);
if (!$product) continue;

$num_recensioni = rand($recensioni_min, $recensioni_max);

// Array per tracciare nomi usati in QUESTO prodotto
$nomi_usati_prodotto = array();
$recensioni_usate_prodotto = array();

for ($i = 0; $i < $num_recensioni; $i++) { // Prendi il prossimo nome disponibile che non è stato usato in questo
    prodotto $nome=null; $tentativi=0; while ($nome===null && $tentativi < 100) {
    $nome_candidato=$nomi_italiani[$indice_nomi % count($nomi_italiani)]; $indice_nomi++; // Controlla se questo nome è
    già stato usato per QUESTO prodotto if (!in_array($nome_candidato, $nomi_usati_prodotto)) { $nome=$nome_candidato;
    $nomi_usati_prodotto[]=$nome; } $tentativi++; } if (!$nome) continue; // Safety check // Prendi la prossima
    recensione disponibile che non è stata usata in questo prodotto $recensione=null; $tentativi=0; while
    ($recensione===null && $tentativi < 100) { $rec_candidata=$tutte_recensioni[$indice_recensioni %
    count($tutte_recensioni)]; $indice_recensioni++; // Controlla se questa recensione è già stata usata per QUESTO
    prodotto if (!in_array($rec_candidata['content'], $recensioni_usate_prodotto)) { $recensione=$rec_candidata;
    $recensioni_usate_prodotto[]=$rec_candidata['content']; } $tentativi++; } if (!$recensione) continue; // Safety
    check // Genera email univoca $nome_pulito=strtolower(str_replace(' ', ' .', remove_accents($nome)));
    $email=$nome_pulito . rand(100, 999) . '@example.com' ; // Aggiungi numero per unicità // Data casuale
    $giorni_fa=rand(3, 120); $data=date('Y-m-d H:i:s', strtotime("-{$giorni_fa} days")); // Acquisto verificato
    $is_verificato=(rand(1, 100) <=$percentuale_verificati) ? 1 : 0; // Inserisci recensione
    $comment_data=array( 'comment_post_ID'=> $product_id,
    'comment_author' => $nome,
    'comment_author_email' => $email,
    'comment_author_url' => '',
    'comment_content' => $recensione['content'],
    'comment_type' => 'review',
    'comment_parent' => 0,
    'user_id' => 0,
    'comment_date' => $data,
    'comment_date_gmt' => get_gmt_from_date($data),
    'comment_approved' => 1,
    'comment_agent' => 'WooCommerce',
    );

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
    update_comment_meta($comment_id, 'rating', $recensione['rating']);

    if ($is_verificato) {
    update_comment_meta($comment_id, 'verified', 1);
    $statistiche['verificati']++;
    }

    $statistiche['recensioni']++;
    }
    }

    // Aggiorna cache prodotto
    wc_delete_product_transients($product_id);
    if (class_exists('WC_Comments')) {
    WC_Comments::clear_transients($product_id);
    }

    $statistiche['prodotti']++;
    }

    // ============================================
    // MOSTRA RISULTATI
    // ============================================

    if ($statistiche['recensioni'] > 0) {
    echo '<div class="notice notice-success is-dismissible" style="padding: 20px;">';
        echo '<h2 style="margin-top: 0;">✅ Recensioni Aggiunte con Successo!</h2>';
        echo '<div style="background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0;">';
            echo '<h3 style="margin-top: 0;">📊 Statistiche:</h3>';
            echo '<ul style="font-size: 15px;">';
                echo '<li><strong>Prodotti elaborati:</strong> ' . $statistiche['prodotti'] . '</li>';
                echo '<li><strong>Recensioni totali:</strong> ' . $statistiche['recensioni'] . '</li>';
                echo '<li><strong>Acquisti verificati:</strong> ' . $statistiche['verificati'] . '</li>';
                echo '<li><strong>Media per prodotto:</strong> ' . round($statistiche['recensioni'] /
                    $statistiche['prodotti'], 1) . ' recensioni</li>';
                echo '</ul>';
            echo '</div>';

        echo '<div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0;">';
            echo '<h3 style="margin-top: 0;">⚠️ Caratteristiche:</h3>';
            echo '<ul>';
                echo '<li>✓ Ogni recensione è <strong>unica</strong> per ogni prodotto</li>';
                echo '<li>✓ Ogni nome è <strong>unico</strong> per ogni prodotto</li>';
                echo '<li>✓ Email generate con numeri random per unicità</li>';
                echo '<li>✓ Date distribuite negli ultimi 120 giorni</li>';
                echo '<li>✓ ' . $percentuale_verificati . '% marcate come "acquisto verificato"</li>';
                echo '</ul>';
            echo '</div>';

        echo '<p style="font-size: 16px;"><strong>🎯 Ora puoi:</strong></p>';
        echo '<ol style="font-size: 15px;">';
            echo '<li>Andare su <a href="' . admin_url('edit.php?post_type=product') . '"
                    style="font-weight: bold;">Prodotti</a> e controllare le recensioni</li>';
            echo '<li>Visitare il frontend del tuo sito per vedere come appaiono</li>';
            echo '<li style="color: #d63638; font-weight: bold;">DISATTIVARE questo snippet per evitare che si riesegua!
            </li>';
            echo '</ol>';
        echo '</div>';
    }
    }

    // Esecuzione con controllo di sicurezza
    if (get_option('wc_recensioni_no_duplicati_v2') !== 'yes') {
    add_action('admin_init', 'aggiungi_recensioni_senza_duplicati');
    add_action('admin_notices', 'aggiungi_recensioni_senza_duplicati');
    update_option('wc_recensioni_no_duplicati_v2', 'yes');
    }

    // Per resettare: delete_option('wc_recensioni_no_duplicati_v2');