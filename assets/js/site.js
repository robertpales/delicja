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

/* === Hero === */
// Homepage hero: a table with Polish food (Elementor Hotspot widget, one hotspot per product). Nothing moves until
// the mouse is on a product (a tap on phones): the rest of the table dims a little, the product's short story shows
// (the hotspot tooltip) and the product changes, the doughnut gets a bite, the bread a slice cut off (the second
// picture, hidden Image widget "dl-hero-scene-after", shown on the table only around that product, under the
// tooltips; mask per product in home.css).
// Versions to compare, until one is chosen: ?hero=stories (story only), ?hero=bites (change only), default both.
(() => {
	const hero = document.querySelector('.dl-hero');
	const art = hero?.querySelector('.dl-hero-art');
	const after = art?.querySelector('.dl-hero-scene-after img');
	const spots = art ? [...art.querySelectorAll('.dl-hero-scene .e-hotspot')] : [];
	if (!after || !spots.length) {
		return;
	}
	const table = spots[0].parentElement;

	const version = new URLSearchParams(location.search).get('hero') || 'both';
	hero.dataset.dlHero = version;
	// The story-only version keeps the product as it is: it stays bright while the rest dims.
	const source = version === 'stories' ? table.querySelector(':scope > img') : after;

	const layers = spots.map((spot) => {
		const key = [...spot.classList].find((name) => name.startsWith('elementor-repeater-item-'))?.slice(24);
		const layer = document.createElement('div');
		layer.className = 'dl-hero-after dl-hero-after--' + key;
		const picture = source.cloneNode();
		picture.removeAttribute('fetchpriority');
		picture.alt = '';
		layer.append(picture);
		spots[0].before(layer);
		return layer;
	});

	const show = (index) => {
		layers.forEach((layer, i) => layer.classList.toggle('is-on', i === index));
		art.classList.toggle('is-focus', index > -1);
	};

	spots.forEach((spot, index) => {
		spot.addEventListener('pointerenter', (event) => event.pointerType === 'mouse' && show(index));
		spot.addEventListener('pointerleave', (event) => event.pointerType === 'mouse' && show(-1));
		spot.addEventListener('click', (event) => {
			if (event.pointerType !== 'mouse') {
				show(layers[index].classList.contains('is-on') ? -1 : index);
			}
		});
	});
})();
