document.addEventListener('DOMContentLoaded', function () {
    var sliders = document.querySelectorAll('.hexagrid-layout-slider');

    sliders.forEach(function (slider) {
        var container = slider.closest('.hexagrid-slider-container');
        var columns = 4;
        var nav = true;
        var dots = false;
        var autoplay = false;

        if (container) {
            columns = parseInt(container.getAttribute('data-columns')) || 4;
            nav = container.getAttribute('data-nav') === 'yes';
            dots = container.getAttribute('data-dots') === 'yes';
            autoplay = container.getAttribute('data-autoplay') === 'yes';
        }

        var swiperConfig = {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            pagination: dots ? {
                el: slider.querySelector('.swiper-pagination'),
                clickable: true,
            } : false,
            navigation: nav ? {
                nextEl: slider.querySelector('.swiper-button-next'),
                prevEl: slider.querySelector('.swiper-button-prev'),
            } : false,
            breakpoints: {
                640: {
                    slidesPerView: Math.min(2, columns),
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: Math.min(3, columns),
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: columns,
                    spaceBetween: 20,
                },
            },
        };

        if (autoplay) {
            swiperConfig.autoplay = {
                delay: 3000,
                disableOnInteraction: false,
            };
        }

        new Swiper(slider, swiperConfig);
    });
});

jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
    var $container = $button.closest('.hexagrid-product-cart-btn');
    if ($container.length) {
        // Change icon
        $button.find('.dashicons').removeClass('dashicons-cart').addClass('dashicons-arrow-right-alt');

        // Get View Cart URL
        var $viewCartLink = $container.find('.added_to_cart.wc-forward');
        var cartUrl = $viewCartLink.attr('href');

        if (cartUrl) {
            $button.attr('href', cartUrl);
            $button.removeClass('ajax_add_to_cart');
        }
    }
});
