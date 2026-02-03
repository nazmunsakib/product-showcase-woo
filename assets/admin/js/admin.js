jQuery(document).ready(function ($) {
    // Tabs (if used)
    $('.hexagrid-settings-tab-link').on('click', function (e) {
        e.preventDefault();
        var tabId = $(this).data('tab');

        // Remove active class
        $('.hexagrid-settings-tab-link').removeClass('active');
        $('.hexagrid-settings-tab-content').removeClass('active');

        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });

    // --- Section Toggle (Accordion) - Copied and renamed from library ---
    $('.hexagrid-settings-section-header').on('click', function () {
        var $section = $(this).closest('.hexagrid-settings-section');
        $section.toggleClass('closed');
        $(this).next('.hexagrid-settings-section-body').slideToggle(200);
    });

    // Copy Shortcode functionality
    $('.hexagrid-settings-copy-btn').on('click', function (e) {
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

    // Range Slider Output (if used)
    $('#hexagrid_columns').on('input', function () {
        $('#hexagrid_columns_output').text($(this).val());
    });

});
