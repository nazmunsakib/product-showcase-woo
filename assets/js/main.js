(function () {
    'use strict';

    /**
     * Initialize HexaGrid Swiper sliders
     * @param {string} selector - Swiper container selector
     */
    const initHexaGridSlider = (selector) => {

        if (typeof Swiper === 'undefined') return;

        const sliders = document.querySelectorAll(selector);
        if (!sliders.length) return;

        sliders.forEach((slider) => {

            if (slider.classList.contains('swiper-initialized')) return;

            const container = slider.closest('.hexagrid-slider-container') || slider;

            const columns  = parseInt(container.dataset.columns, 10) || 4;
            const nav      = container.dataset.nav === 'yes';
            const dots     = container.dataset.dots === 'yes';
            const autoplay = container.dataset.autoplay === 'yes';

           

            const paginationEl = slider.querySelector('.swiper-pagination');
            const nextEl = container.querySelector('.swiper-button-next');
            const prevEl = container.querySelector('.swiper-button-prev');

             console.log(nextEl);

            const swiperConfig = {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                watchOverflow: true,

                breakpoints: {
                    640:  { slidesPerView: Math.min(2, columns) },
                    768:  { slidesPerView: Math.min(3, columns) },
                    1024: { slidesPerView: columns },
                },

                ...(dots && paginationEl ? {
                    pagination: {
                        el: paginationEl,
                        clickable: true,
                    }
                } : {}),

                ...(nav && nextEl && prevEl ? {
                    navigation: {
                        nextEl,
                        prevEl,
                    }
                } : {}),

                ...(autoplay ? {
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    }
                } : {})
            };

            new Swiper(slider, swiperConfig);
        });
    };

    // Initial page load
    document.addEventListener('DOMContentLoaded', () => {
        initHexaGridSlider('.hexagrid-product-slider-1');
    });

    // WooCommerce AJAX / fragments refresh
    document.body.addEventListener('wc_fragments_refreshed', () => {
        initHexaGridSlider('.hexagrid-product-slider-1');
    });

    // Expose globally (for custom AJAX / reuse)
    window.initHexaGridSlider = initHexaGridSlider;

    jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
        let $container = $button.closest('.hexagrid-product-cart-btn');
        if ($container.length) {
            // Change icon
            $button.find('.dashicons').removeClass('dashicons-cart').addClass('dashicons-arrow-right-alt');

            // Get View Cart URL
            let $viewCartLink = $container.find('.added_to_cart.wc-forward');
            let cartUrl = $viewCartLink.attr('href');

            if (cartUrl) {
                $button.attr('href', cartUrl);
                $button.removeClass('ajax_add_to_cart');
            }
        }
    });


})();