/* Settings Builder Library Script */

jQuery(document).ready(function ($) {

    // --- Section Toggle (Accordion) ---
    $('.aksbuilder-section-header').on('click', function () {
        var $section = $(this).closest('.aksbuilder-section');
        $section.toggleClass('closed');
        $(this).next('.aksbuilder-section-body').slideToggle(200);
    });

    // --- Color Picker ---
    // Initializes on any element with the class even if loaded dynamically
    if ($.fn.wpColorPicker) {
        $('.aksbuilder-color-picker').wpColorPicker();
    }

    // --- Card Selection Animation (Generic) ---
    $('.aksbuilder-card-option input[type="radio"], .aksbuilder-card-option input[type="checkbox"]').on('change', function () {
        var $card = $(this).siblings('.aksbuilder-card');

        // Add a pulse animation to the selected card
        $card.addClass('aksbuilder-pulse-animation');
        setTimeout(function () {
            $card.removeClass('aksbuilder-pulse-animation');
        }, 600);
    });

    // --- Keyboard Accessibility for Cards ---
    // Allow pressing Enter/Space on a card to select the radio/checkbox inside
    $('.aksbuilder-card').attr('tabindex', '0');

    $('.aksbuilder-card').on('keypress', function (e) {
        if (e.which === 13 || e.which === 32) { // Enter or Space
            e.preventDefault();
            var $input = $(this).siblings('input');
            if ($input.is(':radio')) {
                $input.prop('checked', true).trigger('change');
            } else if ($input.is(':checkbox')) {
                $input.prop('checked', !$input.prop('checked')).trigger('change');
            }
        }
    });


    /*
     * -------------------------------------------------------------------------
     * Generic Dependency Logic (Scalable)
     * -------------------------------------------------------------------------
     * Looks for [data-dependency] attributes on wrappers.
     * Attribute format: data-dependency='{"id":"field_id","value":"expected_val"}'
     */
    function checkDependencies() {
        $('[data-dependency]').each(function () {
            var $wrapper = $(this);
            var rule = $wrapper.data('dependency');

            // Rule format: { id: 'field_id', value: 'value' || ['v1', 'v2'], operator: '==' }
            if (!rule || !rule.id) return;

            var $trigger = $('[name="' + rule.id + '"], #' + rule.id);
            var currentValue;

            // Determine current value based on input type
            if ($trigger.is(':radio')) {
                currentValue = $('[name="' + rule.id + '"]:checked').val();
            } else if ($trigger.is(':checkbox')) {
                currentValue = $trigger.is(':checked') ? $trigger.val() : 'no'; // Default 'no' for standard WP checkboxes if unchecked
            } else {
                currentValue = $trigger.val();
            }

            // Check match (support single value or array of values)
            var isMatch = false;

            // Allow checking for "not empty" if value is specific wildcard like '*'
            if (rule.value === '*') {
                isMatch = (currentValue !== '' && currentValue !== null);
            } else if (Array.isArray(rule.value)) {
                isMatch = rule.value.includes(currentValue);
            } else {
                // Loose equality (==) allows '1' to match 1
                isMatch = (currentValue == rule.value);
            }

            if (isMatch) {
                if ($wrapper.is(':hidden')) $wrapper.slideDown(200);
            } else {
                if ($wrapper.is(':visible')) $wrapper.slideUp(200);
            }
        });
    }

    // Bind change events to all potential trigger inputs found in dependencies
    var triggerIds = new Set();
    $('[data-dependency]').each(function () {
        var rule = $(this).data('dependency');
        if (rule && rule.id) triggerIds.add(rule.id);
    });

    triggerIds.forEach(function (id) {
        // Bind to change and input (for sliders/text)
        $(document).on('change input', '[name="' + id + '"], #' + id, function () {
            checkDependencies();
        });
    });

    // Initial Run
    checkDependencies();

    /*
     * -------------------------------------------------------------------------
     * Grouped Variation Logic
     * -------------------------------------------------------------------------
     * Handles showing/hiding variation groups based on parent field value.
     * Uses data-parent-field and data-parent-value attributes.
     */
    function checkGroupedVariations() {
        $('.aksbuilder-grouped-variation').each(function () {
            var $group = $(this);
            var parentFieldId = $group.data('parent-field');
            var parentValue = $group.data('parent-value');

            if (!parentFieldId) return;

            var $parentField = $('[name="' + parentFieldId + '"]');
            var currentValue;

            // Get current value of parent field
            if ($parentField.is(':radio')) {
                currentValue = $('[name="' + parentFieldId + '"]:checked').val();
            } else {
                currentValue = $parentField.val();
            }

            // Show/hide based on match
            if (currentValue == parentValue) {
                $group.fadeIn(300);
            } else {
                $group.hide();
            }
        });

        // Show "no match" message if no groups are visible
        $('.aksbuilder-form-group').each(function () {
            var $container = $(this);
            var $groups = $container.find('.aksbuilder-grouped-variation');
            var $noMatch = $container.find('.aksbuilder-no-match');

            if ($groups.length > 0) {
                var hasVisible = $groups.filter(':visible').length > 0;
                if (hasVisible) {
                    $noMatch.hide();
                } else {
                    $noMatch.fadeIn(300);
                }
            }
        });
    }

    // Bind to parent field changes
    $('.aksbuilder-grouped-variation').each(function () {
        var parentFieldId = $(this).data('parent-field');
        if (parentFieldId) {
            $(document).on('change', '[name="' + parentFieldId + '"]', function () {
                checkGroupedVariations();
            });
        }
    });

    // Initial run
    checkGroupedVariations();

});
