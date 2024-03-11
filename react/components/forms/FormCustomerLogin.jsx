/**
 * FormCustomerLogin component
 *
 * Logs in an existing customer.
 */

import { CustomerContext } from '../customer/CustomerContext.jsx';

import FormInputCustomerEmail from './FormInputCustomerEmail.jsx';
import FormInputCustomerPassword from './FormInputCustomerPassword.jsx';

import { useContext } from '@wordpress/element';

export default function FormCustomerLogin() {
	const { isLoggedIn, login, processingStatus } = useContext(CustomerContext);
	const [ error, setError ] = useState('');

	const handleSubmit = async (event) => {
		event.preventDefault();
		setError('');
		login().then(res => {
			if ( 'success' !== res?.status ) {
				setError(res.message);
			}
		});
	};

	let innerContent = null;
	if ( isLoggedIn() ) {
		innerContent = <p>You are already logged in.</p>;
	} else {
		if ( 'loading' === processingStatus ) {
			innerContent = <p>Loading...</p>;
		} else {
			innerContent = (
				<form onSubmit={handleSubmit}>
					<FormInputCustomerEmail />
					<FormInputCustomerPassword />
					{/* @todo ADD CLOUDFLARE TURNSTILE: https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#explicitly-render-the-turnstile-widget */}
					<button type="submit">Log In</button>
				</form>
			);
		}
	}

	return (
		<div className="ptc-FormCustomerLogin">
			{ error && <p>{error}</p> }
			{innerContent}
		</div>
	);
}
