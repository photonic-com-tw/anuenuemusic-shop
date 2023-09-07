    // go top
    var Global = {
        init: function() {
            Global.scroll();
        },

        scroll: function() {
            // Show or hide the sticky footer button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('.goTop').fadeIn(200);
                } else {
                    $('.goTop').fadeOut(200);
                }
            });

            // Animate the scroll to top
            $('.goTop').click(function(event) {
                event.preventDefault();

                $('html, body').animate({ scrollTop: 0 }, 300);
            })
        }

    };

    document.addEventListener("DOMContentLoaded", function() {
        Global.init();
    });


    