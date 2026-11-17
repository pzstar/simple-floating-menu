jQuery(document).ready(function ($) {
    function glideTo(top) {
        $('html, body').animate({ scrollTop: top }, 800);
    }

    // Back to top
    $(".sfm-floating-menu a.sfm-scroll-to-top").on('click', function (event) {
        event.preventDefault();
        glideTo(0);
    });

    // Down to the end of the page
    $(".sfm-floating-menu a.sfm-scroll-to-bottom").on('click', function (event) {
        event.preventDefault();
        glideTo($(document).height() - $(window).height());
    });

    // Add smooth scrolling to all links
    $(".sfm-floating-menu a").on('click', function (event) {
        // Store hash
        var hash = this.hash;
        // Only a real hash: scroll actions carry a bare "#", which jQuery can't select.
        if (hash !== "" && hash !== "#" && $(hash).length > 0) {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Smooth scroll in 800ms, leaving the room the menu asks for above the target.
            var offset = parseInt($(this).closest('.sfm-floating-menu').attr('data-scroll-offset'), 10) || 0;

            $('html, body').animate({
                scrollTop: $(hash).offset().top - offset
            }, 800, function () {

                // Add hash (#) to URL when done scrolling (default click behavior)
                window.location.hash = hash;
            });
        } // End if
    });
});