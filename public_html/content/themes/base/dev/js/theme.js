/**
 *	
 *	Set Row Margins
 * 	Sets the margin of the rows
 *
 */
function setRowMargins() {
	$('.page-row').each(function() {
		
		var baseTopmargin = $(this).data('desktop-margin-top');
		var tabletTopmargin = $(this).data('tablet-margin-top');
		var mobileTopmargin = $(this).data('mobile-margin-top');
		
		var baseBottommargin = $(this).data('desktop-margin-bottom');
		var tabletBottommargin = $(this).data('tablet-margin-bottom');
		var mobileBottommargin = $(this).data('mobile-margin-bottom');
		
		if( baseTopmargin !== '' || tabletTopmargin !== '' || mobileTopmargin !== '' || baseBottommargin !== '' || tabletBottommargin !== '' || mobileBottommargin !== '' ) {
		
			if($(window).width() < 979 && $(window).width() > 768 ) {
				// Tablet
				$(this).css('margin-top', (( tabletTopmargin !== '' ) ? tabletTopmargin : baseTopmargin ));
				$(this).css('margin-bottom', (( tabletBottommargin !== '' ) ? tabletBottommargin : baseTopmargin ));
			}
			
			else if($(window).width() < 768 ) {
				// Mobile
				$(this).css('margin-top', (( mobileTopmargin !== '' ) ? mobileTopmargin : baseTopmargin ));
				$(this).css('margin-bottom', (( mobileBottommargin !== '' ) ? mobileBottommargin : baseTopmargin ));
			}
			
			else {
				// Desktop
				$(this).css('margin-top', baseTopmargin);
				$(this).css('margin-bottom', baseBottommargin);
			}
			
		}
		
		var baseToppadding = $(this).data('desktop-padding-top');
		var tabletToppadding = $(this).data('tablet-padding-top');
		var mobileToppadding = $(this).data('mobile-padding-top');
		
		var baseBottompadding = $(this).data('desktop-padding-bottom');
		var tabletBottompadding = $(this).data('tablet-padding-bottom');
		var mobileBottompadding = $(this).data('mobile-padding-bottom');
		
		var target = (( $(this).data('target') === 'row' || $(this).data('target') === undefined ) ? $(this) : $(this).find('.content') );
		
		if( baseToppadding !== '' || tabletToppadding !== '' || mobileToppadding !== '' || baseBottompadding !== '' || tabletBottompadding !== '' || mobileBottompadding !== '' ) {
		
			if($(window).width() < 979 && $(window).width() > 768 ) {
				// Tablet
				target.css('padding-top', (( mobileToppadding !== '' ) ? mobileToppadding : baseToppadding ));
				target.css('padding-bottom', (( tabletBottompadding !== '' ) ? tabletBottompadding : baseBottompadding ));
			}
			
			else if($(window).width() < 768 ) {
				// Mobile
				target.css('padding-top', (( mobileToppadding !== '' ) ? mobileToppadding : baseBottompadding ));
				target.css('padding-bottom', (( mobileBottompadding !== '' ) ? mobileBottompadding : baseBottompadding ));
			}
			
			else {
				// Desktop
				target.css('padding-top', baseToppadding);
				target.css('padding-bottom', baseBottompadding);
			}
			
		}
		
	});
}


$(function() {
	
	
	if( $('.page-row').length ) {
		setRowMargins();
		$(window).on('resize', function() { setRowMargins(); });
	}
		
});