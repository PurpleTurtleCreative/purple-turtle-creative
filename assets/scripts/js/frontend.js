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

//////////////////////////////
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
