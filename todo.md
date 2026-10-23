# To do

What is still to be done on Delicja, split by who does it. Finished items move to `progress.md`.

- **Client**: Delicja ApS (the owner). Matej asks for everything in this section and passes it on.
- **Matej**: design in Elementor, communication with the client.
- **Robo**: technical setup (plugins, WooCommerce, code, server).

---

## Client (Matej asks, then sends it to Robo)

**Page texts** (needed before launch; the payment provider checks these pages)
- [ ] Terms and conditions (Handelsbetingelser) → page `terms-and-conditions`. They must cover prices incl. moms,
  delivery, payment, the 14-day right of withdrawal and its exceptions (perishable food, opened hygiene products),
  the 2-year right to complain (reklamationsret) and the complaint body (Nævnenes Hus). The EU ODR link is no longer
  needed (the platform closed on 20 July 2025).
- [ ] Delivery and returns → page `levering-og-returnering`.
- [ ] Privacy policy → page `privacy-policy`.
- [ ] If the client has no texts: they get them from a lawyer or a template service. We do not write legal texts.
- [ ] Confirm or correct the texts we wrote on the site: the four promises and the FAQ answers on the homepage,
  the About us story.

**Shop information**
- [ ] Opening hours of the shop.
- [ ] Delivery: area, prices (by distance from the shop?), delivery days and times.
- [ ] Minimum order value and free delivery threshold, if any.
- [ ] Chilled and frozen products (meat, dairy, fish): how are they kept cold on the way? Only local delivery for them?
- [ ] Pickup in the shop: how soon an order is ready, and how the customer is told.
- [ ] Which e-mail address receives new orders, and who handles them in the shop.

**Food rules** (required for selling food online in Denmark)
- [ ] Fødevarestyrelsen: is online sale and delivery part of the shop's food registration? Send the link to the
  shop's page on findsmiley.dk; a food webshop must link to its latest inspection report (smiley).
- [ ] Product information shown before purchase (EU 1169/2011): ingredients with allergens highlighted, net quantity,
  storage conditions, nutrition table, producer or importer (name and address), country of origin where required.
  Best source: supplier data sheets.
- [ ] Alcohol: will beer, vodka or other alcohol be sold online? Then an age check is needed at checkout and on
  delivery (16+, or 18+ above 16.5 %).
- [ ] Deposit (pant): drinks in cans and bottles sold in Denmark need the Danish pant mark, and the pant is charged on
  top of the price. Which drinks carry pant, and which pant class (A, B, C)?

**Products and stock**
- [ ] Product list in the main language: name, price incl. moms, weight or volume, category, EAN barcode, photo.
  Ideally an export from the cash register system. Which system does the shop use?
- [ ] Stock: the shop and the webshop sell from the same shelves. Is stock kept up to date by hand in WooCommerce or
  synced with the cash register? Otherwise the webshop sells products that are already gone.
- [ ] Products sold by weight (cheese, cold cuts from the counter): online they need a fixed pack size and price.

**Online presence** (Google, local search, AI answers)
- [ ] Manager access to the Google Business Profile, so opening hours, photos and the website link match the site.
- [ ] Other profiles to link besides Instagram @delicja.dk (Facebook, TikTok, …).
- [ ] May we show their Google reviews on the site (real reviews only)?
- [ ] More real photos: the shop inside, shelves, the counter, people. People in photos must agree to it (GDPR);
  confirm this for the photos already used (Elżbieta, the customer at the counter).

**Launch**
- [ ] Launch date, and who manages the domain delicja.dk (DNS) and the website on it now.

**Payment gateway (Flatpay / Frisbii Pay)**
- [ ] Ask Flatpay to activate online payments (Frisbii Pay) for the website delicja.dk.
- [ ] Invite `pales.robo@gmail.com` as a user in the Frisbii (Billwerk+) admin. If that is not possible: send the Live Private Key and a test key or test account.
- [ ] Which payment methods to offer online (MobilePay, Dankort, Visa/Mastercard, Apple Pay, Google Pay); some need a
  separate agreement.
- [ ] Accounting: which system (e-conomic, Dinero, Billy, …)? Should orders go there automatically?

**Language versions**
- [ ] Which language is the main (default) one: Danish, English or Polish. The main language has no prefix in the URL,
  the other two get a language folder (`/da/`, `/en/`, `/pl/`). The client writes products in the main language.
- [ ] Buy the WPML licence, plan **Multilingual CMS**, in the name of Delicja ApS, and give us access to the account. https://app.wpml.org/purchase
  Check how many automatic translation credits the plan includes; more cost extra.

**Order of work for the language versions**
1. Client → Matej: main language + WPML account.
2. Matej → Robo: tells Robo that both are ready.
3. Robo: installs and sets up WPML, then tells Matej.
4. Matej: pages and templates in the main language.
5. Matej: translates them into the other two languages from the final main-language version.

---

## Matej

- [ ] Continue the design, but now unify and finish it rather than change it: give the site its final look,
  keep what is already done and avoid big changes.
  - [ ] Style the product page (template Single Product) in the site style.
  - [ ] Related product cards (template Related product card) in the same style as all the other product cards on the site.
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
