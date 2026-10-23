# To do

What is still to be done on Delicja. Finished items move to `progress.md`.

---

## Language versions (WPML)

Three languages: **Danish** (main, no prefix), **English** `/en/`, **Polish** `/pl/`.

**Decided**
- Plugin: WPML Multilingual CMS + WooCommerce Multilingual.
- The licence is bought by the client (Delicja ApS) in their own name; we get access to the account.
- Pages and Elementor templates are translated by hand (WPML translation editor).
- Products, categories and attributes are translated automatically when saved (WPML automatic translation),
  including slugs and Rank Math meta title and description. The client only writes in Danish.
- Prices, stock and images are shared by all languages.

**Before installing**
- [ ] Client buys the WPML licence and gives us access. Check the current price and automatic translation credits.
- [ ] Switch the WordPress site language from `en_US` to Danish (the main language is hard to change later).
- [ ] Replace the English test names of products and categories with Danish ones.

**Setup**
- [ ] Install WPML + WooCommerce Multilingual, languages DA (main) / EN / PL, URLs with a language folder (`/en/`, `/pl/`).
- [ ] Automatic translation only for products, product categories and attributes; publish without review.
- [ ] Glossary: Polish brand and product names (Krakus, Wedel, Ptasie Mleczko, …) are never translated.
- [ ] Translate slugs of pages, categories and products.
- [ ] Translate Theme Builder templates (header, footer, product card, shop, product, cart, checkout, purchase summary) and menus.
- [ ] Translate pages by hand (front page, about, contact, terms, delivery, cookies, privacy).
- [ ] Code texts (`inc/*.php`, shortcodes, buttons) as translatable strings; translate them in String Translation.
- [ ] `inc/schema.php`: language, URLs and breadcrumbs from the current language.
- [ ] Rank Math: meta title and description per language, check `hreflang` and the sitemaps.
- [ ] WooCommerce e-mails to customers in the customer's language.
- [ ] Language switcher in the header (desktop and mobile menu). No automatic redirect by browser language.
- [ ] Complianz cookie banner in all three languages.
- [ ] Test: add a product in Danish → EN and PL versions appear with translated slug and meta; order in each language.
