/**
 * Custom scripts for the Completionist landing page.
 *
 * No need for DOMContentLoaded listener. This script is enqueued in the footer
 * and deferred.
 */

// Track package downloads.
document.querySelector('#primary header a.ptc-completionist-free-download')
	.addEventListener('click', function() {
		if ( 'function' === typeof window.ptcTrackEvent ) {
			window.ptcTrackEvent(
				'plugin-completionist',
				'completionist-free-download-click',
				'header',
			);
		}
	});
document.querySelector('#primary footer a.ptc-completionist-free-download')
	.addEventListener('click', function() {
		if ( 'function' === typeof window.ptcTrackEvent ) {
			window.ptcTrackEvent(
				'plugin-completionist',
				'completionist-free-download-click',
				'footer',
			);
		}
	});
