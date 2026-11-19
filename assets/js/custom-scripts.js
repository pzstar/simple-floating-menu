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
        // Make sure this.hash has a value before overriding default behavior
        // A bare "#" is what the scroll actions carry, and $("#") is not a
        // selector jQuery accepts.
        var samePage = this.host === window.location.host && this.pathname.replace(/\/$/, '') === window.location.pathname.replace(/\/$/, '');
        if (samePage && hash !== "" && hash !== "#" && $(hash).length > 0) {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Smooth scroll in 800ms, leaving the room the menu asks for above the target.
            var offset = parseInt($(this).closest('.sfm-floating-menu').attr('data-scroll-offset'), 10) || 0;

            $('html, body').animate({
                scrollTop: $(hash).offset().top - offset
            }, 800).promise().done(function () {

                // Add hash (#) to URL when done scrolling, once, without the browser's own jump that would undo the offset.
                if (window.location.hash === hash) {
                    return;
                }
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, '', hash);
                } else {
                    window.location.hash = hash;
                }
            });
        } // End if
    });
});