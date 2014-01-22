jQuery(document).ready(function($) {
	$('.roost-section-expansion').on('click', function() {
		$(this).toggleClass('collapsed');
		$(this).parent().find('.roost-section-content').slideToggle(200);
	});
	$('#rooster-status-close').click(function() {
		$('#rooster-status').css('display', 'none');
	})
});