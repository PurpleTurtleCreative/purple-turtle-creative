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

///////////////////////////////
// -- Block Custom Styles -- //
///////////////////////////////

// Create popover components for gallery images.
document.querySelectorAll('.wp-block-gallery.is-style-popover-alt-text')
	.forEach(function(galleryEl) {
		galleryEl.querySelectorAll('figure.wp-block-image img[alt]:not([alt=""])')
			.forEach(function(imageEl) {
				const popoverEl = document.createElement('p');
				popoverEl.innerText = imageEl.alt;
				popoverEl.classList.add('popover-alt-text');
				imageEl.parentElement.insertBefore(popoverEl, imageEl);
			});
	});

/////////////////////////////
// -- Utility Functions -- //
/////////////////////////////

function ptcTrackEvent( eventCategory, eventAction, eventLabel ) {

	if ( 'function' === typeof window.gtag ) {
		// Prioritize Google Analytics 4 gtag.js.
		window.gtag('event', eventAction, {
			"event_category": eventCategory,
			"event_label": eventLabel
		});
	} else if ( 'function' === typeof window.ga ) {
		// Fall back to Universal Analytics analytics.js.
		window.ga('send', {
			"hitType": 'event',
			"eventCategory": eventCategory,
			"eventAction": eventAction,
			"eventLabel": eventLabel
		});
	}

	console.log(
		'Event:',
		{
			"eventCategory": eventCategory,
			"eventAction": eventAction,
			"eventLabel": eventLabel
		}
	);
}
