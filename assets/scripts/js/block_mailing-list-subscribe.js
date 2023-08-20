/**
 * PTC Block - Mailing List Subscribe scripts.
 */

// Handle mailing list subscribe form submission.
document.querySelectorAll('.ptc-mailing-list-subscribe form')
	.forEach(form => {

		// Get form identifier.
		let formLabel = 'unknown';
		if ( form.dataset.label ) {
			formLabel = form.dataset.label;
		}

		// Track form interaction.
		form.addEventListener(
			'input',
			event => {
				// Log GA4 event.
				window.ptcTrackEvent(
					'mailing_lists',
					'subscribe_start',
					formLabel,
				);
			},
			{ "once" : true }
		);

		// Handle form submission via AJAX.
		form.addEventListener('submit', async (event) => {
			// Prevent form submit redirect.
			event.preventDefault();

			// Log GA4 event.
			window.ptcTrackEvent(
				'mailing_lists',
				'subscribe_submit',
				formLabel,
			);

			// Remember parent element.
			const container = form.closest('.ptc-mailing-list-subscribe');

			// Collect form data.
			const formData = new window.FormData(form);

			// Disable Cloudflare Turnstile widget listeners.
			try {
				window.turnstile.remove(
					container.querySelector('.cf-turnstile iframe').getAttribute('id')
				);
			} catch ( err ) {
				window.console.error(err);
			}

			// Replace form with loader.
			container.innerHTML = '<p class="ptc-form-loader">Processing...</p>';

			// Format the request.
			const request = {
				"method": form.method,
				"body": formData,
			}

			// Send the request.
			await window
				.fetch(form.action, request)
				.then(res => res.json())
				.then(data => {
					if (
						'message' in data &&
						data.message &&
						'status' in data &&
						data.status
					) {
						if ( data.status >= 400 ) {
							// Determine severity.
							let statusLevel = 'warning';
							if ( data.status >= 500 ) {
								statusLevel = 'danger';
							}
							// Show error message.
							container.innerHTML = `
								<div class="ptc-form-response">
									<p>${data.message}</p>
									<p>For assistance, please email <a href="mailto:michelle@purpleturtlecreative.com">michelle@purpleturtlecreative.com</a>.</p>
								</div>
							`;
							container.classList.add(`banner-${statusLevel}`);
							// Log GA4 event.
							window.ptcTrackEvent(
								'mailing_lists',
								'subscribe_error',
								formLabel,
							);
						} else {
							// Show success message.
							container.innerHTML = `
								<div class="ptc-form-response">
									<p>${data.message}</p>
								</div>
							`;
							container.classList.add('banner-success');
							// Log GA4 conversion event.
							window.ptcTrackEvent(
								'mailing_lists',
								'subscribe_success',
								formLabel,
							);
						}
					} else {
						throw 'Bad response';
					}
				})
				.catch(err => {
					// Show error message.
					container.innerHTML = `
						<div class="ptc-form-response">
							<p>Your request failed to submit, so it could not be processed. Please check your Internet connection, <a href="javascript:document.location.reload()">refresh the page</a>, and try again.</p>
							<p>For assistance, please email <a href="mailto:michelle@purpleturtlecreative.com">michelle@purpleturtlecreative.com</a>.</p>
						</div>
					`;
					container.classList.add('banner-danger');
					// Log GA4 event.
					window.ptcTrackEvent(
						'mailing_lists',
						'subscribe_exception',
						formLabel,
					);
					// Log error.
					window.console.error(err);
				});
		});
	});
