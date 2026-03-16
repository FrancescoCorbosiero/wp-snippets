// ─── Quick View Modal for WooCommerce ───
// Add to Code Snippets → Run everywhere

// Inject Quick View button on product card image area
remove_action('woocommerce_after_shop_loop_item', 'rp_quick_view_button', 9);
add_action('woocommerce_before_shop_loop_item_title', 'rp_quick_view_button', 11);

function rp_quick_view_button() {
    global $product;
    echo '<button class="rp-quick-view-btn" data-product-id="' . esc_attr($product->get_id()) . '" aria-label="Quick View">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
    </button>';
}

// AJAX handler
add_action('wp_ajax_rp_quick_view', 'rp_quick_view_handler');
add_action('wp_ajax_nopriv_rp_quick_view', 'rp_quick_view_handler');

function rp_quick_view_handler() {
    $product_id = intval($_GET['product_id'] ?? 0);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_send_json_error('Prodotto non trovato');
    }

    // Images
    $images = [];
    $main_img = wp_get_attachment_image_url($product->get_image_id(), 'medium_large');
    if ($main_img) $images[] = $main_img;

    $gallery_ids = $product->get_gallery_image_ids();
    foreach (array_slice($gallery_ids, 0, 4) as $gid) {
        $url = wp_get_attachment_image_url($gid, 'medium_large');
        if ($url) $images[] = $url;
    }

    // Attributes
    $attributes = [];
    foreach ($product->get_attributes() as $attr) {
        $attributes[] = [
            'label' => wc_attribute_label($attr->get_name()),
            'value' => $product->get_attribute($attr->get_name()),
        ];
    }

    // Categories
    $categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']);

    wp_send_json_success([
        'title'       => $product->get_name(),
        'url'         => get_permalink($product_id),
        'price'       => html_entity_decode(strip_tags($product->get_price_html())),
        'short_desc'  => wpautop($product->get_short_description()),
        'images'      => $images,
        'in_stock'    => $product->is_in_stock(),
        'stock_text'  => $product->is_in_stock() ? 'Disponibile' : 'Esaurito',
        'attributes'  => $attributes,
        'categories'  => implode(', ', $categories),
        'sku'         => $product->get_sku(),
    ]);
}

