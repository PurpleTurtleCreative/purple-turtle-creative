/**
 * FormStepVerificationCode component
 *
 * Sends a verification code to a recipient's email and validates
 * the user's response.
 */

import FormInputCodePuncher from './FormInputCodePuncher.jsx';

import { useEffect, useRef, useState } from '@wordpress/element';

export default function FormStepVerificationCode({ email, list_id, onSuccess }) {
	const [ codeDigits, setCodeDigits ] = useState(Array.from({ length: 6 }, () => ''));
	const [ status, setStatus ] = useState('idle');
	const [ error, setError ] = useState('');
	const formRef = useRef();

	useEffect(async () => {
		// Attempt to send a verification code to the user.
		if ( 'loading' !== status ) {
			setStatus('loading');
			await window.fetch(
				`${window.ptcTheme.api.v1}/mailing-lists/subscribe`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.ptcTheme.api.auth_nonce,
					},
					body: JSON.stringify({
						email,
						list_id,
						verification_type: 'code',
						nonce: window.ptcTheme.api.nonce,
					}),
				})
				.then(async (res) => {
					const body = await res.json();
					if ( res.ok && 'success' === body?.status ) {
						onSuccess(body);
					} else if ( body?.message ) {
						setError(body.message);
					} else {
						setError('Failed to verify email. Please try again.');
					}
				})
				.catch(err => {
					window.console.error(err);
					setError(err.message);
				})
				.finally(() => {
					setStatus('idle');
				});
		}
	}, []);

	useEffect(() => {
		if ( 6 === codeDigits.join('').length ) {
			// Automatically submit when all digits are entered.
			formRef.current.requestSubmit();
		}
	}, [codeDigits]);

	const handleSubmit = async (event) => {
		event.preventDefault();
		// Check verification code length.
		const verificationCodeString = codeDigits.join('');
		if ( 6 !== verificationCodeString.length ) {
			setError('Please enter the 6-digit verification code.');
			return false;
		}
		// Check the verification code against the server.
		if ( 'loading' !== status ) {
			setStatus('loading');
			await window.fetch(
				`${window.ptcTheme.api.v1}/mailing-lists/verify`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.ptcTheme.api.auth_nonce,
					},
					body: JSON.stringify({
						email,
						list_id,
						verification_code: verificationCodeString,
						nonce: window.ptcTheme.api.nonce,
					}),
				})
				.then(async (res) => {
					const body = await res.json();
					if ( res.ok && 'success' === body?.status ) {
						onSuccess(body);
					} else if ( body?.message ) {
						setError(body.message);
					} else {
						setError('Failed to verify email. Please try again.');
					}
				})
				.catch(err => {
					window.console.error(err);
					setError(err.message);
				})
				.finally(() => {
					setStatus('idle');
				});
		}
	};

	let innerContent = null;
	if ( 'loading' === status ) {
		innerContent = <p>Loading...</p>;
	} else {
		innerContent = (
			<form ref={formRef} onSubmit={handleSubmit}>
				<p>Confirm your email address</p>
				<p>{`To continue, please enter the 6-digit verification code sent to your ${email} email.`}</p>
				<FormInputCodePuncher slots={codeDigits} onChange={setCodeDigits} />
				<button type="submit">Continue</button>
			</form>
		);
	}

	return (
		<div className="ptc-FormStepVerificationCode">
			{ error && <p>{error}</p> }
			{innerContent}
		</div>
	);
}
