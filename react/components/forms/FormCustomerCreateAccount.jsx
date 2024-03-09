/**
 * FormCustomerCreateAccount component
 *
 * Creates a new customer account.
 */

import { CustomerContext } from '../customer/CustomerContext.jsx';

import FormInputCustomerEmail from './FormInputCustomerEmail.jsx';
import FormInputCustomerPassword from './FormInputCustomerPassword.jsx';

import { useContext } from '@wordpress/element';

export default function FormCustomerCreateAccount() {
	const { isLoggedIn, signup, processingStatus, passwordInput } = useContext(CustomerContext);
	const [ error, setError ] = useState('');
	const [ confirmPasswordInput, setConfirmPasswordInput ] = useState('');

	const handleSubmit = async (event) => {
		event.preventDefault();
		setError('');

		if ( confirmPasswordInput !== passwordInput ) {
			setError('password_mismatch');
			return;
		}

		signup().then(res => {
			if ( 'success' !== res?.status ) {
				setError(res.message);
			}
		});
	};

	let errorText = error;
	if ( 'password_mismatch' === error ) {
		errorText = 'Passwords do not match. Please ensure your password was entered correctly into both fields and try again.';
	}

	let innerContent = null;
	if ( isLoggedIn() ) {
		innerContent = <p>You are already logged in.</p>;
	} else {
		if ( 'loading' === processingStatus ) {
			innerContent = <p>Loading...</p>;
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
					<button type="submit">Create Account</button>
				</form>
			);
		}
	}

	return (
		<div className="ptc-FormCustomerCreateAccount">
			{ errorText && <p>{errorText}</p> }
			{innerContent}
		</div>
	);
}
