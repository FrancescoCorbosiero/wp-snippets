// ─── Quick Add to Cart for Variable Products ───
// Add to Code Snippets → Run everywhere

// Replace default Add to Cart button on shop loop
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_after_shop_loop_item', 'rp_quick_add_button', 10);

function rp_quick_add_button() {
    global $product;

    if ($product->is_type('variable')) {
        echo '<button class="rp-quick-add-btn" data-product-id="' . esc_attr($product->get_id()) . '">AGGIUNGI AL CARRELLO</button>';
    } else {
        // Keep default button for simple products
        woocommerce_template_loop_add_to_cart();
    }
}

// AJAX: get variations
add_action('wp_ajax_rp_get_variations', 'rp_get_variations_handler');
add_action('wp_ajax_nopriv_rp_get_variations', 'rp_get_variations_handler');

function rp_get_variations_handler() {
    $product_id = intval($_GET['product_id'] ?? 0);
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Prodotto non trovato');
    }

    // Get attributes with their options
    $attributes = [];
    foreach ($product->get_variation_attributes() as $attr_name => $options) {
        $label = wc_attribute_label($attr_name, $product);
        $attributes[] = [
            'name'    => $attr_name,
            'label'   => $label,
            'options' => array_values($options),
        ];
    }

    // Get all available variations
    $variations = [];
    foreach ($product->get_available_variations() as $v) {
        $variations[] = [
            'variation_id' => $v['variation_id'],
            'attributes'   => $v['attributes'],
            'price_html' => html_entity_decode(strip_tags($v['price_html'])),
            'is_in_stock'  => $v['is_in_stock'],
            'image'        => $v['image']['thumb_src'] ?? '',
        ];
    }

    wp_send_json_success([
        'title'      => $product->get_name(),
        'price' => html_entity_decode(strip_tags($product->get_price_html())),
        'image'      => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail'),
        'attributes' => $attributes,
        'variations' => $variations,
    ]);
}

// AJAX: add to cart
add_action('wp_ajax_rp_add_to_cart', 'rp_add_to_cart_handler');
add_action('wp_ajax_nopriv_rp_add_to_cart', 'rp_add_to_cart_handler');

function rp_add_to_cart_handler() {
    $product_id   = intval($_POST['product_id'] ?? 0);
    $variation_id = intval($_POST['variation_id'] ?? 0);
    $quantity     = intval($_POST['quantity'] ?? 1);

    if (!$product_id || !$variation_id) {
        wp_send_json_error('Dati mancanti');
    }

    // Get variation attributes
    $variation = wc_get_product($variation_id);
    if (!$variation) {
        wp_send_json_error('Variante non trovata');
    }

    $attributes = $variation->get_variation_attributes();

    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $attributes);

    if ($cart_item_key) {
        // Get updated fragments (mini-cart, count, etc.)
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        wp_send_json_success([
            'message'    => 'Prodotto aggiunto al carrello!',
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'cart_url'   => wc_get_cart_url(),
        ]);
    } else {
        wp_send_json_error('Impossibile aggiungere al carrello');
    }
}

