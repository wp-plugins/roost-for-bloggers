(function($) {
    var $roostAdmin = $('.roost-admin-section');    
    $roostAdmin.hide();
    $('#roost-activity').show();
        
    $('#roost-tabs li').on('click', function(){
        $(this).parent().find('.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index();
        if(index === 0) {
            $roostAdmin.hide();
            $('#roost-activity').show();
        } else if (index === 1) {
            $roostAdmin.hide();
            $('#roost-manual-push').show();
        } else {
            $roostAdmin.hide();            
            $('#roost-settings').show();
        }
    });

    var roostInput = $('#roostManualNote');
    var roostCount = $('#roostManualNoteCountInt');
    var roostLimit = 70;

    roostInput.keyup(function() {
        var n = this.value.replace(/{.*?}/g, '').length;
        if ( n > ( roostLimit - 11 ) ){
            if(!roostCount.hasClass('roostWarning')){
                roostCount.addClass('roostWarning');   
            }
        } else if ( n < roostLimit - 10 ) {
            if(roostCount.hasClass('roostWarning')){
                roostCount.removeClass('roostWarning');   
            }
        }
        roostCount.text( 0 + n );
    }).triggerHandler('keyup');
})(jQuery);
