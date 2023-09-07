    jQuery(function($) {
        setInterval(function() {
            var marquee = $("#announcementCarousel");
            marquee.stop().animate({ scrollLeft: marquee.children(":first").width() }, {
                duration: "slow",
                complete: function() {
                    marquee.scrollLeft(0).children(":first").appendTo(marquee);
                }
            });
        }, 4500);
    });

    //.stop()