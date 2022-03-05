/**
 * Theme global frontend scripts.
 *
 * No need for DOMContentLoaded listener. This script is enqueued in the footer
 * and deferred.
 */

// Toggle mobile menu open/close.
document.querySelector('#site-navigation button.menu-toggle')
	.addEventListener('click', function() {
		document.documentElement.classList.toggle('mobile-menu-open');
	});

// Close mobile menu on overlay click.
document.getElementById('overlay')
	.addEventListener('click', function() {
		document.documentElement.classList.remove('mobile-menu-open');
	});
