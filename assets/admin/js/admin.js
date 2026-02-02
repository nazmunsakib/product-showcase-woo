jQuery(document).ready(function ($) {
    // Tabs
    $('.hexagrid-tab-link').on('click', function (e) {
        e.preventDefault();
        var tabId = $(this).data('tab');

        // Remove active class
        $('.hexagrid-tab-link').removeClass('active');
        $('.hexagrid-tab-content').removeClass('active');

        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });

    // Section Toggle (Accordion)
    $('.hexagrid-section-header').on('click', function () {
        var $section = $(this).closest('.hexagrid-section');
        $section.toggleClass('closed');
        $(this).next('.hexagrid-section-body').slideToggle(200);
    });

    // Color Picker
    $('.hexagrid-color-picker').wpColorPicker();

    // Copy Shortcode functionality
    $('.hexagrid-copy-btn').on('click', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var targetId = $btn.data('clipboard-target');
        var $target = $(targetId);
        var text = $target.text();

        // Create temporary textarea
        var $temp = $("<textarea>");
        $("body").append($temp);
        $temp.val(text).select();

        try {
            document.execCommand("copy");
            var originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
            setTimeout(function () {
                $btn.html(originalText);
            }, 2000);
        } catch (err) {
            console.error('Failed to copy', err);
        }

        $temp.remove();
    });

    // Range Slider Output
    $('#hexagrid_columns').on('input', function () {
        $('#hexagrid_columns_output').text($(this).val());
    });

    // Content Type Card Selection Animation
    $('.hexagrid-content-type-option input[type="radio"]').on('change', function () {
        var $card = $(this).siblings('.hexagrid-content-type-card');

        // Add a pulse animation to the selected card
        $card.addClass('hexagrid-pulse-animation');
        setTimeout(function () {
            $card.removeClass('hexagrid-pulse-animation');
        }, 600);
    });


    // Layout Type Card Selection Animation & Logic
    $('.hexagrid-layout-option input[type="radio"]').on('change', function () {
        var $card = $(this).siblings('.hexagrid-layout-card');
        var selectedLayout = $(this).val();

        // Animation
        $card.addClass('hexagrid-pulse-animation');
        setTimeout(function () {
            $card.removeClass('hexagrid-pulse-animation');
        }, 600);

        // Logic: Show corresponding variation group
        $('.hexagrid-layout-variation-group').hide();
        $('.hexagrid-no-variations').hide();

        var $targetGroup = $('.hexagrid-layout-variation-group[data-parent-layout="' + selectedLayout + '"]');

        if ($targetGroup.length) {
            $targetGroup.fadeIn(200);

            // If no variation in this group is checked, check the first one
            if (!$targetGroup.find('input[type="radio"]:checked').length) {
                $targetGroup.find('input[type="radio"]').first().prop('checked', true).trigger('change');
            }
        } else {
            $('.hexagrid-no-variations').show();
        }

        // Conditional Logic: Columns (Grid & Slider only)
        if (selectedLayout === 'grid' || selectedLayout === 'slider') {
            $('#hexagrid-columns-wrapper').slideDown(200);
        } else {
            $('#hexagrid-columns-wrapper').slideUp(200);
        }

        // Conditional Logic: Slider Settings (Slider only)
        if (selectedLayout === 'slider') {
            $('#hexagrid-slider-settings-wrapper').slideDown(200);
        } else {
            $('#hexagrid-slider-settings-wrapper').slideUp(200);
        }
    });

    // Initialize state on page load for Layout Variations & Fields
    var currentLayout = $('.hexagrid-layout-option input[type="radio"]:checked').val();
    if (currentLayout) {
        $('.hexagrid-layout-variation-group').hide(); // Hide all first
        $('.hexagrid-layout-variation-group[data-parent-layout="' + currentLayout + '"]').show();

        // Initial Visibility: Columns
        if (currentLayout === 'grid' || currentLayout === 'slider') {
            $('#hexagrid-columns-wrapper').show();
        } else {
            $('#hexagrid-columns-wrapper').hide();
        }

        // Initial Visibility: Slider Settings
        if (currentLayout === 'slider') {
            $('#hexagrid-slider-settings-wrapper').show();
        } else {
            $('#hexagrid-slider-settings-wrapper').hide();
        }
    } else {
        // Fallback default if nothing selected (unlikely in WP admin but good for safety)
        $('.hexagrid-layout-option input[type="radio"][value="grid"]').prop('checked', true).trigger('change');
    }

    // Layout Variation Card Selection Animation
    $('.hexagrid-variation-option input[type="radio"]').on('change', function () {
        var $card = $(this).siblings('.hexagrid-variation-card');

        // Add a pulse animation to the selected card
        $card.addClass('hexagrid-pulse-animation');
        setTimeout(function () {
            $card.removeClass('hexagrid-pulse-animation');
        }, 600);
    });

    // Keyboard accessibility for layout cards
    $('.hexagrid-content-type-card, .hexagrid-layout-card, .hexagrid-variation-card').on('keypress', function (e) {
        if (e.which === 13 || e.which === 32) { // Enter or Space
            e.preventDefault();
            $(this).siblings('input[type="radio"]').prop('checked', true).trigger('change');
        }
    });

    // Make cards focusable for keyboard navigation
    $('.hexagrid-content-type-card, .hexagrid-layout-card, .hexagrid-variation-card').attr('tabindex', '0');
});

