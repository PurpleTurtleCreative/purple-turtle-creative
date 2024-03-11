/**
 * FormCustomerCreateAccount component
 *
 * Creates a new customer account.
 */

import { CustomerContext } from '../customer/CustomerContext.jsx';

import FormInputCustomerEmail from './FormInputCustomerEmail.jsx';
import FormInputCustomerPassword from './FormInputCustomerPassword.jsx';
import FormStepVerificationCode from './FormStepVerificationCode.jsx';

import { useContext, useState } from '@wordpress/element';

export default function FormCustomerCreateAccount( onSuccess ) {
	const {
		emailInput,
		isLoggedIn,
		passwordInput,
		processingStatus,
		signup,
	} = useContext(CustomerContext);
	const [ error, setError ] = useState('');
	const [ confirmPasswordInput, setConfirmPasswordInput ] = useState('');

	const handleSubmit = async (event) => {
		event.preventDefault();

		if ( confirmPasswordInput !== passwordInput ) {
			setError('password_mismatch');
			return;
		} else {
			setError('');
		}

		processSignup();
	};

	const handleEmailVerificationSuccess = (res) => {
		setError('');
	};

	const processSignup = async () => {
		signup().then(res => {
			if ( 'success' === res?.status ) {
				onSuccess(res);
				setError('');
			} else {
				if ( 403 === res?.code ) {
					// Signup could not be authorized due to missing
					// email verification. Show email verification step.
					setError('unverified_email');
				} else if ( res?.message ) {
					setError(res.message);
				} else {
					setError('Failed to create new account. Please try again.');
				}
			}
		});
	};

	let errorText = error;
	if ( 'password_mismatch' === error ) {
		errorText = 'Passwords do not match. Please ensure your password was entered correctly into both fields and try again.';
	} else if ( 'unverified_email' === error ) {
		errorText = '';
	}

	let innerContent = null;
	if ( isLoggedIn() ) {
		innerContent = <p>You are already logged in.</p>;
	} else if ( 'loading' === processingStatus ) {
		innerContent = <p>Loading...</p>;
	} else if ( 'unverified_email' === error ) {
		innerContent = (
			<FormStepVerificationCode
				email={emailInput}
				codeLength={6}
				onSuccess={handleEmailVerificationSuccess}
			/>
		);
	} else {

		let passwordExtraClassNames = '';
		if ( 'password_mismatch' === error ) {
			passwordExtraClassNames += ' input-error';
		}

		innerContent = (
			<form onSubmit={handleSubmit}>
				<FormInputCustomerEmail />
				<FormInputCustomerPassword
					extraClassNames={passwordExtraClassNames}
				/>
				<div className={"customer-confirm-password"+passwordExtraClassNames}>
					<label htmlFor="customer-confirm-password">Confirm Password</label>
					<input
						type="password"
						id="customer-confirm-password"
						name="confirm_password"
						value={confirmPasswordInput}
						onChange={(event) => { setConfirmPasswordInput(event.target.value) }}
						required
					/>
				</div>
				{/* @todo ADD CLOUDFLARE TURNSTILE: https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#explicitly-render-the-turnstile-widget */}
				<button type="submit">Create Account</button>
				<p><small>By submitting this form, you agree to our <a href="https://purpleturtlecreative.com/privacy-policy/">Privacy Policy</a> and to receiving important email messages from Purple Turtle Creative about your purchases. A verification email will be sent to confirm your account creation.</small></p>
			</form>
		);
	}

	return (
		<div className="ptc-FormCustomerCreateAccount">
			{ errorText && <p>{errorText}</p> }
			{innerContent}
		</div>
	);
}
