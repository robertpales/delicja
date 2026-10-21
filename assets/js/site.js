/* Delicja custom scripts. No jQuery. */

/* === Product card === */
// Once a product is in the cart, the card's "Add to cart" button turns into a stepper (− "2 in cart" +) with a
// "View cart" link below. The first click is WooCommerce's own AJAX add to cart (it also works without
// JavaScript, as a link); − and + change the quantity through WooCommerce's Store API. The mini cart in the
// header refreshes on the same events WooCommerce's blocks use.
(() => {
	const api = (document.querySelector('link[rel="https://api.w.org/"]')?.href || '/wp-json/') + 'wc/store/v1/cart';
	const cartUrl = window.wc_add_to_cart_params?.cart_url || '/cart/';
	let nonce = '';
	let items = new Map();

	const request = async (path = '', body = null) => {
		const response = await fetch(api + path, {
			method: body ? 'POST' : 'GET',
			credentials: 'same-origin',
			headers: body ? { 'Content-Type': 'application/json', Nonce: nonce } : {},
			body: body ? JSON.stringify(body) : null,
		});
		nonce = response.headers.get('Nonce') || nonce;
		const data = await response.json();
		if (!response.ok) {
			throw data;
		}
		return data;
	};

	const stepper = (button) => {
		const element = document.createElement('div');
		element.className = 'dl-stepper';
		element.innerHTML = '<button type="button" class="dl-stepper-minus" aria-label="Remove one"></button>'
			+ '<span class="dl-stepper-count" aria-live="polite"></span>'
			+ '<button type="button" class="dl-stepper-plus" aria-label="Add one more"></button>';
		button.after(element);
		return element;
	};

	const render = (wrapper) => {
		const button = wrapper.querySelector('.ajax_add_to_cart');
		if (!button) {
			return;
		}

		const quantity = items.get(Number(button.dataset.product_id))?.quantity || 0;
		const current = wrapper.querySelector('.dl-stepper');
		const link = wrapper.querySelector('.added_to_cart');
		if (!quantity) {
			button.hidden = false;
			button.classList.remove('added');
			current?.remove();
			link?.remove();
			return;
		}

		const element = current || stepper(button);
		element.querySelector('.dl-stepper-count').textContent = `${quantity} in cart`;
		button.hidden = true;
		if (!link) {
			element.insertAdjacentHTML('afterend', `<a href="${cartUrl}" class="added_to_cart wc_forward">View cart</a>`);
		}
	};

	const show = (cart) => {
		items = new Map(cart.items.map((item) => [item.id, item]));
		document.querySelectorAll('.dl-card-button').forEach(render);
	};

	const sync = () => request().then(show).catch(() => {});

	// Refresh the header mini cart, as WooCommerce's own blocks do after a change.
	const notify = (name) => {
		document.body.dispatchEvent(new CustomEvent(name, { bubbles: true, detail: { preserveCartData: false, dlCard: true } }));
	};

	document.addEventListener('click', async (event) => {
		const add = event.target.closest('.dl-card-button .ajax_add_to_cart');
		if (add) {
			// WooCommerce marks the button "added" when its AJAX request is done.
			const observer = new MutationObserver(() => {
				if (add.classList.contains('added') && !add.classList.contains('loading')) {
					observer.disconnect();
					sync();
				}
			});
			observer.observe(add, { attributes: true, attributeFilter: ['class'] });
			return;
		}

		const step = event.target.closest('.dl-stepper button');
		if (!step) {
			return;
		}

		const wrapper = step.closest('.dl-card-button');
		const id = Number(wrapper.querySelector('.ajax_add_to_cart')?.dataset.product_id);
		wrapper.classList.add('is-busy');
		try {
			if (!nonce) {
				show(await request());
			}
			const item = items.get(id);
			if (step.classList.contains('dl-stepper-plus')) {
				show(await request('/add-item', { id, quantity: 1 }));
				notify('wc-blocks_added_to_cart');
			} else if (item) {
				show(await (item.quantity > 1
					? request('/update-item', { key: item.key, quantity: item.quantity - 1 })
					: request('/remove-item', { key: item.key })));
				notify('wc-blocks_removed_from_cart');
			}
		} catch (error) {
			// For example not enough stock: show what is really in the cart.
			await sync();
		}
		wrapper.classList.remove('is-busy');
	});

	// The cart changed somewhere else (for example in the mini cart drawer).
	['wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart'].forEach((name) => {
		document.body.addEventListener(name, (event) => {
			if (!event.detail?.dlCard) {
				sync();
			}
		});
	});

	// Products already in the cart show the stepper right away. WooCommerce sets this cookie while the cart has items.
	if (document.cookie.includes('woocommerce_items_in_cart=')) {
		sync();
	}
})();
