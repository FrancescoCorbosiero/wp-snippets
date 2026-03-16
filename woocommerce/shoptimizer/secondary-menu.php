add_filter('wp_nav_menu', function($nav_menu, $args) {
if ($args->menu->slug !== 'secondario') return $nav_menu;

return '
<div class="menu-secondario-container">
    <ul id="menu-secondario" class="menu" style="color: white;">

        <li class="menu-item">
            <a href="https://resellpiacenza.shop/my-account/" style="color: white;">
                Account
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>
        </li>

        <li class="menu-item">
            <a href="https://resellpiacenza.shop/my-account/orders/" style="color: white;">
                Ordini
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>
        </li>

        <li class="menu-item">
            <a href="https://resellpiacenza.shop/checkout/" style="color: white;">
                Checkout
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>
        </li>

    </ul>
</div>';
}, 10, 2);