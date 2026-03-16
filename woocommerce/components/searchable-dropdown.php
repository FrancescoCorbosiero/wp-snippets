add_action('wp_footer', function () {
    if (!is_shop() && !is_product_taxonomy()) return;
    ?>
    <style>
        /* Collapse toggle */
        .woocommerce-widget-layered-nav .gamma.widget-title,
        .woocommerce-widget-layered-nav .widget-title {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
        }
        .woocommerce-widget-layered-nav .widget-title::after {
            content: '▾';
            font-size: 14px;
            transition: transform 0.3s ease;
        }
        .woocommerce-widget-layered-nav .widget-title.collapsed::after {
            transform: rotate(-90deg);
        }

        /* Filter section wrapper */
        .rp-filter-section {
            overflow: hidden;
            transition: max-height 0.35s ease, opacity 0.25s ease;
            max-height: 800px;
            opacity: 1;
        }
        .rp-filter-section.collapsed {
            max-height: 0;
            opacity: 0;
        }

        /* Dropdown + clear row */
        .rp-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 0 16px;
        }
        .rp-filter-row .rp-select-wrap {
            flex: 1;
            min-width: 0;
        }

        /* Select2 accent overrides */
        .rp-filter-section .select2-container--default .select2-selection--single {
            border-radius: 20px;
            border-color: #ccc;
            height: 38px;
            padding: 4px 12px;
            transition: border-color 0.2s;
        }
        .rp-filter-section .select2-container--default .select2-selection--single:hover {
            border-color: #721124;
        }
        .rp-filter-section .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            font-size: 13px;
            color: #333;
        }
        .rp-filter-section .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .rp-filter-section .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #721124;
        }

        /* Select2 dropdown results accent */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #721124 !important;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: rgba(114, 17, 36, 0.1) !important;
            color: #721124 !important;
        }

        /* Clear button */
        .rp-filter-clear {
            padding: 6px 14px;
            border: 1px dashed #999;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            background: transparent;
            color: #999;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 38px;
            box-sizing: border-box;
        }
        .rp-filter-clear:hover {
            border-color: #721124;
            color: #721124;
        }
        .rp-filter-clear .rp-clear-x {
            font-size: 15px;
            line-height: 1;
            font-weight: bold;
        }

        /* Hide original form */
        .woocommerce-widget-layered-nav-dropdown {
            display: none !important;
        }

        /* Attribute label after title */
        .rp-filter-attr-label {
            font-weight: normal;
            opacity: 0.65;
            margin-left: 6px;
            font-size: 0.85em;
            text-transform: capitalize;
        }
    </style>
    <script>
    jQuery(function($) {
        var params = new URLSearchParams(window.location.search);

        // Friendly names for known attributes
        var attrLabels = {
            'marca':              'Marca',
            'taglia':             'Taglia',
            'data-di-rilascio':   'Anno',
            'colore':             'Colore',
            'modello':            'Modello',
            'genere':             'Genere',
            'materiale':          'Materiale'
        };

        // Process every layered nav widget
        $('.widget_layered_nav').each(function() {
            var $widget = $(this);
            var $title = $widget.find('.widget-title');
            var $form = $widget.find('form.woocommerce-widget-layered-nav-dropdown');
            var $select = $form.find('select');

            if (!$select.length) return;

            // Detect filter param from select class
            var filterParam = '';
            var attrSlug = '';
            var classList = $select.attr('class') || '';
            var match = classList.match(/dropdown_layered_nav_([^\s]+)/);
            if (match) {
                attrSlug = match[1];
                filterParam = 'filter_' + attrSlug;
            }

            if (!filterParam) return;

            // Append attribute label to the title
            var friendlyName = attrLabels[attrSlug] || attrSlug.replace(/-/g, ' ');
            $title.contents().first().after('<span class="rp-filter-attr-label">– ' + friendlyName + '</span>');

            var activeValue = params.get(filterParam) || '';

            // Build new section with cloned select
            var $section = $('<div class="rp-filter-section"></div>');
            var $row = $('<div class="rp-filter-row"></div>');
            var $wrap = $('<div class="rp-select-wrap"></div>');

            // Clone select for Select2
            var $newSelect = $select.clone().removeAttr('class').addClass('rp-select2-filter');
            $newSelect.attr('data-filter', filterParam);

            // Set active value
            if (activeValue) {
                $newSelect.val(activeValue);
            }

            $wrap.append($newSelect);
            $row.append($wrap);

            // Add clear button if active
            if (activeValue) {
                $row.append(
                    '<a href="#" class="rp-filter-clear" data-filter="' + filterParam + '">' +
                    '<span class="rp-clear-x">✕</span> Cancella</a>'
                );
            }

            $section.append($row);
            $form.after($section);

            // Init Select2
            $newSelect.select2({
                placeholder: 'Cerca ' + friendlyName.toLowerCase() + '...',
                allowClear: false,
                width: '100%',
                dropdownAutoWidth: false
            });

            // On change → navigate
            $newSelect.on('change', function() {
                var val = $(this).val();
                if (val) {
                    params.set(filterParam, val);
                } else {
                    params.delete(filterParam);
                }
                window.location.search = params.toString();
            });

            // Collapse toggle on title
            $title.on('click', function() {
                $(this).toggleClass('collapsed');
                $section.toggleClass('collapsed');
            });

            // Start collapsed if no active filter
            if (!activeValue) {
                $title.addClass('collapsed');
                $section.addClass('collapsed');
            }
        });

        // Clear button handler
        $(document).on('click', '.rp-filter-clear', function(e) {
            e.preventDefault();
            var filterParam = $(this).data('filter');
            params.delete(filterParam);
            window.location.search = params.toString();
        });
    });
    </script>
    <?php
});