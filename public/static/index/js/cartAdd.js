    var i = -1;  //preset
    function add() {
        i++;
        $('.counter.prodNum').html(i);
    }
    add();
    $( ".addProdNum" ).click(function() {
        $('.counter.prodNum').addClass('pulse');
        add();
    });
    $('.counter').on('animationend',function(){
        $('.counter.prodNum').removeClass('pulse');
    });