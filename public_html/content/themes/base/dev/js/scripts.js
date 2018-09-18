var scaleResizeTimer;


function scaleImages( target ) {
	clearTimeout(scaleResizeTimer);
	$(target).imageScale({
    	rescaleOnResize: true
	});
}


function openModal( modal ) {
	var topScrollPos = $(window).scrollTop();
	$('.modal, .modal .modal__box[data-modal="' + modal + '"]').addClass('is--active');
	$('body').addClass('no-scroll').attr( 'data-scrollpos', topScrollPos );
}


(function($) {
	
	// Lazyload overrides
	window.lazySizesConfig = window.lazySizesConfig || {};
	lazySizesConfig.srcAttr = 'data-original';
	lazySizesConfig.init = false;
	
	// Delayed init
	setTimeout(function(){ lazySizes.init(); }, 50);
	
	// Scale the images
	$(document).ready(function () {
		scaleResizeTimer = setTimeout( scaleImages('.js-image-scale'), 300);
		$(window).on('resize', function(){ 
			scaleResizeTimer = setTimeout( scaleImages('.js-image-scale'), 300);
		});
	});
	
	
	// Scale images when lazyloaded
	document.addEventListener('lazyloaded', function() {
		resizeTimer = setTimeout(function() { 
	    	$('.js-image-scale').imageScale({
		    	rescaleOnResize: true
	    	});
	    }, 30);
	});
	
	
	// Trigger a resize action
	setTimeout(function(){ $(window).trigger('resize'); }, 200);
	
	
	// Truncate strings
	setTimeout(function(){ 
		$('.js-truncate').dotdotdot({
			wrap: 'word',
			watch: window,
			after: '.js-readmore-handle'
		});
	}, 200);
	
	
	// Set action to menu button
	$('.js-toggle-nav').on('click', function(e) {
		e.preventDefault();
		$(this).toggleClass('is--active');
		$('.navigation').toggleClass('is--active');
		
		
		if( $('.navigation').hasClass('is--active') ) {
			var topScrollPos = $(window).scrollTop();
			$('body').addClass('no-scroll').attr( 'data-scrollpos', topScrollPos );
		} else {
			$('body').removeClass('no-scroll');
			$(window).scrollTop( $('body').attr('data-scrollpos') );
			$('body').attr( 'data-scrollpos', '' );
		}
			
	});
	
	$('.js-toggle-subnav').on('click', function(e) {
		e.preventDefault();
		$(this).parents('li').toggleClass('is--active');
	});
	
		
	// Modal actions
	$(document).on('click', '.js-open-modal', function(e){
		e.preventDefault();
		openModal( $(this).data('modal') );
	});
	
	
	$('.js-close-modal').on('click', function(e){
		e.preventDefault();
		$('.modal, .modal .modal__box').removeClass('is--active');
		
		// Revert back to old scrollposition
		$('body').removeClass('no-scroll');
		$(window).scrollTop( $('body').attr('data-scrollpos') );
		$('body').attr( 'data-scrollpos', '' );
	});
		
	
})(jQuery);