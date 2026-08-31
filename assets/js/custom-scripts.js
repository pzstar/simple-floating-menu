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
        if (hash !== "" && hash !== "#" && $(hash).length > 0) {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Using jQuery's animate() method to add smooth page scroll
            // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
            // The menu may ask for room above whatever it scrolls to.
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