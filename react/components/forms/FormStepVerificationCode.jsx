/**
 * FormStepVerificationCode component
 *
 * Sends a verification code to a recipient's email and validates
 * the user's response.
 */

import FormInputCodePuncher from './FormInputCodePuncher.jsx';

import { useEffect, useRef, useState } from '@wordpress/element';

export default function FormStepVerificationCode({ email, codeLength, onSuccess }) {
	const [ codeDigits, setCodeDigits ] = useState(Array.from({ length: codeLength }, () => ''));
	const [ status, setStatus ] = useState('idle');
	const [ error, setError ] = useState('');
	const formRef = useRef();

	useEffect(() => {
		if ( codeLength === codeDigits.join('').length ) {
			// Automatically submit when all digits are entered.
			formRef.current.submit();
		}
	}, [codeDigits]);

	const handleSubmit = async (event) => {
		event.preventDefault();
		// Check verification code length.
		const verificationCodeString = codeDigits.join('');
		if ( verificationCodeString.length !== codeLength ) {
			setError(`Please enter the ${codeLength}-digit verification code.`);
		}
		// Check the verification code against the server.
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
						email: email,
						verification_code: codeDigits.join(''),
						list_id: 'a2tBf2KrKnxBF66s', // Hard-coded. Sorry, Mom.
						nonce: window.ptcTheme.api.nonce,
					}),
				})
				.then(async (res) => {
					body = await res.json();
					if ( res.ok && 'success' === body?.status ) {
						onSuccess(body);
					} else if ( body?.message ) {
						setError(err.message);
					} else {
						setError('Failed to verify email. Please try again.');
					}
				})
				.catch(err => {
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
				<p>{`To continue, please enter the ${length}-digit verification code sent to your ${email} email.`}</p>
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