// Frontend
add_action('wp_footer', function () {
    if (!is_shop() && !is_product_taxonomy() && !is_search()) return;
    ?>
    <style>
        /* Trigger button */
        .rp-quick-add-btn {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid #721124;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #721124;
            transition: all 0.2s;
            text-align: center;
            margin-top: 6px;
        }
        .rp-quick-add-btn:hover {
            background: #721124;
            color: #fff;
        }

        /* Overlay */
        .rp-qa-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 99998;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .rp-qa-overlay.active { display: block; }

        /* Modal */
        .rp-qa-modal {
            display: none;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            max-height: 85vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 20px 20px 0 0;
            z-index: 99999;
            box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.15);
            animation: rp-qa-slide-up 0.3s ease;
        }
        .rp-qa-modal.active { display: block; }

        @keyframes rp-qa-slide-up {
            from { transform: translateX(-50%) translateY(100%); }
            to { transform: translateX(-50%) translateY(0); }
        }

        /* Handle bar */
        .rp-qa-handle {
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 4px;
            margin: 10px auto 0;
        }

        .rp-qa-close {
            position: absolute;
            top: 10px;
            right: 14px;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #999;
            line-height: 1;
            transition: color 0.2s;
        }
        .rp-qa-close:hover { color: #721124; }

        /* Product header */
        .rp-qa-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px 20px 12px;
        }
        .rp-qa-header img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 12px;
            background: #f5f5f5;
            flex-shrink: 0;
        }
        .rp-qa-header-info { min-width: 0; }
        .rp-qa-title {
            font-size: 15px;
            font-weight: 700;
            color: #222;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .rp-qa-price {
            font-size: 17px;
            font-weight: 700;
            color: #721124;
            margin-top: 2px;
        }

        /* Attribute selectors */
        .rp-qa-attributes {
            padding: 0 20px;
        }
        .rp-qa-attr-group {
            margin-bottom: 16px;
        }
        .rp-qa-attr-label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .rp-qa-attr-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .rp-qa-attr-option {
            padding: 8px 16px;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
            background: #fff;
            color: #333;
            user-select: none;
            text-align: center;
            min-width: 44px;
        }
        .rp-qa-attr-option:hover {
            border-color: #721124;
            color: #721124;
        }
        .rp-qa-attr-option.selected {
            border-color: #721124;
            background: #721124;
            color: #fff;
        }
        .rp-qa-attr-option.unavailable {
            opacity: 0.3;
            cursor: not-allowed;
            text-decoration: line-through;
            pointer-events: none;
        }

        /* Quantity */
        .rp-qa-quantity-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            margin-bottom: 16px;
        }
        .rp-qa-qty-label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .rp-qa-qty-wrap {
            display: flex;
            align-items: center;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .rp-qa-qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: #f9f9f9;
            cursor: pointer;
            font-size: 18px;
            color: #555;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rp-qa-qty-btn:hover {
            background: #721124;
            color: #fff;
        }
        .rp-qa-qty-value {
            width: 40px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            border: none;
            outline: none;
            color: #333;
        }

        /* Add to cart */
        .rp-qa-footer {
            padding: 16px 20px 24px;
        }
        .rp-qa-add-to-cart {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: #ccc;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: not-allowed;
            transition: all 0.2s;
            pointer-events: none;
        }
        .rp-qa-add-to-cart.ready {
            background: #721124;
            cursor: pointer;
            pointer-events: auto;
        }
        .rp-qa-add-to-cart.ready:hover {
            background: #5a0d1d;
        }
        .rp-qa-add-to-cart.adding {
            background: #999;
            pointer-events: none;
        }
        .rp-qa-add-to-cart.added {
            background: #2e7d32;
            pointer-events: none;
        }

        /* Error */
        .rp-qa-error {
            font-size: 12px;
            color: #c62828;
            text-align: center;
            padding: 0 20px 8px;
            display: none;
        }
        .rp-qa-error.visible { display: block; }

        /* Loading */
        .rp-qa-loading {
            padding: 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
        }

        /* Desktop: center modal instead of bottom sheet */
        @media (min-width: 641px) {
            .rp-qa-modal {
                bottom: auto;
                top: 50%;
                transform: translate(-50%, -50%);
                border-radius: 20px;
                animation: rp-qa-fade-in 0.25s ease;
            }
            @keyframes rp-qa-fade-in {
                from { opacity: 0; transform: translate(-50%, -48%); }
                to { opacity: 1; transform: translate(-50%, -50%); }
            }
        }
    </style>

    <!-- Modal markup -->
    <div class="rp-qa-overlay"></div>
    <div class="rp-qa-modal">
        <div class="rp-qa-handle"></div>
        <button class="rp-qa-close" aria-label="Chiudi">✕</button>
        <div class="rp-qa-content"></div>
    </div>

    <script>
    jQuery(function($) {
        var $overlay = $('.rp-qa-overlay');
        var $modal = $('.rp-qa-modal');
        var $content = $('.rp-qa-content');
        var currentVariations = [];
        var selectedAttrs = {};
        var matchedVariation = null;

        function closeModal() {
            $overlay.removeClass('active');
            $modal.removeClass('active');
            $('body').css('overflow', '');
        }

        $overlay.on('click', closeModal);
        $(document).on('click', '.rp-qa-close', closeModal);
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Open modal
        $(document).on('click', '.rp-quick-add-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');

            selectedAttrs = {};
            matchedVariation = null;
            $content.html('<div class="rp-qa-loading">Caricamento varianti...</div>');
            $overlay.addClass('active');
            $modal.addClass('active');
            $('body').css('overflow', 'hidden');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                data: { action: 'rp_get_variations', product_id: productId },
                success: function(response) {
                    if (!response.success) {
                        $content.html('<div class="rp-qa-loading">Prodotto non trovato</div>');
                        return;
                    }
                    renderModal(response.data, productId);
                }
            });
        });

        function renderModal(data, productId) {
            currentVariations = data.variations;
            selectedAttrs = {};

            var html = '';

            // Header
            html += '<div class="rp-qa-header">';
            html += '<img src="' + data.image + '" alt="' + data.title + '">';
            html += '<div class="rp-qa-header-info">';
            html += '<div class="rp-qa-title">' + data.title + '</div>';
            html += '<div class="rp-qa-price">' + data.price + '</div>';
            html += '</div></div>';

            // Attributes
            html += '<div class="rp-qa-attributes">';
            data.attributes.forEach(function(attr) {
                html += '<div class="rp-qa-attr-group" data-attr="' + attr.name + '">';
                html += '<div class="rp-qa-attr-label">' + attr.label + '</div>';
                html += '<div class="rp-qa-attr-options">';
                attr.options.forEach(function(opt) {
                    html += '<div class="rp-qa-attr-option" data-attr="' + attr.name + '" data-value="' + opt + '">' + opt + '</div>';
                });
                html += '</div></div>';
            });
            html += '</div>';

            // Quantity
            html += '<div class="rp-qa-quantity-row">';
            html += '<span class="rp-qa-qty-label">Quantità</span>';
            html += '<div class="rp-qa-qty-wrap">';
            html += '<button class="rp-qa-qty-btn rp-qa-qty-minus">−</button>';
            html += '<input type="text" class="rp-qa-qty-value" value="1" readonly>';
            html += '<button class="rp-qa-qty-btn rp-qa-qty-plus">+</button>';
            html += '</div></div>';

            // Error + ATC
            html += '<div class="rp-qa-error">Seleziona tutte le opzioni</div>';
            html += '<div class="rp-qa-footer">';
            html += '<button class="rp-qa-add-to-cart" data-product-id="' + productId + '">Seleziona le opzioni</button>';
            html += '</div>';

            $content.html(html);

            // If only one attribute with options, check availability immediately
            updateAvailability();
        }

        // Attribute selection
        $(document).on('click', '.rp-qa-attr-option:not(.unavailable)', function() {
            var attrName = $(this).data('attr');
            var val = $(this).data('value');

            // Toggle
            if (selectedAttrs[attrName] === val) {
                delete selectedAttrs[attrName];
                $(this).removeClass('selected');
            } else {
                selectedAttrs[attrName] = val;
                $(this).siblings().removeClass('selected');
                $(this).addClass('selected');
            }

            updateAvailability();
            updateButton();
        });

        function updateAvailability() {
            // For each attribute group, check which options are still possible
            $('.rp-qa-attr-group').each(function() {
                var groupAttr = $(this).data('attr');

                $(this).find('.rp-qa-attr-option').each(function() {
                    var optValue = $(this).data('value');

                    // Build a test selection: current selections + this option
                    var testAttrs = $.extend({}, selectedAttrs);
                    testAttrs[groupAttr] = optValue;

                    // Check if any variation matches
                    var possible = currentVariations.some(function(v) {
                        return Object.keys(testAttrs).every(function(key) {
                            var vKey = 'attribute_' + key;
                            // Empty attribute in variation means "any"
                            return !v.attributes[vKey] || v.attributes[vKey] === testAttrs[key];
                        }) && v.is_in_stock;
                    });

                    $(this).toggleClass('unavailable', !possible);
                });
            });
        }

        function updateButton() {
            var $btn = $('.rp-qa-add-to-cart');
            var $error = $('.rp-qa-error');
            var totalAttrs = $('.rp-qa-attr-group').length;
            var selectedCount = Object.keys(selectedAttrs).length;

            if (selectedCount < totalAttrs) {
                $btn.removeClass('ready').text('Seleziona le opzioni');
                $error.removeClass('visible');
                matchedVariation = null;
                return;
            }

            // Find matching variation
            matchedVariation = currentVariations.find(function(v) {
                return Object.keys(selectedAttrs).every(function(key) {
                    var vKey = 'attribute_' + key;
                    return !v.attributes[vKey] || v.attributes[vKey] === selectedAttrs[key];
                });
            });

            if (matchedVariation && matchedVariation.is_in_stock) {
                var priceText = matchedVariation.price_html ? ' – ' + matchedVariation.price_html : '';
                $btn.addClass('ready').text('Aggiungi al Carrello' + priceText);
                $error.removeClass('visible');

                // Update image if available
                if (matchedVariation.image) {
                    $('.rp-qa-header img').attr('src', matchedVariation.image);
                }
            } else if (matchedVariation && !matchedVariation.is_in_stock) {
                $btn.removeClass('ready').text('Esaurito');
                $error.removeClass('visible');
            } else {
                $btn.removeClass('ready').text('Combinazione non disponibile');
                $error.addClass('visible');
            }
        }

        // Quantity controls
        $(document).on('click', '.rp-qa-qty-minus', function() {
            var $val = $(this).siblings('.rp-qa-qty-value');
            var current = parseInt($val.val());
            if (current > 1) $val.val(current - 1);
        });
        $(document).on('click', '.rp-qa-qty-plus', function() {
            var $val = $(this).siblings('.rp-qa-qty-value');
            var current = parseInt($val.val());
            $val.val(current + 1);
        });

        // Add to cart
        $(document).on('click', '.rp-qa-add-to-cart.ready', function() {
            var $btn = $(this);
            var productId = $btn.data('product-id');
            var quantity = parseInt($('.rp-qa-qty-value').val()) || 1;

            if (!matchedVariation) return;

            $btn.removeClass('ready').addClass('adding').text('Aggiunta in corso...');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                method: 'POST',
                data: {
                    action: 'rp_add_to_cart',
                    product_id: productId,
                    variation_id: matchedVariation.variation_id,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        $btn.removeClass('adding').addClass('added').text('✓ Aggiunto! (' + response.data.cart_count + ' nel carrello)');

                        // Update WooCommerce cart fragments
                        $(document.body).trigger('wc_fragment_refresh');

                        setTimeout(function() {
                            closeModal();
                        }, 1500);
                    } else {
                        $btn.removeClass('adding').addClass('ready').text('Errore – Riprova');
                    }
                },
                error: function() {
                    $btn.removeClass('adding').addClass('ready').text('Errore – Riprova');
                }
            });
        });
    });
    </script>
    <?php
});