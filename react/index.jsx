/**
 * Renders customer components.
 */

import { CustomerContextProvider } from './components/customer/CustomerContext.jsx';
import FormCustomerAuthentication from './components/forms/FormCustomerAuthentication.jsx';

import { createRoot } from '@wordpress/element';

document.addEventListener('DOMContentLoaded', () => {
	const rootNode = document.querySelector('#ptc-react-customer-authentication');
	if ( rootNode ) {

		const handleSuccess = (res) => {
			window.console.log('Successfully authenticated!', res);
		};

		createRoot(rootNode).render((
			<CustomerContextProvider>
				<FormCustomerAuthentication onSuccess={handleSuccess} />
			</CustomerContextProvider>
		));
	}
});
