/*globals FastClick:false */
/*globals ajaxurl:false */
/*globals google:false */
/*globals odometer:false */
/*globals lazySizesConfig:false */
/*globals lazySizes:false */
/*globals jQuery:false */
/*globals $:false */


// Global variable to sture clubresults
var clubresults = '', resizeTimer, scaleResizeTimer, map, mapcenter, input, autocomplete, marker, selectedMarker, markers = [];
var screenWidth = $(window).width();


function scaleImages( target ) {
	clearTimeout(scaleResizeTimer);
	$(target).imageScale({
    	rescaleOnResize: true
	});
}


function initializeMap() {
	
	// If the map is available, call to the mapSetup function
	if( $('#map').length ) { 
		
		// Add autocomplete to input
		input = (document.getElementById('clubsearch')); 
		autocomplete = new google.maps.places.Autocomplete(input);
		
		// Setup the map
		mapSetup();
		
		// When place changed, find closest
		autocomplete.addListener('place_changed', function() {
		    
		    // Get the coordinates of city selected
		    var coordinatesLat = autocomplete.getPlace().geometry.location.lat();
		    var coordinatesLng = autocomplete.getPlace().geometry.location.lng();
		    
		    // Find the closest marker
		    findClosestMarker( map, coordinatesLat, coordinatesLng, markers );
		    
		    // Switch result
		    $('.clubfinder').addClass('is--result');
			$('.clubfinder__form').removeClass('is--active');
		    $('.clubfinder__result').addClass('is--active');
		    
		});
		
		
	} else {
		input = (document.getElementById('input_2_3')); 
		autocomplete = new google.maps.places.Autocomplete(input);
		
		// Empty the placeholder
		$('#input_2_3').prop('placeholder', '');
		
		// Disable the next button
		$('#gform_next_button_2_4').hide();
		
		
		
		// Get results when place is changed
	    autocomplete.addListener('place_changed', function() {
		    
		    // Get the coordinates of city selected
		    var coordinatesLat = autocomplete.getPlace().geometry.location.lat();
		    var coordinatesLng = autocomplete.getPlace().geometry.location.lng();
		    
		    // Remove ajax error
		    $('.ajax_error').slideUp();
		    
		    // Get closests results
		    $.ajax({
		    	url: ajaxurl,
		    	type: 'POST',
		    	data: { action: 'find_clubs', lat: coordinatesLat, lng: coordinatesLng },
		    	success: function(response) {
			    	
			    	var data = JSON.parse(response);
			    	
			    	clubresults = data;
			    	$('#gform_next_button_2_4').show();
			    	
		    	},
		    	error: function() {
			    	
			    	// Error handling when location can't be determined
			    	$('#gform_next_button_2_4').before('<span class="ajax_error">We konden het adres niet omzetten naar een locatie, kunt u het opnieuw proberen?</span>');
		    	}
	        });
	        
		});
	}
	
}

function mapSetup() {
	
	// Setup the map center
	mapcenter = new google.maps.LatLng(52.35, 5.2);
	
	// Initialize function for the Google Maps
	// object on the archive-sportcentra.php page
	var mapOptions = {
		zoom: (( screenWidth < 767 ) ? 7 : 8),
		center: mapcenter, // Centre of The Netherlands
		disableDefaultUI: true,
		scrollwheel: false,
		scaleControl: false,
		disableDoubleClickZoom: true,
		zoomControl: true
	}
	
	// Load the map
	map = new google.maps.Map(document.getElementById('map'),mapOptions);
	
	// Load the markers
	$.ajax({
    	url: ajaxurl,
    	type: 'POST',
    	data: { action: 'get_clubs', echo : 'true' },
    	success: function(response) {
	    	
	    	// Parse the JSON result
	    	var results = JSON.parse(response);
	    	
	    	// Drop the markers
	    	addMarkers(results, map, markers);
	    	
    	}
    });
 
 
    if (location.protocol === 'https:') {
		// Get location request
		setTimeout(function(){ getGeoLocation( map, markers ) }, 1000);
	} else {
		// Open the clubfinder
		$('.clubfinder').addClass('is--active');
	}
	
}

function mapSetLocation( map, coords, markers ) {
	
	// Setup lat long
	var latlng = { lat : coords[0], lng : coords[1]  };
	
	// And get general location based on coords
	var geocoder = new google.maps.Geocoder;
	
	// Geocode the location to get a formatted address
	geocoder.geocode({'location': latlng}, function(results, status) {
		if(status === 'OK') {
			if(results[0]) {
				
				// Find the closest marker and zoom in
				findClosestMarker( map, coords[0], coords[1], markers );
				
			} else {
				$('.clubfinder').addClass('is--active');
			}
		} else {		
			$('.clubfinder').addClass('is--active');
		}
	});
	
}