// Frontend
add_action('wp_footer', function () {
    if (!is_shop() && !is_product_taxonomy() && !is_search()) return;
    ?>
    <style>
        /* Product card needs relative positioning */
        .woocommerce ul.products li.product,
        .woocommerce-page ul.products li.product {
            position: relative;
        }

        /* Mini icon button */
        .rp-quick-view-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            opacity: 0;
            transform: scale(0.8);
            pointer-events: none;
        }

        /* Show on card hover */
        .woocommerce ul.products li.product:hover .rp-quick-view-btn,
        .woocommerce-page ul.products li.product:hover .rp-quick-view-btn {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        /* Always visible on touch devices */
        @media (hover: none) {
            .rp-quick-view-btn {
                opacity: 1;
                transform: scale(1);
                pointer-events: auto;
            }
        }

        .rp-quick-view-btn:hover {
            background: #721124;
            color: #fff;
            box-shadow: 0 4px 12px rgba(114, 17, 36, 0.3);
            transform: scale(1.1);
        }

        /* Overlay */
        .rp-qv-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 99998;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .rp-qv-overlay.active { display: block; }

        /* Modal */
        .rp-qv-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 95%;
            max-width: 800px;
            max-height: 85vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            z-index: 99999;
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.2);
        }
        .rp-qv-modal.active { display: block; }

        .rp-qv-close {
            position: absolute;
            top: 12px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            z-index: 2;
            transition: color 0.2s;
            line-height: 1;
        }
        .rp-qv-close:hover { color: #721124; }

        .rp-qv-body {
            display: flex;
            gap: 0;
        }

        /* Gallery */
        .rp-qv-gallery {
            flex: 1;
            min-width: 0;
            background: #f9f9f9;
            border-radius: 16px 0 0 16px;
            overflow: hidden;
        }
        .rp-qv-main-img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            display: block;
        }
        .rp-qv-thumbs {
            display: flex;
            gap: 6px;
            padding: 8px;
            overflow-x: auto;
        }
        .rp-qv-thumb {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }
        .rp-qv-thumb:hover,
        .rp-qv-thumb.active {
            border-color: #721124;
        }

        /* Info */
        .rp-qv-info {
            flex: 1;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .rp-qv-cats {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .rp-qv-title {
            font-size: 20px;
            font-weight: 700;
            color: #222;
            line-height: 1.3;
        }
        .rp-qv-price {
            font-size: 22px;
            font-weight: 700;
            color: #721124;
        }
        .rp-qv-stock {
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            width: fit-content;
        }
        .rp-qv-stock.in-stock {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .rp-qv-stock.out-of-stock {
            background: #fbe9e7;
            color: #c62828;
        }
        .rp-qv-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }
        .rp-qv-desc p { margin: 0 0 8px; }
        .rp-qv-attrs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .rp-qv-attr {
            font-size: 12px;
            padding: 4px 10px;
            background: #f5f5f5;
            border-radius: 6px;
            color: #555;
        }
        .rp-qv-attr strong {
            color: #333;
        }
        .rp-qv-sku {
            font-size: 11px;
            color: #bbb;
        }
        .rp-qv-view-full {
            display: inline-block;
            margin-top: auto;
            padding: 12px 24px;
            background: #721124;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: background 0.2s;
        }
        .rp-qv-view-full:hover {
            background: #5a0d1d;
            color: #fff;
        }

        /* Loading */
        .rp-qv-loading {
            padding: 60px;
            text-align: center;
            color: #999;
            font-size: 14px;
            width: 100%;
        }

        /* Mobile */
        @media (max-width: 640px) {
            .rp-qv-body { flex-direction: column; }
            .rp-qv-gallery { border-radius: 16px 16px 0 0; }
            .rp-qv-info { padding: 20px 16px; }
            .rp-qv-title { font-size: 18px; }
        }
    </style>

    <!-- Modal markup -->
    <div class="rp-qv-overlay"></div>
    <div class="rp-qv-modal">
        <button class="rp-qv-close" aria-label="Chiudi">✕</button>
        <div class="rp-qv-content"></div>
    </div>

    <script>
    jQuery(function($) {
        var $overlay = $('.rp-qv-overlay');
        var $modal = $('.rp-qv-modal');
        var $content = $('.rp-qv-content');

        function closeModal() {
            $overlay.removeClass('active');
            $modal.removeClass('active');
            $('body').css('overflow', '');
        }

        $overlay.on('click', closeModal);
        $('.rp-qv-close').on('click', closeModal);
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        $(document).on('click', '.rp-quick-view-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');

            $content.html('<div class="rp-qv-loading">Caricamento...</div>');
            $overlay.addClass('active');
            $modal.addClass('active');
            $('body').css('overflow', 'hidden');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                data: { action: 'rp_quick_view', product_id: productId },
                success: function(response) {
                    if (!response.success) {
                        $content.html('<div class="rp-qv-loading">Prodotto non trovato</div>');
                        return;
                    }

                    var p = response.data;
                    var html = '<div class="rp-qv-body">';

                    // Gallery
                    html += '<div class="rp-qv-gallery">';
                    if (p.images.length) {
                        html += '<img src="' + p.images[0] + '" class="rp-qv-main-img" alt="' + p.title + '">';
                        if (p.images.length > 1) {
                            html += '<div class="rp-qv-thumbs">';
                            p.images.forEach(function(img, i) {
                                html += '<img src="' + img + '" class="rp-qv-thumb' + (i === 0 ? ' active' : '') + '" data-src="' + img + '">';
                            });
                            html += '</div>';
                        }
                    }
                    html += '</div>';

                    // Info
                    html += '<div class="rp-qv-info">';
                    if (p.categories) html += '<div class="rp-qv-cats">' + p.categories + '</div>';
                    html += '<div class="rp-qv-title">' + p.title + '</div>';
                    html += '<div class="rp-qv-price">' + p.price + '</div>';
                    html += '<div class="rp-qv-stock ' + (p.in_stock ? 'in-stock' : 'out-of-stock') + '">' + p.stock_text + '</div>';
                    if (p.short_desc) html += '<div class="rp-qv-desc">' + p.short_desc + '</div>';

                    if (p.attributes.length) {
                        html += '<div class="rp-qv-attrs">';
                        p.attributes.forEach(function(a) {
                            html += '<span class="rp-qv-attr"><strong>' + a.label + ':</strong> ' + a.value + '</span>';
                        });
                        html += '</div>';
                    }

                    if (p.sku) html += '<div class="rp-qv-sku">SKU: ' + p.sku + '</div>';
                    html += '<a href="' + p.url + '" class="rp-qv-view-full">Vedi Prodotto Completo</a>';
                    html += '</div></div>';

                    $content.html(html);
                }
            });
        });

        // Thumbnail click
        $(document).on('click', '.rp-qv-thumb', function() {
            var src = $(this).data('src');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $(this).closest('.rp-qv-gallery').find('.rp-qv-main-img').attr('src', src);
        });
    });
    </script>
    <?php
});