/**
 * FormInputCaptcha component
 *
 * Form input to render a captcha.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#explicitly-render-the-turnstile-widget
 */

export default function FormInputCaptcha({ action, onTurnstileReponse }) {

	// @todo - Use turnstile.render() to manage state.

	let innerContent = null;
	if ( ! window?.turnstile ) {
		window.console.error( 'Failed to render FormInputCaptcha without Cloudflare Turnstile script enqueued.' );
	} else if ( ! window?.ptcTheme?.cf_turnstile?.site_key ) {
		window.console.error( 'Failed to render FormInputCaptcha without configured Cloudflare Turnstile site key.' );
	} else if ( ! action ) {
		window.console.error( 'Failed to render FormInputCaptcha without specified action.' );
	} else {
		innerContent = (
			<>
				<input type="hidden" name="cf-turnstile-action" value={action} />
				<div class="cf-turnstile" data-language="en-us" data-theme="light" data-size="normal" data-appearance="always" data-sitekey={window.ptcTheme.cf_turnstile.site_key} data-action={action} data-callback={onTurnstileReponse}></div>
			</>
		);
	}

	return (
		<div className="ptc-FormInputCaptcha">
			{innerContent}
		</div>
	);
}
