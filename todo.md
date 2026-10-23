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
- [ ] Buy the WPML licence, plan **Multilingual CMS**, in the name of Delicja ApS, and give us access to the account. https://app.wpml.org/purchase

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

**Language versions (WPML)**, when the client has bought the licence

Recommended: Danish is the main language (no prefix), English `/en/`, Polish `/pl/`. Pages and templates are translated
by hand (Matej). Products, categories and attributes are translated automatically when saved, including slugs and
Rank Math meta title and description; the client only writes in Danish. Prices, stock and images are shared.

- [ ] Switch the WordPress site language from `en_US` to Danish before installing (hard to change later).
- [ ] Install WPML + WooCommerce Multilingual, languages DA / EN / PL with language folders.
- [ ] Automatic translation for products, categories and attributes; glossary so Polish brand and product names are not translated.
- [ ] Translated slugs for pages, categories and products.
- [ ] Code texts (`inc/*.php`, shortcodes, buttons) as translatable strings, translated in String Translation.
- [ ] `inc/schema.php`: language, URLs and breadcrumbs from the current language.
- [ ] Rank Math per language: meta, `hreflang`, sitemaps. WooCommerce e-mails in the customer's language. Complianz banner in all languages.
- [ ] Test: a product added in Danish gets EN and PL versions; an order in each language.

**Before launch**
- [ ] Turn off WooCommerce "Coming soon" mode.
- [ ] Order e-mails back to the client's address (now `web@digistudio.sk` for testing).
- [ ] Replace test products and categories with real ones in Danish.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] Rich Results Test on the front page and a product.
