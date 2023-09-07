$(document).ready(function() {
    min = 1; // Minimum of 1
    max = 99; // Maximum of 99
    $(".minus").on("click", function() {
        if ($('.count').val() > min) {
            $('.count').val(parseInt($('.count').val()) - 1);
            // $('.counter').text(parseInt($('.counter').text()) - 1);
        }
    });
    $(".plus").on("click", function() {
        if ($('.count').val() < max) {
            $('.count').val(parseInt($('.count').val()) + 1);
            // $('.counter').text(parseInt($('.counter').text()) + 1);
        }
    });
});