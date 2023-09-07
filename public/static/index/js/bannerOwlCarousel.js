$(document).ready(function() {
    // bannerOwlCarouselRow  start /////////////////////////////////////////////////////////////////////////////////


    $('.bannerOwlCarouselRow .owl-carousel').owlCarousel({
        loop: true,
        margin: 0,
        nav: false,
        items: 1,
        autoplay: true,
        autoplayTimeout: 4500,
        autoplayHoverPause: true,
        smartSpeed: 650,

        // lazyLoad: true,
        // navText:["<div class='nav-btn prev-slide icon-left_arrow'></div>","<div class='nav-btn next-slide icon-right_arrow'></div>"],
    })

    // threeSquareAdRow  start /////////////////////////////////////////////////////////////////////////////////
    $('.threeSquareAdRow .owl-carousel').owlCarousel({
        loop: false,
        margin: 12,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4500,
        autoplayHoverPause: true,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 1,
                stagePadding: 60,
                center: true,
                loop: true,
                margin: 6,
            },
            414: {
                items: 1,
                stagePadding: 75,
                center: true,
                loop: true,
                margin: 6,
            },
            576: {
                items: 1,
                stagePadding: 120,
                center: true,
                loop: true,
                margin: 6,
            },
            767: {
                items: 3,
                stagePadding: 0,
                center: false,
                loop: false,
                margin: 12,
            },
        }

    })

    // productListRow  start /////////////////////////////////////////////////////////////////////////////////


    $('.productListRow .owl-carousel').owlCarousel({
        loop: false,
        margin: 12,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4500,
        autoplayHoverPause: true,
        smartSpeed: 1000,
        responsive: {
            0: {
                items: 1,
                stagePadding: 0,
                center: false,
                loop: false,
                margin: 6,
            },
            414: {
                items: 2,
                stagePadding: 0,
                center: false,
                loop: false,
                margin: 6,
            },
            991: {
                items: 3,
                stagePadding: 0,
                center: false,
                loop: false,
                margin: 12,
            },
            1440: {
                items: 4,
                stagePadding: 0,
                center: false,
                loop: false,
                margin: 12,
            },
        },
        navText: ["<div class='nav-btn prev-slide icon-left'></div>", "<div class='nav-btn next-slide icon-right'></div>"],
    })


});
