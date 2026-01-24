document.addEventListener('DOMContentLoaded', function () {
    var sliders = document.querySelectorAll('.psw-layout-slider');

    sliders.forEach(function (slider) {
        new Swiper(slider, {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            pagination: {
                el: slider.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: slider.querySelector('.swiper-button-next'),
                prevEl: slider.querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
            },
        });
    });
});

jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
    var $container = $button.closest('.psw-product-cart-btn');
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
