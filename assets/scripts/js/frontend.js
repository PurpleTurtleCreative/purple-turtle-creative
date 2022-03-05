document.addEventListener('DOMContentLoaded', function() {

	// Toggle mobile menu open/close.
	document.querySelector('#site-navigation button.menu-toggle')
		.addEventListener('click', function() {
			document.documentElement.classList.toggle('mobile-menu-open');
		});

	// Close mobile menu on overlay click.
	document.getElementById('overlay')
		.addEventListener('click', function() {
			document.documentElement.classList.remove('mobile-menu-open');
		});
});
