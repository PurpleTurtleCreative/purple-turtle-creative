jQuery(function($) {

	// Mobile menu toggle
	$('#site-navigation button.menu-toggle').on('click touch', function() {
		$('html').toggleClass('mobile-menu-open');
	});

	$('body').on('click touch', '#overlay', function(e) {
		$('html').removeClass('mobile-menu-open');
	});

});