jQuery(document).ready(function($) {
    $('.roost-section-expansion').on('click', function() {
		$(this).toggleClass('collapsed');
		$(this).parent().find('.roost-section-content').slideToggle(200);
	});
	$('#rooster-status-close').click(function() {
		$('#rooster-status').css('display', 'none');
	})

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
});
