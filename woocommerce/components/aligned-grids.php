add_action('wp_footer', function () {
    if (!is_shop() && !is_product_taxonomy() && !is_search()) return;
    ?>
    <style>
        /* Each card becomes a flex column */
        .woocommerce ul.products li.product {
            display: flex !important;
            flex-direction: column !important;
        }

        /* Image: uniform ratio */
        .woocommerce ul.products li.product a img,
        .woocommerce ul.products li.product .attachment-woocommerce_thumbnail {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
        }

        /* Title: fixed 2-line height */
        .woocommerce ul.products li.product .woocommerce-loop-product__title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.8em;
            line-height: 1.4;
            font-size: 14px;
        }

        /* Price + buttons pushed to bottom */
        .woocommerce ul.products li.product .price {
            margin-top: auto;
            padding-top: 6px;
            padding-bottom: 8px;
        }

        .woocommerce ul.products li.product .button,
        .woocommerce ul.products li.product .rp-quick-add-btn {
            margin-top: auto;
        }
    </style>

    <script>
    jQuery(function($) {
        // Equalize card heights per row
        function equalizeCards() {
            var $cards = $('ul.products li.product');
            if (!$cards.length) return;

            // Reset heights
            $cards.css('min-height', '');

            // Group cards by row (same offsetTop)
            var rows = {};
            $cards.each(function() {
                var top = Math.round($(this).offset().top);
                if (!rows[top]) rows[top] = [];
                rows[top].push(this);
            });

            // Set each row to tallest card
            $.each(rows, function(top, cards) {
                var maxHeight = 0;
                $(cards).each(function() {
                    var h = $(this).outerHeight();
                    if (h > maxHeight) maxHeight = h;
                });
                $(cards).css('min-height', maxHeight + 'px');
            });
        }

        // Run on load, resize, and after AJAX (infinite scroll, filters)
        equalizeCards();
        $(window).on('resize', $.debounce ? $.debounce(200, equalizeCards) : function() {
            clearTimeout(window.rpEqTimer);
            window.rpEqTimer = setTimeout(equalizeCards, 200);
        });
        $(document).on('ajaxComplete', function() {
            setTimeout(equalizeCards, 300);
        });
        // Re-run after all images loaded
        $('ul.products img').on('load', equalizeCards);
    });
    </script>
    <?php
});