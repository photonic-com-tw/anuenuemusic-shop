    $(window).on("resize", function() {
        var width1600 = Modernizr.mq('(max-width: 1600px)');
        if (width1600) {
            $(".adSideBox img").addClass('closed');
            $(".closeAdSide").html('開啟廣告');
        }
        // else{
        //     $(".adSideBox img").removeClass('closed');
        //     $(".closeAdSide").html('關閉廣告');
        // }
    }).resize();

    $(".closeAdSide").on("click", function() {
        if($(".adSideBox img").hasClass('closed')){
            $(".adSideBox img").removeClass('closed');
            $(".closeAdSide").html('關閉廣告');
        }else{
            $(".adSideBox img").addClass('closed');
            $(".closeAdSide").html('開啟廣告');
        }

        $.ajax({
                url: "/index/ajax/closeAdSide.html",
                type: 'get',
                datatype: 'json',
                error: function (xhr) {
                },
                success: function (response) {
                }

            });
    });