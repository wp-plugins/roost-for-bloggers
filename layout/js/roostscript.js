jQuery(document).ready(function($) {

	$('.roost-section-expansion').on('click', function() {
		
		$(this).toggleClass('collapsed');
		
		$(this).parent().find('.roost-section-content').slideToggle(200);
		$(this).parent().find('.roost-section-heading').css({
			'border-radius': '5px'
		})
				
		if($(this).data('rounded')){
			$(this).data('rounded', false);
			$(this).parent().find('.roost-section-heading').css({
				'border-radius': '5px 5px 0 0'
			});
		} else {
			$(this).data('rounded', true);
		}
		
	});
	
	$('.roost-send-title').click(function() {
		$this = $(this);
		if($this.hasClass('roost-title-active')) {
			return false;
		} else {
		    var dataTarget = $this.attr('data-related');
			$('#roost-manual-send-wrapper').find('.roost-title-active').removeClass('roost-title-active');
			$('#roost-manual-send-wrapper').find('.roost-send-active').removeClass('roost-send-active');
			if(dataTarget == 1) {
				$('#roost-send-with-link').addClass('roost-send-active');
			} else {
				$('#roost-send-no-link').addClass('roost-send-active');				
			}
			$this.addClass('roost-title-active');
		}
	});
		
	$('.roost-hint').click(function(e) {
		$('#rooster').find('.hint-active').removeClass('hint-active');
		$('.roost-hint-bubble').remove();
	
		$this = $(this);
		$this.addClass('hint-active');
		
		var x = e.pageX-284;
		var y = e.pageY-125;
		
		var helperText = $(this).data('helper');
		$('#rooster').prepend("<div class='roost-hint-bubble'>"+ hintText[helperText] +"<span class='roost-hint-notch'></span></div>");
		$('.roost-hint-bubble').css({
			'left': x,
			'top': y
		});		
		
		e.stopPropagation();
	});
	
	$('html').click(function() {
		$('#rooster').find('.hint-active').removeClass('hint-active');
		$('.roost-hint-bubble').remove();
	});	
	
	$('#rooster-status-close').click(function() {
		$('#rooster-status').css('display', 'none');
	})
	
});