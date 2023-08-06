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

////////////////////////////
// -- Accessible Media -- //
////////////////////////////

// Play/Pause moving pictures (GIFs).
document.querySelectorAll('.wp-block-image img[src$=".gif"]')
	.forEach(function(img) {
		if ( img.complete ) {
			// If image is already loaded.
			addAnimatedMediaControls(img);
		} else {
			// Once image is lazy loaded.
			img.addEventListener('load', function(event) {
				addAnimatedMediaControls(img);
			}, { once: true });
		}
	});

function addAnimatedMediaControls(img) {
	// Create static image.
	const canvas = document.createElement('canvas');
	canvas.width = img.naturalWidth;
	canvas.height = img.naturalHeight;
	canvas.getContext('2d').drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight);
	canvas.classList.add('animation-static-frame');
	// Add to DOM in wrapper.
	const mediaWrap = document.createElement('div');
	mediaWrap.classList.add('controlled-animated-media');
	img.parentElement.replaceChild(mediaWrap, img);
	mediaWrap.appendChild(canvas);
	mediaWrap.appendChild(img);
	img.classList.add('animation');
	// Allow user control.
	canvas.addEventListener('click', function(event) {
		// Clicked static image, so now show animation.
		canvas.style.display = 'none';
		img.style.removeProperty('display');
		mediaWrap.classList.remove('has-paused-animation');
		// Start GIF at beginning.
		const src = img.src;
		img.src = '';
		img.src = src;
	});
	img.addEventListener('click', function(event) {
		// Clicked animated image, so now show static.
		img.style.display = 'none';
		canvas.style.removeProperty('display');
		mediaWrap.classList.add('has-paused-animation');
	});
	// Play by default.
	canvas.click();
}

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

	window.console.log(
		'Event:',
		{
			"eventCategory": eventCategory,
			"eventAction": eventAction,
			"eventLabel": eventLabel
		}
	);
}
