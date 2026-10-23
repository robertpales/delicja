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

/* === Picture reveal === */
// A photo in an Elementor Hotspot widget (one hotspot per thing in the photo) and a hidden Image widget with the same
// photo after a change, aligned. Nothing moves until the mouse is on a hotspot (a tap on phones): the changed photo
// shows only around that hotspot (a layer per hotspot, class <prefix>--<hotspot id>, mask in the page CSS), the rest
// of the photo dims a little (class is-focus on the container) and the tooltip tells the story.
const delicjaReveal = (art, scene, after, prefix, source = null) => {
	const changed = art?.querySelector(after + ' img');
	const spots = art ? [...art.querySelectorAll(scene + ' .e-hotspot')] : [];
	if (!changed || !spots.length) {
		return;
	}
	const picture = source || changed;

	const layers = spots.map((spot) => {
		const key = [...spot.classList].find((name) => name.startsWith('elementor-repeater-item-'))?.slice(24);
		const layer = document.createElement('div');
		layer.className = 'dl-reveal-layer ' + prefix + ' ' + prefix + '--' + key;
		const copy = picture.cloneNode();
		copy.removeAttribute('fetchpriority');
		copy.removeAttribute('loading');
		copy.alt = '';
		layer.append(copy);
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
};

/* === Hero === */
// Homepage hero: a table with Polish food. On a product its story shows and the product changes: the doughnut gets
// a bite, the bread a slice cut off (Picture reveal; masks in home.css).
// Versions to compare, until one is chosen: ?hero=stories (story only), ?hero=bites (change only), default both.
(() => {
	const hero = document.querySelector('.dl-hero');
	const art = hero?.querySelector('.dl-hero-art');
	if (!art) {
		return;
	}

	const version = new URLSearchParams(location.search).get('hero') || 'both';
	hero.dataset.dlHero = version;
	// The story-only version keeps the product as it is: it stays bright while the rest dims.
	const source = version === 'stories' ? art.querySelector('.dl-hero-scene .elementor-widget-container > img') : null;
	delicjaReveal(art, '.dl-hero-scene', '.dl-hero-scene-after', 'dl-hero-after', source);
})();

/* === Contact === */
// Contact page: the shop front. Each spot is a way to Delicja: come in (the door opens), stay for coffee (coffee and
// cake appear in the window), shop (the shelves fill up), or get it delivered (a bike with a box arrives).
// Picture reveal; masks in kontakt.css.
delicjaReveal(document.querySelector('.dl-contact-art'), '.dl-contact-shop', '.dl-contact-shop-after', 'dl-shop-after');

/* === About story === */
// About us, "It all starts with a taste": a red-and-white baker's twine runs from each chapter photo to the next,
// like photos strung on a line, zigzagging between the left and the right column. One SVG behind the photos; each
// piece draws itself when the next photo scrolls into view. Only where the photos sit side by side (782 px and up).
(() => {
	const story = document.querySelector('.dl-about-story');
	const photos = story ? [...story.querySelectorAll('.dl-chapter .dl-about-photo')] : [];
	if (photos.length < 2) {
		return;
	}

	const ns = 'http://www.w3.org/2000/svg';
	const make = (name, attributes = {}) => {
		const element = document.createElementNS(ns, name);
		Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));
		return element;
	};

	const svg = make('svg', { class: 'dl-twine', 'aria-hidden': 'true' });
	const pieces = photos.slice(1).map((photo, index) => {
		const id = 'dl-twine-' + index;
		const reveal = make('path', { class: 'dl-twine-reveal', pathLength: '1' });
		const mask = make('mask', { id, maskUnits: 'userSpaceOnUse' });
		const group = make('g', { mask: 'url(#' + id + ')' });
		const base = make('path', { class: 'dl-twine-base' });
		const stripe = make('path', { class: 'dl-twine-stripe' });
		mask.append(reveal);
		group.append(base, stripe);
		svg.append(mask, group);
		return { mask, group, paths: [reveal, base, stripe] };
	});
	story.prepend(svg);

	const wide = matchMedia('(min-width: 782px)');
	const draw = () => {
		svg.style.display = wide.matches ? '' : 'none';
		if (!wide.matches) {
			return;
		}
		const box = story.getBoundingClientRect();
		svg.setAttribute('width', box.width);
		svg.setAttribute('height', box.height);
		pieces.forEach((piece, index) => {
			const from = photos[index].getBoundingClientRect();
			const to = photos[index + 1].getBoundingClientRect();
			const x1 = from.left + from.width / 2 - box.left;
			const y1 = from.bottom - box.top - 40;
			const x2 = to.left + to.width / 2 - box.left;
			const y2 = to.top - box.top + 16;
			const drop = Math.max(150, (y2 - y1) * 0.6 + 150);
			const d = `M${x1} ${y1}C${x1} ${y1 + drop} ${x2} ${y2 - drop} ${x2} ${y2}`;
			piece.paths.forEach((path) => path.setAttribute('d', d));
			['x', 'y'].forEach((key) => piece.mask.setAttribute(key, 0));
			piece.mask.setAttribute('width', box.width);
			piece.mask.setAttribute('height', box.height);
		});
	};

	new ResizeObserver(draw).observe(story);
	wide.addEventListener('change', draw);

	// The chapter number stamps press in as their chapter scrolls into view.
	const chapters = [...story.querySelectorAll('.dl-chapter')];
	chapters.forEach((chapter) => chapter.classList.add('is-waiting'));

	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}
			if (entry.target.classList.contains('dl-chapter')) {
				entry.target.classList.replace('is-waiting', 'is-in');
			} else {
				pieces[photos.indexOf(entry.target) - 1]?.mask.classList.add('is-drawn');
			}
			observer.unobserve(entry.target);
		});
	}, { threshold: 0.25 });
	photos.slice(1).forEach((photo) => observer.observe(photo));
	chapters.forEach((chapter) => observer.observe(chapter));
})();

