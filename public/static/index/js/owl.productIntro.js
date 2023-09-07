    function resizedw() {
        var woonine = Modernizr.mq('(max-width: 991px)');
        var wnnone = Modernizr.mq('(max-width: 767px)');
        if (woonine) {
            var contentBoxW = $("#itemBox").width();
            contentBoxW = contentBoxW - 220; //left width
            $("#rightContentBox").innerWidth(contentBoxW);
            if (wnnone) {
                $("#rightContentBox").innerWidth("100%");
            }
        } else {
            var contentBoxW = $("#itemBox").width();
            contentBoxW = contentBoxW - 300; //left width
            $("#rightContentBox").innerWidth(contentBoxW);
        }
        $('#carousel01 .item').height($('#carousel01 .item').width());
        $('#carousel02 .item').height($('#carousel02 .item').width());
    }

    var doit; //**執行一次

    $(window).on('load scroll resize', function() {
        // clearTimeout(doit); //**
        // doit = setTimeout(resizedw, 100); //**
        resizedw();
    });
    resizedw();

    /////////////////////////////////////////////////////////////////
    function owlProCarouselTop() {
        var cal01Div = $("#carousel01");
        var cal02Div = $("#carousel02");
        var slidesPerPage = 7;
        var syncedSecondary = true;

        cal01Div
            .owlCarousel({
                items: 1,
                slideSpeed: 2000,
                nav: false,
                autoplay: false,
                dots: true,
                loop: true,
                responsiveRefreshRate: 200,
            }).on('changed.owl.carousel', syncPosition);

        cal02Div
            .on('initialized.owl.carousel', function() {
                cal02Div.find(".owl-item").eq(0).addClass("current");
            })
            .owlCarousel({
                items: slidesPerPage,
                dots: false,
                nav: false,
                margin: 8,
                smartSpeed: 200,
                slideSpeed: 500,
                slideBy: slidesPerPage,
                responsiveRefreshRate: 100,
                responsive: {
                    0: {
                        margin: 8,
                        items: 5
                    }
                }
            }).on('changed.owl.carousel', syncPosition2);

        function syncPosition(el) {
            var count = el.item.count - 1;
            var current = Math.round(el.item.index - (el.item.count / 2) - .5);
            if (current < 0) {
                current = count;
            }
            if (current > count) {
                current = 0;
            }
            cal02Div
                .find(".owl-item")
                .removeClass("current")
                .eq(current)
                .addClass("current");
            var onscreen = cal02Div.find('.owl-item.active').length - 1;
            var start = cal02Div.find('.owl-item.active').first().index();
            var end = cal02Div.find('.owl-item.active').last().index();
            if (current > end) {
                cal02Div.data('owl.carousel').to(current, 100, true);
            }
            if (current < start) {
                cal02Div.data('owl.carousel').to(current - onscreen, 100, true);
            }
        }

        function syncPosition2(el) {
            if (syncedSecondary) {
                var number = el.item.index;
                cal01Div.data('owl.carousel').to(number, 100, true);
            }
        }

        cal02Div.on("click", ".owl-item", function(e) {
            e.preventDefault();
            var number = $(this).index();
            cal01Div.data('owl.carousel').to(number, 300, true);
        });
    };

    function owlSuggestPro() {
        $('.suggest-owl-carousel').owlCarousel({
            loop: true,
            margin: 0,
            dots: false,
            nav: false,
            margin: 8,
            responsive: {
                0: {
                    items: 2
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 4
                }
            }
        });
    };

    $(document).ready(function() {
        owlProCarouselTop();
        owlSuggestPro();
    });