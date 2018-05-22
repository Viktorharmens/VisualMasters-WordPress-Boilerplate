(function($) {
	
	// Set state on input when containing text
	$('.login-username > input, .login-password > input').on('blur', function(e) {
		 var str = $(this).val();
		 if( str == null || str.trim() === '') {
			$(this).removeClass('is--filled');
		 } else {
			$(this).addClass('is--filled');
		 }
	});
	
	
	// Slide the slides
	$('.js-slideshow').slick({
		dots: true,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 6000,
		speed: 500
	});
	
	
	
	// On Resize
	$(window).on('resize', function() {
		$('.js-slide-image > img').imageScale();
	});
	
})(jQuery);
