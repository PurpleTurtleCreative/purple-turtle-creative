/**
 * Core Details (accordion) block custom frontend scripts.
 */

// Find all core/details block instances.
document.querySelectorAll('details.wp-block-details')
	.forEach(el => {
		// Track toggle interaction.
		el.addEventListener(
			'toggle',
			event => {

				let eventLabel = 'unknown';

				// Check if the <summary> element exists within <details>.
				const summaryEl = el.querySelector('summary');
				if ( summaryEl ) {
				  // If <summary> exists, get its text content.
				  eventLabel = summaryEl.textContent;
				}

				// Log GA4 event.
				window.ptcTrackEvent(
					'accordion',
					'click',
					eventLabel,
				);
			},
			{ "once" : true }
		);
	});
