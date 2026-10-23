# To do

What is still to be done on Delicja, split by who does it. Finished items move to `progress.md`.

- **Client**: Delicja ApS (the owner). Matej asks for everything in this section and passes it on.
- **Matej**: design in Elementor, communication with the client.
- **Robo**: technical setup (plugins, WooCommerce, code, server).

---

## Client (Matej asks, then sends it to Robo)

**Page texts** (needed before launch; the payment provider checks these pages)
- [ ] Terms and conditions (Handelsbetingelser) → page `terms-and-conditions`.
- [ ] Delivery and returns → page `levering-og-returnering`.
- [ ] Privacy policy → page `privacy-policy`.

**Shop information**
- [ ] Opening hours of the shop.
- [ ] Delivery: area, prices (by distance from the shop?), delivery days and times.

**Payment gateway (Flatpay / Frisbii Pay)**
- [ ] Ask Flatpay to activate online payments (Frisbii Pay) for the website delicja.dk.
- [ ] Invite `pales.robo@gmail.com` as a user in the Frisbii (Billwerk+) admin. If that is not possible: send the Live Private Key and a test key or test account.

**Language versions**
- [ ] Which language is the main (default) one: Danish, English or Polish. The main language has no prefix in the URL,
  the other two get a language folder (`/da/`, `/en/`, `/pl/`). The client writes products in the main language.
- [ ] Buy the WPML licence, plan **Multilingual CMS**, in the name of Delicja ApS, and give us access to the account. https://app.wpml.org/purchase

**Order of work for the language versions**
1. Client → Matej: main language + WPML account.
2. Matej → Robo: tells Robo that both are ready.
3. Robo: installs and sets up WPML, then tells Matej.
4. Matej: pages and templates in the main language.
5. Matej: translates them into the other two languages from the final main-language version.

---

## Matej

- [ ] Tell Robo when the client has chosen the main language and given access to the WPML account.
- [ ] After Robo has set up WPML: pages, Theme Builder templates and menus in the main language.
- [ ] When the main-language version is final: translate it into the other two languages in the WPML translation editor.

---

## Robo

**WooCommerce**
- [ ] Tax rate: remove the city limit (Herning) so the 25 % moms applies to all of Denmark.
- [ ] Pickup location: add the street address (Bethaniagade 5).
- [ ] Delivery methods and prices, when the client sends them.
- [ ] Put the client's texts on the legal pages; set the terms page in WooCommerce; remove the sample page `refund_returns`.

**Payment gateway (Frisbii Pay)**, when the client has done their part
- [ ] Keys, payment methods, logos, order status mapping; check webhooks.
- [ ] Test payments on test.delicja.dk.
- [ ] Turn off cash on delivery; payment icons in the footer match the real methods.

**Language versions (WPML)**, when Matej says the client has chosen the main language and given access to the WPML account

Recommended: Danish is the main language (no prefix), English `/en/`, Polish `/pl/`. The client decides. Pages and
templates are translated by hand (Matej). Products, categories and attributes are translated automatically when saved,
including slugs and Rank Math meta title and description; the client only writes in the main language.
Prices, stock and images are shared.

- [ ] Switch the WordPress site language from `en_US` to the main language before installing (hard to change later).
- [ ] Install WPML + WooCommerce Multilingual, languages DA / EN / PL, main language without a prefix.
- [ ] Tell Matej that WPML is ready for the pages in the main language.
- [ ] Automatic translation for products, categories and attributes; glossary so Polish brand and product names are not translated.
- [ ] Translated slugs for pages, categories and products.
- [ ] Code texts (`inc/*.php`, shortcodes, buttons) as translatable strings, translated in String Translation.
- [ ] `inc/schema.php`: language, URLs and breadcrumbs from the current language.
- [ ] Rank Math per language: meta, `hreflang`, sitemaps. WooCommerce e-mails in the customer's language. Complianz banner in all languages.
- [ ] Test: a product added in the main language gets the other two versions; an order in each language.

**Before launch**
- [ ] Turn off WooCommerce "Coming soon" mode.
- [ ] Order e-mails back to the client's address (now `web@digistudio.sk` for testing).
- [ ] Replace test products and categories with real ones in the main language.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] Rich Results Test on the front page and a product.
