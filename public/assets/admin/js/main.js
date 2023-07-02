function handleScreen() {
    let screen = $(this).width();
    if(screen >= 1170) {
        if($("aside#sidebar").hasClass('show-sidebar')) {
            $("aside#sidebar").removeClass('show-sidebar');
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
    $(window).resize(function(){
        handleScreen();
    });
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
    $(document).on("click",".menu-item.has-children > a", function(e) {
        e.preventDefault();
        let $this = $(this);
        if($this.hasClass('show')) {
            $this.removeClass('show');
            $this.siblings(".menu-children").slideUp();
        }
        else {
            $(".menu-item.has-children > a").removeClass("show");
            $(".menu-item.has-children .menu-children").slideUp();
            $this.addClass('show');
            $this.siblings(".menu-children").slideDown();
        }
    });
});