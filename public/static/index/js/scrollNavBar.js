// scrollNavBar //////////////////////////////////////////////////////////////////////////////////////////
    function scrollNavBar() {
        // console.log(12131313)
        var $tabNav = $('.tab-nav'),
            width = 0;

        $tabNav.find('li').each(function() {
            width += $(this).width();
        });

        $tabNav.width(width);
        // console.log(width)

        $tabNav.on('click', 'li', function() {
            var $el = $(this),
                halfItemWidth = parseFloat($el.width() + (2 * parseFloat($el.css('paddingLeft')))) / 2,
                halfVp = parseFloat($('.viewport').width() / 2),
                offset = $el.offset().left - $tabNav.offset().left,
                left = Math.floor(offset - halfVp + halfItemWidth);
            $el
                .siblings();
            $('.viewport').animate({ scrollLeft: left });
        });
    };
    scrollNavBar();
    $("#right").click(function() {
        var ulWidth = $('ul.menu-main').innerWidth();
        var scrollLeft = $('.viewport').scrollLeft();
        if (scrollLeft < ulWidth) {
            var viewportW = $('.viewport').innerWidth();
            left = scrollLeft + viewportW;
            $('.viewport').animate({ scrollLeft: left });
        }
    });
    $("#left").click(function() {
        var scrollLeft = $('.viewport').scrollLeft();
        if (scrollLeft > 0) {
            var viewportW = $('.viewport').innerWidth();
            left = scrollLeft - viewportW;
            $('.viewport').animate({ scrollLeft: left });
        }
    });


// productBranchRow //////////////////////////////////////////////////////////////////////////////////////////
// console.log($('.productBranchRow').width())
    $(".productBranchRow .right").click(function() {
        var slidingWidth=0;
        var proNav =$(this).closest('.productBranchRow');
        var scrollLeft = proNav.find('.productBranch-viewport').scrollLeft();
        var ulWidth =0;
        proNav.find('li').each(function() {
            ulWidth += $(this).innerWidth();
        });
        var n=0;
        if (scrollLeft < ulWidth) {
            var viewportW = proNav.find('.productBranch-viewport').innerWidth();
            left = scrollLeft + viewportW;
            proNav.find('li').each(function() {
                slidingWidth += $(this).innerWidth();
                if(slidingWidth>left && n==0 ){
                    left = slidingWidth -$(this).innerWidth();
                    proNav.find('.productBranch-viewport').animate({ scrollLeft: left });

                    n +=1;
                }
            });
        }else{
            proNav.find('.productBranch-viewport').animate({ scrollLeft: ulWidth });
        }
    });
    $(".productBranchRow .left").click(function() {
        var slidingWidth=0;
        var proNav =$(this).closest('.productBranchRow');
        var scrollLeft = proNav.find('.productBranch-viewport').scrollLeft();
        var n=0;
        if (scrollLeft > 0) {
            var viewportW = proNav.find('.productBranch-viewport').innerWidth();
            left = scrollLeft - viewportW;
            proNav.find('li').each(function() {
                slidingWidth += $(this).innerWidth();
                if(slidingWidth>left && n==0 ){
                    left = slidingWidth-$(this).innerWidth();
                    proNav.find('.productBranch-viewport').animate({ scrollLeft: left });

                    n +=1;
                }
            });
        }else{
            proNav.find('.productBranch-viewport').animate({ scrollLeft: 0 });
        }
    });