function findClosestMarker( map, lat, lng, markers ) {
    var R = 6371; // radius of earth in km
    var distances = [];
    var closest = -1;
    for( var i=0; i < markers.length; i++ ) {
        var mlat = markers[i].position.lat();
        var mlng = markers[i].position.lng();
        var dLat  = rad(mlat - lat);
        var dLong = rad(mlng - lng);
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(rad(lat)) * Math.cos(rad(lat)) * Math.sin(dLong/2) * Math.sin(dLong/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c;
        distances[i] = d;
        if ( closest == -1 || d < distances[closest] ) {
            closest = i;
        }
    }

	// Set the selected marker
	selectedMarker = markers[closest];
    
    // Calculate the location and offset it with pixels
    var markerLocation = selectedMarker.getPosition();
    var scale = Math.pow( 2, map.getZoom() );
    var coordsCenter = map.getProjection().fromLatLngToPoint( markerLocation );
    var pixelOffset = new google.maps.Point( 0, ( (( screenWidth < 767 ) ? 0 : 10) / scale) );
    
    var coordsNewCenter = new google.maps.Point(
	    coordsCenter.x - pixelOffset.x,
	    coordsCenter.y - pixelOffset.y
    );
    
    var newCenter = map.getProjection().fromPointToLatLng(coordsNewCenter);

	// Zoom in to the location
	map.setCenter( newCenter );
	map.setZoom( (( screenWidth < 767 ) ? 15 : 11) );

	// Bounce the marker
    selectedMarker.setAnimation(google.maps.Animation.BOUNCE);
    
    // Build selected icon
    var selectedIcon = {
        url: '/content/themes/exclusievesportcentra/lib/img/maps_marker-selected.svg',
        anchor: new google.maps.Point(20,40),
        scaledSize: new google.maps.Size(40,40)
    }
    
    // Set icon color
    selectedMarker.setIcon(selectedIcon);
    
    // Activate the box
    $('.clubfinder').find('.content .name').html( selectedMarker.metadata.name );
    $('.clubfinder').find('.content .address').html( selectedMarker.metadata.address );
    $('.clubfinder').find('.content').attr( 'href' , selectedMarker.metadata.url );
    
    // Show it!
    $('.clubfinder').addClass('is--active');
	$('.clubfinder__form').removeClass('is--active');
    $('.clubfinder__result').addClass('is--active');
    
}



function calculatePostSliderWidth() {
	
	// calculate the widths of static elements
	var windowWidth = $('body').width(),
		containerWidth = $('.container').width();
		
	// Calculate the needed width
	var sliderWidth = ( windowWidth - ( (windowWidth - containerWidth) / 2 ) );
	
	// Set the width
	$('.postslider').width( sliderWidth );
	
}

function startAnimatedCounter( elem ) {
	
	// Setup the needed ID and classes
	$(elem).attr('id', 'odometer').addClass('odometer');
	
	// Setup the threshold as amount of pixels
	// to be visible before firing
	var threshold = 200;
	
	// Check the static position of the element and current scrollpos
	var elementPosition = $(elem).offset().top,
		scrollpos = ( $(document).scrollTop() + $(window).height() ) - threshold;
	
	// Check if the element is visible
	if( scrollpos > elementPosition ) {
		odometer.innerHTML = $(elem).data('count');
	}
}

function stickyMenuButton( target ) {
	
	// Get the bottom pixel position of the target element
	var scrollPos = $(document).scrollTop(),
		targetPos = (target.height() + target.offset().top);
	
	// Check if target has passed
	if(scrollPos > targetPos) {
		$('.menu-button').addClass('menu-button--is-sticky');
	} else {
		$('.menu-button') .removeClass('menu-button--is-sticky');
	}
	
}

function homeScrollActions(navPos, introCopy) {
	
	// Check if the video needs to be paused
	var scrollPos = $(document).scrollTop(),
		contentPos = $('.home__content').offset().top;
	
	// Calculate the topmargin based on screen dimensions
	// and also calculate the amount of opacity needed to
	// fade out the intro copy
	var blockMovement = (introCopy + (scrollPos * 1.5) ),
		blockOpacity = ((100 - (scrollPos / 6) ) / 100);
	
	$('.home__video .overlay').css( 'bottom', blockMovement + 'px')
							  .css( 'opacity', blockOpacity );
	
	// Pause/play video and set the menu button to sticky
	if( scrollPos > contentPos ) {
		$('#homepage_video').get(0).pause();
		$('.menu-button').addClass('menu-button--is-sticky');
	} else {
		$('#homepage_video').get(0).play();
		$('.menu-button') .removeClass('menu-button--is-sticky');
	}
}


function scaleImageWrapper() {
	
	// Gather image and text elements
	var elem = $('.js-size-image-wrapper'),
		row = elem.parents('.container'),
		target = row.find('.text-wrapper');
	
	// Get the height of the target to mirror
	var targetHeight = target.height();
	
	// Set height to the element
	$(elem).height(targetHeight);
	
	// Scale the image to fit
	setTimeout(function(){
		$(elem).find('.js-image-scale').imageScale({
			scale: 'best-fill',
			fadeInDuration: 500,
			rescaleOnResize: true
		});
	}, 200);
}


function setFormLabel() {
	
	// Loop each form body
	var selector = $('.gform_body');
	
	// Setup the loop
	$(selector).each(function() {
		
		// Loop all gfields
		$('.gfield').each(function() {
		
			// Get the element value
			var elem = $(this).find('input[type=text], input[type=tel], input[type=email], input[type=phone], input[type=number], textarea');
			if( elem.length ) {
			
				var str = elem.val();
				
				// Test is string is empty
				if( str === null || str.length === 0 || (!/[^\s]/.test(str)) ) {
					elem.parents('.gfield').removeClass('is--filled').removeClass('is--focus');
				} else {
					elem.parents('.gfield').addClass('is--filled');
				}
			}
			
			
			// Get the select box
			elem = $(this).find('select, input[type=radio], input[type=checkbox]');
			if( elem.length ) {
				
				// By default, set filled status
				elem.parents('.gfield').addClass('is--filled');
				
			}
			
		});
		
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
	
	
	// Setup resizeTimer
	if(resizeTimer === 0) { /* Do nothing */ } // Remove this line in production
	
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
	
	
	// Home specific scripts
	if($('.home').length) {
		
		// Set navigation position as static
		var navPos = $('.menu-button').offset().top,
			introCopy = ( $(window).height() - ( $('.home__video .overlay').position().top + $('.home__video .overlay').outerHeight() ) );
		
		calculatePostSliderWidth();
		homeScrollActions(navPos, introCopy);
		startAnimatedCounter('.js-counter');
			
		$(document).on('scroll', function() {
			homeScrollActions(navPos, introCopy);
			startAnimatedCounter('.js-counter');
		});
		
		$(window).on('resize', function(){ 
			calculatePostSliderWidth();
			homeScrollActions(navPos, introCopy);
			startAnimatedCounter('.js-counter');
		});
	} else {
		$(document).on('scroll', function() {
			stickyMenuButton( $('.header') );
		});
	}
	
	// If the counter is in the page, count up
	if($('.js-counter').length) {
		$(document).on('scroll', function() {
			startAnimatedCounter('.js-counter');
		});
	}
	
	// If there is a content slider, enable the slideshow function
	if($('.postslider').length) {
		$('.postslider__slides').slick({
			variableWidth: true,
			dots: false,
			infinite: true,
			swipeToSlide: true,
			prevArrow: $('.postslider__control--prev'),
			nextArrow: $('.postslider__control--next'),
			responsive: [
				{
					breakpoint: 979,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2,
						centerMode: true,
						autoplay: true
					}
				}
			]
		});
	}
	
	// If there is a content slider, enable the slideshow function
	if($('.contentslider').length) {
		$('.contentslider__slides').slick({
			dots: false,
			infinite: true,
			arrows: true,
			swipeToSlide: true
		});
	}
	
	if($('.js-size-image-wrapper').length) {
		scaleImageWrapper();
		$(window).on('resize', function(){ 
			scaleImageWrapper();
		});
	}
	
	
	// Toggle club single form
	if($('.single-club').length) {
		$(document).on('click', '.js-open-form', function(e) {
			e.preventDefault();
			$(document).find('.form-wrapper').addClass('is--active');
		});
		
		$('.js-toggle-deviant-openinghours').on('click', function(e) {
			e.preventDefault();
			$('.opening-hours--deviant').toggleClass('is--active');
		});
	}
	
	
	
	// Set class to parent when focus on field
	$(document).on('focus', '.gfield input, .gfield textarea', function() {
		$(this).parents('.gfield').addClass('is--filled');
	});
	
	$(document).on('blur', '.gfield input, .gfield textarea', function() {
		setFormLabel();
	});
	
	
	
	// Modal actions
	$(document).on('click', '.js-open-modal', function(e){
		e.preventDefault();
		$('.modal, .modal .box[data-modal="' + $(this).data('modal') + '"]').fadeIn();
		
		// Store scrollposition and lock scroll on body
		$('body').addClass('no-scroll').data( 'scrollpos', $(window).scrollTop() );
	});
	
	$(document).on('click', '.js-open-clubfinder', function(e){
		e.preventDefault();
		$('.modal, .modal .modal__box[data-modal="clubfinder"]').fadeIn();
		
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
	
	
	
	// Specific actions
	$(document).on('gform_page_loaded', function(event, form_id, current_page){
        if(form_id === 2) {
	        
	        // Get the name and set it at the page
	        var fname = $('#input_2_2').val();
	        $('.js-replace[data-placeholder="fname"]').text(fname);
	        
	        // Initialize the map
	        if(current_page === '2') {
		        initializeMap();
	        }
	        
	        if(current_page === '3') {
		        $('.js-replace[data-placeholder="resultcount"]').text( ((clubresults.count > 0) ? clubresults.count : 'geen') + ' clubs gevonden' );
		        
		        // Empty the resultlist
		        $('.js-replace[data-placeholder="clubs"]').html('');
		        
		        // Loop the results
		        for( var i=0; i < clubresults.clubs.length; i++ ) {
			        
			        // Store club info
			        var club = clubresults.clubs[i];
			        
			        var clubContent = '<a href="' + club.url + '" class="club-result">' + 
										  '<span class="heading heading--small">' + club.name + '</span>' + 
										  '<span class="club-result__address">' + club.address + '</span>' + 
									  '</a>';
			        
			        $('.js-replace[data-placeholder="clubs"]').append( clubContent );
		        }
		        
	        }
	        
        }
        
    });
    
    
    
    // Reset clubfinder box
    $('.js-correct-clubfinder-result').on('click', function(e) {
	    e.preventDefault();
		$('.clubfinder__form').addClass('is--active');
	    $('.clubfinder__result').removeClass('is--active');
	    $('.clubfinder__form input').val('').focus();
	    
	    // Remove selected marker
	    selectedMarker.setAnimation(null);
	    selectedMarker.setIcon( { url: '/content/themes/exclusievesportcentra/lib/img/maps_marker.svg', 
		    					  anchor: new google.maps.Point(17,34), 
		    					  scaledSize: new google.maps.Size(34,34)
		    					});
		
		// Reset center and zoom level
		map.setCenter( mapcenter );
		map.setZoom(8);
		
    });
    
	
	if($('.js-price-slideshow').length) {
		// Set the slideshow
		$('.js-price-slideshow').slick({
			dots: false,
			arrows: false,
			slidesToShow: 3,
			slidesToScroll: 3,
			swipeToSlide: true,
			variableHeight: true,
			responsive: [
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						centerMode: true,
						autoplay: true
					}
				}
			]
		});
	}
	
	
	
	
	
})(jQuery);



function rad(x) {return x*Math.PI/180;}

// Function to add markers to the map
// call in script to add markers to the map.
function addMarkers(results, map, markers) {
	if(results && results.length > 0) {
		// Drop it like it's hot!
		// Drop markers on map
		$.each(results, function(i, obj) {
			
			// Set SVG default icon
			var icon = {
		        url: '/content/themes/exclusievesportcentra/lib/img/maps_marker.svg',
		        anchor: new google.maps.Point(17,34),
		        scaledSize: new google.maps.Size(34,34)
		    }
		    
		 	marker = new google.maps.Marker({
			 	title: obj.name,
		    	position: new google.maps.LatLng(obj.lat, obj.lng),
		        map: map,
		        icon: icon,
				animation: google.maps.Animation.DROP
		    });
			
			// Add result metadata
			marker.metadata = obj;
			
			// Push to array
			markers.push(marker);
		});
	}
}
	
	
//	Get GeoLocation in the HTML5 format
//	And set location if user clicks the geo-location button
function getGeoLocation( map, markers ) {
	
	// Get navigator geolocation availability
	if(navigator.geolocation) { 
		navigator.geolocation.getCurrentPosition(function(position) {
	
			// Setup the coords array
			var coordinates = [];
			
			// Load results in panel
			coordinates.push(position.coords.latitude);
			coordinates.push(position.coords.longitude);
	
			// Return the coordinates
			mapSetLocation( map, coordinates, markers );
			
		});
		
	}
}