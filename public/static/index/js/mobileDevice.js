    function mobileDevice() {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            // alert("使用行動裝置!");
        } else {
            // alert("使用桌上型裝置!");
            $('.scroll').css('overflow', 'hidden')

        }
    }
    $(window).on("resize", function() {
        var width576 = Modernizr.mq('(max-width: 576px)');
        var width991 = Modernizr.mq('(max-width: 991px)');
        var width1199 = Modernizr.mq('(max-width: 1199px)');
        var width1439 = Modernizr.mq('(max-width: 1439px)');
        var width1920 = Modernizr.mq('(max-width: 1920px)');
        var totalLiNum = $(".viewport li").length; // li總數量
        var viewportW = $('.viewport').innerWidth();
        if (width576) {
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 4));
            });
            if (totalLiNum > 4) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        } else if (width991) {
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 5));
            });
            if (totalLiNum > 5) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        } else if (width1199) {
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 7));
            });
            if (totalLiNum > 7) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        } else if (width1439) {
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 8));
            });
            if (totalLiNum > 8) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        } else if (width1920) {
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 8));
            });
            if (totalLiNum > 8) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        }else{
            $(".viewport li").each(function() {
                $(this).css("width", (viewportW / 8));
            });
            if (totalLiNum > 8) {
                $('.navBtnBox').css("display", "block");
            } else {
                $('.navBtnBox').css("display", "none");
            }
        }
        mobileDevice();
    }).resize();