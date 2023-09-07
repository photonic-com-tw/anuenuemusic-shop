
    // $(document).ready(function() {

    // });



    $(window).on("resize", function() {
        var width992 = Modernizr.mq('(min-width: 992px)');
        if (width992) {

	        $(".search-wrapper input").mouseenter(function() {
	            $(".search-wrapper button").css("color", "#ff6d00");


	        });
	        $(".search-wrapper input").mouseout(function() {
	            $(".search-wrapper button").css("color", "");
	        });

        }else{
	        $(".search-wrapper input").mouseenter(function() {
	            $(".search-wrapper button").css("color", "#fff");


	        });
	        $(".search-wrapper input").mouseout(function() {
	            $(".search-wrapper button").css("color", "");
	        });	
        }

    }).resize();