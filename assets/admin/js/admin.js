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

    // Card Selection Animation (Generic)
    $('.hexagrid-content-type-option input[type="radio"], .hexagrid-layout-option input[type="radio"], .hexagrid-variation-option input[type="radio"]').on('change', function () {
        var $card = $(this).siblings('.hexagrid-content-type-card, .hexagrid-layout-card, .hexagrid-variation-card');

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

    /*
     * -------------------------------------------------------------------------
     * Layout Variation Logic (Specific)
     * -------------------------------------------------------------------------
     */
    function updateLayoutVariations() {
        var selectedLayout = $('.hexagrid-layout-option input[type="radio"]:checked').val();

        // Hide all first
        $('.hexagrid-layout-variation-group').hide();
        $('.hexagrid-no-variations').hide();

        if (!selectedLayout) return;

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
    }

    $('.hexagrid-layout-option input[type="radio"]').on('change', updateLayoutVariations);

    // Initial Run for Variations
    updateLayoutVariations();

    /*
     * -------------------------------------------------------------------------
     * Generic Dependency Logic (Scalable)
     * -------------------------------------------------------------------------
     */
    function checkDependencies() {
        $('[data-dependency]').each(function () {
            var $wrapper = $(this);
            var rule = $wrapper.data('dependency');

            // Rule format: { id: 'field_id', value: 'value' || ['v1', 'v2'] }
            if (!rule || !rule.id) return;

            var $trigger = $('[name="' + rule.id + '"], #' + rule.id);
            var currentValue;

            // Determine current value based on input type
            if ($trigger.is(':radio')) {
                currentValue = $('[name="' + rule.id + '"]:checked').val();
            } else if ($trigger.is(':checkbox')) {
                currentValue = $trigger.is(':checked') ? $trigger.val() : 'no'; // Default to 'no' if unchecked/hidden value logic aligns
            } else {
                currentValue = $trigger.val();
            }

            // Check match (support single value or array of values)
            var isMatch = false;
            if (Array.isArray(rule.value)) {
                isMatch = rule.value.includes(currentValue);
            } else {
                isMatch = (currentValue == rule.value); // Loose equality for numbers/strings
            }

            if (isMatch) {
                if ($wrapper.is(':hidden')) $wrapper.slideDown(200);
            } else {
                if ($wrapper.is(':visible')) $wrapper.slideUp(200);
            }
        });
    }

    // Bind change events to all potential trigger inputs
    // We find all unique IDs used in dependencies and bind listeners
    var triggerIds = new Set();
    $('[data-dependency]').each(function () {
        var rule = $(this).data('dependency');
        if (rule && rule.id) triggerIds.add(rule.id);
    });

    triggerIds.forEach(function (id) {
        $(document).on('change input', '[name="' + id + '"], #' + id, function () {
            checkDependencies();
        });
    });

    // Initial Run
    checkDependencies();

});
