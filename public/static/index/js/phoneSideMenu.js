    // $('.menuTrigger').click(function() {
    $('.burger-menu').click(function() {
        $('.panel').toggleClass('isOpen');
        $('.wrapper').toggleClass('pushed');
        $(".burger-menu").toggleClass("menu-on"); //add

        ////////////////////////////////////////////////
        if($('.subPanel').hasClass('isOpen')){
            $('.subPanel').removeClass('isOpen');
        }
        ////////////////////////////////////////////////
    });
    // $('.burger-menu.menu-on').click(function() {
    //     $('.subPanel').removeClass('isOpen');

    // });
    $('.openSubPanel').click(function() {
        $(this).next('.subPanel').addClass('isOpen');
    });

    $('.closeSubPanel').click(function() {
        $('.subPanel').removeClass('isOpen');
        $('.panel').removeClass('isOpen');
        $('.wrapper').removeClass('pushed');
        $(".burger-menu").removeClass("menu-on"); //add
    });

    $('.closePanel').click(function() {
        $('.panel').removeClass('isOpen');
        $('.wrapper').removeClass('pushed');
        $(".burger-menu").removeClass("menu-on"); //add

    });
    $(window).on("resize", function() {
        var width992 = Modernizr.mq('(min-width: 992px)');
        if (width992) {
            $('.panel').removeClass('isOpen');
            $('.wrapper').removeClass('pushed');
            $(".burger-menu").removeClass("menu-on"); //add
            $('.subPanel').removeClass('isOpen');

        }

    }).resize();