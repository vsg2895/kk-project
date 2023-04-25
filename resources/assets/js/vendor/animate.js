// Animate function
// $('#yourElement').animateCss('bounce');
$.fn.extend({
    animateCss: function (animationName, end) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        $(this).addClass('animated ' + animationName).one(animationEnd, function() {
            if (end == 'hide') {
                $(this).hide();
            } else if (end == 'remove') {
                $(this).remove();
            } else {
                $(this).removeClass('animated ' + animationName);
            }
        });
    }
});