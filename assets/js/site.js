/* Delicja custom scripts. No jQuery. */

/* === Product card === */
// After an AJAX add to cart the card button says how many of the product are in the cart ("1 in cart"),
// like the block theme's product button. WooCommerce marks the button with the class "added".
document.addEventListener('click', (event) => {
	const button = event.target.closest('.dl-card-button .ajax_add_to_cart');
	if (!button) {
		return;
	}

	const count = Number(button.dataset.dlInCart || 0) + Number(button.dataset.quantity || 1);
	const observer = new MutationObserver(() => {
		if (button.classList.contains('added') && !button.classList.contains('loading')) {
			button.dataset.dlInCart = String(count);
			button.textContent = `${count} in cart`;
			observer.disconnect();
		}
	});
	observer.observe(button, { attributes: true, attributeFilter: ['class'] });
});
