$(document).ready(function() {
    if ("ontouchstart" in document.documentElement) {
        $("html").addClass("touch");
    }
});

$('#item-navbar').prev().css('margin-bottom', '70px');
