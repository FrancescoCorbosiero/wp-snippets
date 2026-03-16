
/**
 * Category Banner Cover Hero — paste into WPCode as PHP snippet
 * Runs only on WooCommerce product category pages.
 */
add_action('wp_footer', function () {
    if (!is_product_category()) return;
    ?>
    <style>
        .shoptimizer-category-banner { display: none !important; }

        .cbr-hero {
            position: relative;
            width: 100%;
            min-height: 420px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
            background: var(--gh-gray-900);
            font-family: var(--gh-font-sans);
            -webkit-font-smoothing: antialiased;
        }
        .cbr-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(0,0,0,0.72) 0%,
                rgba(0,0,0,0.35) 55%,
                rgba(0,0,0,0.10) 100%
            );
            z-index: 1;
        }
        .cbr-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: var(--gh-space-3xl) var(--gh-space-xl) var(--gh-space-2xl);
        }
        .cbr-tag {
            display: inline-block;
            background: var(--gh-accent);
            color: var(--gh-white);
            font-family: var(--gh-font-sans);
            font-size: var(--gh-text-xs);
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 2px;
            margin-bottom: var(--gh-space-lg);
        }
        .cbr-title {
            font-family: var(--gh-font-display) !important;
            font-size: var(--gh-text-4xl);
            font-weight: 800 !important;
            color: var(--gh-white);
            line-height: 1.08;
            margin: 0 0 var(--gh-space-md);
            max-width: 680px;
            border-left: 3px solid var(--gh-accent);
            padding-left: var(--gh-space-lg);
        }
        .cbr-desc {
            font-family: var(--gh-font-sans);
            font-size: var(--gh-text-sm);
            color: var(--gh-gray-300);
            line-height: 1.65;
            max-width: 560px;
            margin: 0;
            padding-left: calc(var(--gh-space-lg) + 3px);
        }

        @keyframes cbrFadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .cbr-tag   { animation: cbrFadeUp .5s var(--gh-ease-out, ease) both .05s; }
        .cbr-title { animation: cbrFadeUp .5s var(--gh-ease-out, ease) both .15s; }
        .cbr-desc  { animation: cbrFadeUp .5s var(--gh-ease-out, ease) both .25s; }
    </style>

    <script>
    (function () {
        // ── CONFIG ─────────────────────────────────────────────
        var BG_IMAGE  = 'https://resellpiacenza.shop/wp-content/uploads/2026/03/Gemini_Generated_Image_1th6uz1th6uz1th6.png';
        var TAG_LABEL = 'Collezione'; // pill above the title
        // ───────────────────────────────────────────────────────

        var original = document.querySelector('.shoptimizer-category-banner');
        if (!original) return;

        var titleEl = original.querySelector('h1, h2, h3');
        var descEl  = original.querySelector('.taxonomy-description');
        var title   = titleEl ? titleEl.textContent.trim() : document.title;
        var desc    = descEl  ? descEl.textContent.trim()  : '';

        var hero = document.createElement('div');
        hero.className = 'cbr-hero gh-block';
        hero.style.backgroundImage    = "url('" + BG_IMAGE + "')";
        hero.style.backgroundSize     = 'cover';
        hero.style.backgroundPosition = 'center top';

        hero.innerHTML =
            '<div class="cbr-inner">' +
                '<span class="cbr-tag">' + TAG_LABEL + '</span>' +
                '<h1 class="cbr-title">' + title + '</h1>' +
                (desc ? '<p class="cbr-desc">' + desc + '</p>' : '') +
            '</div>';

        original.parentNode.insertBefore(hero, original);
    })();
    </script>
    <?php
});