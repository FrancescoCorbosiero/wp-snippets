    /**
 * Shopify-Style Announcement Text Slider
 * ─────────────────────────────────────────
 * Renders as the FIRST visible element on the page,
 * above the Shoptimizer header and everything else.
 *
 * ✦ Hidden on: Cart, Checkout, My Account, Order endpoints, Thank You page
 * ✦ Hidden when: Shoptimizer drawer/mini-cart is open
 *
 * Usage: Add as a PHP snippet in Code Snippets plugin.
 *        Set to run on the Frontend only.
 */

add_action( 'wp_body_open', 'custom_announcement_slider', 1 );

function custom_announcement_slider() {

    // ═══════════════════════════════════════════
    // 🚫  SKIP on these WooCommerce pages
    // ═══════════════════════════════════════════
    if ( function_exists( 'is_cart' ) && is_cart() )                         return;
    if ( function_exists( 'is_checkout' ) && is_checkout() )                 return;
    if ( function_exists( 'is_account_page' ) && is_account_page() )         return;
    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() )   return;
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) return;

    // Uncomment any of these to also hide on those pages:
    // if ( function_exists( 'is_product' ) && is_product() )               return; // single product
    // if ( function_exists( 'is_shop' ) && is_shop() )                     return; // shop archive
    // if ( function_exists( 'is_product_category' ) && is_product_category() ) return; // category pages
    // if ( function_exists( 'is_product_tag' ) && is_product_tag() )       return; // tag pages

    // ═══════════════════════════════════════════
    // ✏️  SETTINGS — change these freely
    // ═══════════════════════════════════════════
	$messages = [

		"SPEDIZIONE IN TUTTA EUROPA",

		"New release 2026",

		"Iscriviti alla newsletter e ottieni 10€ di sconto",

		"SHOES & CLOTHING RESELLING",

		"Reso in 14 giorni",

	  ];

    $bg_color     = '#1a1a1a';
    $text_color   = '#ffffff';
    $accent_color = '#e8c547';
    $speed        = '28s';       // lower = faster
    $font_size    = '13px';
    $separator    = '✦';
    // ═══════════════════════════════════════════

    // Build the repeated item markup (duplicate for seamless loop)
    $items_html = '';
    foreach ( $messages as $msg ) {
        $items_html .= '<span class="cta-slider__item">' . esc_html( $msg ) . '</span>';
    }
    $track_html = $items_html . $items_html;

    ?>
    <div id="cta-slider" style="
        --s-bg: <?php echo $bg_color; ?>;
        --s-text: <?php echo $text_color; ?>;
        --s-accent: <?php echo $accent_color; ?>;
        --s-speed: <?php echo $speed; ?>;
        --s-fs: <?php echo $font_size; ?>;
        --s-sep: '<?php echo $separator; ?>';
    ">
        <div class="cta-slider__fade cta-slider__fade--left"></div>
        <div class="cta-slider__fade cta-slider__fade--right"></div>
        <div class="cta-slider__track">
            <?php echo $track_html; ?>
        </div>
    </div>

    <style>
        /* ── Container ── */
        #cta-slider {
            background: var(--s-bg);
            overflow: hidden;
            position: relative;
            width: 100%;
            z-index: 99999;
            box-sizing: border-box;
            line-height: 1;
            transition: max-height 0.35s ease, opacity 0.3s ease;
            max-height: 50px;
            opacity: 1;
        }

        /* ── Hidden state (drawer cart open) ── */
        #cta-slider.cta-slider--hidden {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* ── Fade edges ── */
        .cta-slider__fade {
            position: absolute;
            top: 0;
            width: 50px;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        .cta-slider__fade--left  { left: 0;  background: linear-gradient(to right, var(--s-bg), transparent); }
        .cta-slider__fade--right { right: 0; background: linear-gradient(to left,  var(--s-bg), transparent); }

        /* ── Track ── */
        .cta-slider__track {
            display: flex;
            align-items: center;
            white-space: nowrap;
            width: max-content;
            padding: 11px 0;
            animation: ctaSlide var(--s-speed) linear infinite;
        }

        #cta-slider:hover .cta-slider__track {
            animation-play-state: paused;
        }

        /* ── Individual items ── */
        .cta-slider__item {
            display: inline-flex;
            align-items: center;
            gap: 2.5rem;
            padding: 0 2.5rem;
            font-family: inherit;
            font-size: var(--s-fs);
            font-weight: 500;
            color: var(--s-text);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1;
        }

        .cta-slider__item::before {
            content: var(--s-sep);
            color: var(--s-accent);
            font-size: calc(var(--s-fs) * 0.8);
        }

        /* ── Keyframes ── */
        @keyframes ctaSlide {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

    <script>
    (function () {
        var slider = document.getElementById('cta-slider');
        if (!slider) return;

        /**
         * Shoptimizer Drawer Cart Detection
         * ──────────────────────────────────
         * Shoptimizer toggles body classes and drawer element visibility
         * when the mini-cart is opened. We watch for all known patterns.
         */

        var BODY_CLASSES = [
            'drawer-open',
            'cart-drawer-open',
            'has-active-drawer',
            'cartDrawerOpen',
            'mini-cart-open'
        ];

        var DRAWER_SELECTORS = [
            '.cart-drawer.active',
            '.cart-drawer.open',
            '.cart-drawer.is-open',
            '#cart-drawer.active',
            '#cart-drawer.open',
            '.shoptimizer-mini-cart.active',
            '.slide-in-mini-cart.open',
            '.csuspended-cart.is-visible'
        ];

        function isDrawerOpen() {
            // 1. Check body classes
            for (var i = 0; i < BODY_CLASSES.length; i++) {
                if (document.body.classList.contains(BODY_CLASSES[i])) return true;
            }
            // 2. Check drawer element selectors
            for (var j = 0; j < DRAWER_SELECTORS.length; j++) {
                var el = document.querySelector(DRAWER_SELECTORS[j]);
                if (el) {
                    var style = getComputedStyle(el);
                    if (style.display !== 'none' &&
                        style.visibility !== 'hidden' &&
                        parseFloat(style.opacity) > 0) {
                        return true;
                    }
                }
            }
            // 3. Shoptimizer often uses a visible overlay when drawer is open
            var overlay = document.querySelector('.cart-drawer-overlay, .drawer-overlay, .shoptimizer-overlay');
            if (overlay) {
                var oStyle = getComputedStyle(overlay);
                if (oStyle.display !== 'none' && oStyle.visibility !== 'hidden' && parseFloat(oStyle.opacity) > 0) {
                    return true;
                }
            }
            return false;
        }

        function update() {
            slider.classList.toggle('cta-slider--hidden', isDrawerOpen());
        }

        // Watch <body> class changes (primary Shoptimizer trigger)
        var bodyObs = new MutationObserver(update);
        bodyObs.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Watch DOM for drawer elements appearing / changing state
        var domObs = new MutationObserver(update);
        domObs.observe(document.documentElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        // Initial check
        update();
    })();
    </script>
    <?php
}