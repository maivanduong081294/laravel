function handleScreen() {
    let screen = $(this).width();
    if(screen >= 1170) {
        if(!$("aside#sidebar").hasClass('show-sidebar')) {
            $("aside#sidebar").addClass('show-sidebar');
        }
    }
    else {
        if($("aside#sidebar").hasClass('show-sidebar')) {
            $("aside#sidebar").removeClass('show-sidebar');
        }
    }
}
jQuery(document).ready(function($) {
    handleScreen();
    $(document).on('click','.popper-button', function(e) {
        e.preventDefault();
        let popper = $(this).data('popper');
        popper = $('.popper[data-popper="'+popper+'"]');
        if(popper.length && popper.hasClass('show-popper')) {
            $('.popper').removeClass('show-popper');
        }
        else {
            $('.popper').removeClass('show-popper');
            popper.addClass('show-popper');
        }
    });
    $(document).on("click", function(event){
        var a = $(event.target).parents(".popper-button");
        if (!$(event.target).is(".popper,.popper-button") && !$(event.target).parents(".popper-button").length && !$(event.target).parents(".popper").length) {
            $('.popper').removeClass('show-popper');
        }
    });
    $(document).on("click", "#menu-toggle", function() {
        $("aside#sidebar").toggleClass('show-sidebar');
    })
    $(window).resize(function(){
        handleScreen();
    });
});