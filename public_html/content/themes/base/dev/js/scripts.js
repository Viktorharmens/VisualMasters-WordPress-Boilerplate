/*globals FastClick:false */
/*globals ajaxurl:false */
/*globals lazySizesConfig:false */
/*globals lazySizes:false */
/*globals jQuery:false */
/*globals $:false */



function scaleImages( target ) {
	clearTimeout(scaleResizeTimer);
	$(target).imageScale({
    	rescaleOnResize: true
	});
}



(function($) {
	
	// Add fastclick polyfill
	var needsClick = FastClick.prototype.needsClick;
	FastClick.prototype.needsClick = function(target) { 
		if ( (target.className || '').indexOf('pac-item') > -1 ) {
			return true;
		} else if ( (target.parentNode.className || '').indexOf('pac-item') > -1) {
			return true;
		} else {
			return needsClick.apply(this, arguments);
		}
	};
	FastClick.attach(document.body);
	
	// Lazyload overrides
	window.lazySizesConfig = window.lazySizesConfig || {};
	lazySizesConfig.srcAttr = 'data-original';
	lazySizesConfig.init = false;
	
	// Delayed init
	setTimeout(function(){ lazySizes.init(); }, 50);
	
	// Scale the images
	$(document).ready(function () {
		scaleResizeTimer = setTimeout( scaleImages('.img-scale'), 300);
		$(window).on('resize', function(){ 
			scaleResizeTimer = setTimeout( scaleImages('.img-scale'), 300);
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
	});
	
		
	// Modal actions
	$(document).on('click', '.js-open-modal', function(e){
		e.preventDefault();
		$('.modal, .modal .box[data-modal="' + $(this).data('modal') + '"]').fadeIn();
		
		// Store scrollposition and lock scroll on body
		$('body').addClass('no-scroll').data( 'scrollpos', $(window).scrollTop() );
	});
	
	$('.js-close-modal').on('click', function(e){
		e.preventDefault();
		$('.modal, .modal .box').fadeOut();
		
		// Revert back to old scrollposition
		$('body').removeClass('no-scroll');
		$(window).scrollTop( $('body').data('scrollpos') );
		$('body').data( 'scrollpos', '' );
	});
		
	
})(jQuery);