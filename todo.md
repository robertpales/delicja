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
- [ ] Privacy policy → page `privacy-policy`. It must also cover the e-mails about deals and new products (customers
  can sign up for them when they create an account, 2026-10-27), and the statistics tools Google Analytics and
  Microsoft Clarity (only with the visitor's consent in the cookie banner).
- [ ] If the client has no texts: they get them from a lawyer or a template service. We do not write legal texts.
  (2026-10-27: the client will send all three texts; we wait for them.)
- [ ] Confirm or correct the texts we wrote on the site: the four promises and the FAQ answers on the homepage,
  the About us story.
- [ ] Homepage hero picture: confirm the short product stories in it (e.g. the doughnut "filled with rose jam").
  (Apples and fresh milk: the shop sells them, 2026-10-26.)
- [ ] Customer accounts: should customers be able to create an account, and log in with Google or Facebook? (Robo
  switches it on.)

**Shop information**
- [ ] Pickup orders: collect within **3 days** (Matej's proposal, 2026-10-27; the client confirms). On the site it is
  3 days the shop is open, counted from the "Ready for pickup" e-mail (Robo, 2026-10-27). Ready in about two
  hours; a reminder after 2 days; not collected after 3 days → the order is cancelled, the card reservation (valid
  about 7 days) released and the goods go back on the shelf. Payment is charged only at handover (see Robo →
  Payment gateway). Then the Delivery & returns text and the FAQ say the same.
- [ ] Who packs and handles the online orders in the shop? (The orders e-mail is `shop@delicja.dk`, 2026-10-26.)

**Food rules** (required for selling food online in Denmark)
- [ ] Fødevarestyrelsen: is online sale and delivery part of the shop's food registration? (The link to the shop's
  inspection reports on findsmiley.dk is in the footer, 2026-10-26.) The client has asked Fødevarestyrelsen and waits
  for their answer (2026-10-27).
- [ ] Product information shown before purchase (EU 1169/2011): ingredients with allergens highlighted, net quantity,
  storage conditions, nutrition table, producer or importer (name and address), country of origin where required.
  Best source: supplier data sheets.
- [ ] Pant: when she adds a drink in a can or bottle, she picks its pant class in *Product data → General → Pant*
  (several products at once: Products → Bulk edit → Pant), from the Danish pant mark on the pack: A 1 kr. (glass and
  cans under 1 l), B 1.50 kr. (plastic under 1 l), C 3 kr. (from 1 l). The shop then shows "+ pant" under the price
  and adds the pant in the cart (2026-10-27). No drinks on the site yet.

**Products and stock** (the client adds the products herself in WooCommerce, as agreed in the contract; we do not
need a product list from her, 2026-10-27)
- [ ] Stock: the shop and the webshop sell from the same shelves. Is stock kept up to date by hand in WooCommerce or
  synced with the cash register? Otherwise the webshop sells products that are already gone.
- [ ] Products sold by weight (cheese, cold cuts from the counter): online they need a fixed pack size and price.
- [ ] When she adds a product, in the main language (Danish), from the pack or the supplier's data sheet: name, price
  incl. moms, weight or volume, category, photo, ingredients with allergens, nutrition, producer or importer, country
  of origin where required, and storage (keep chilled, frozen, or cool and dry),
  allergens it contains and may contain, vegetarian/vegan if stated. The product page shows them as icons.
  The products now on the site are real and stay (Matej, 2026-10-27); their allergens are filled in from the
  ingredient lists (to be checked), storage is still empty.

**Online presence** (Google, local search, AI answers)
- [ ] Manager access to the Google Business Profile, so opening hours, photos and the website link match the site.
- [ ] Other profiles to link besides Instagram @delicja.dk (Facebook, TikTok, …).
- [ ] May we show their Google reviews on the site (real reviews only)?
- [ ] More real photos: the shop inside, shelves, the counter, people. People in photos must agree to it (GDPR);
  confirm this for the photos already used (Elżbieta, the customer at the counter).
- [ ] A sharp photo of Elżbieta for the About us opening (asked by Matej, 2026-10-27): now it is a blurry frame from
  the fair video. Best: in the shop by the shelves or the counter, landscape, daylight, the phone's main camera (not
  a screenshot from a video), looking at the camera.


**Payment gateway (Flatpay / Frisbii Pay)**
- [ ] Ask Flatpay to activate online payments (Frisbii Pay) for the website delicja.dk. The client has asked Flatpay
  for online payments and the API key and waits for their reply (2026-10-27).
- [ ] Invite `pales.robo@gmail.com` as a user in the Frisbii (Billwerk+) admin. If that is not possible: send the Live Private Key and a test key or test account.
- [ ] Accounting: which system (e-conomic, Dinero, Billy, …)? Should orders go there automatically?

**Language versions**
- [ ] Buy the WPML licence, plan **Multilingual CMS**, in the name of Delicja ApS, and give us access to the account. https://app.wpml.org/purchase
  Check how many automatic translation credits the plan includes; more cost extra.
  2026-10-27: her Visa payment fails. Next: the error message or a screenshot; try "Use a different payment method"
  (PayPal) at checkout; check in the bank app that online payments abroad are allowed and approve with MitID. If it
  still fails: we buy it in an account with Delicja ApS's details (name, address, CVR, her e-mail), pay with our
  card and invoice her.

**Order of work for the language versions**
1. Client → Matej: main language (done: **Danish**, 2026-10-26) + WPML account (still open).
2. Matej → Robo: tells Robo that both are ready.
3. Robo: installs and sets up WPML, then tells Matej.
4. Matej: pages and templates in the main language.
5. Matej: translates them into the other two languages from the final main-language version.

---

## Matej

- [ ] Choose the homepage hero version (`/?hero=stories`, `/?hero=bites` or `/` both), then remove the `?hero=` switch
  and the unused code (`site.js` and `home.css`, Hero).
- [ ] Continue the design, but now unify and finish it rather than change it: give the site its final look,
  keep what is already done and avoid big changes.
  - [ ] Style the product page (template Single Product) in the site style.
  - [ ] Related product cards (template Related product card) in the same style as all the other product cards on the site.
- [ ] A short guide for the client on adding a product (which fields, allergens and storage icons, photo size,
  category), so every product page is complete.
- [ ] Tell Robo when the client has given access to the WPML account (main language chosen: Danish, 2026-10-26).
- [ ] After Robo has set up WPML: pages, Theme Builder templates and menus in the main language.
- [ ] When the main-language version is final: translate it into the other two languages in the WPML translation editor.
- [ ] Put the client's texts on the legal pages; set the terms page in WooCommerce; remove the sample page `refund_returns`.
  Privacy Policy still shows WordPress's sample text ("Suggested text: …", comments, Gravatar); Terms and Delivery &
  returns say "[TODO] Indhold mangler". The pages already have their design (template Information page).
---

## Robo

**WooCommerce**
- [ ] Delivery & returns page: the same delivery rules as in the checkout (zones below), with the client's texts.
  Zones (2026-10-27, by road distance from Bethaniagade 5 to the town centre, OSRM ≈ Google Maps): Herning 7400;
  up to 15 km 7451, 7430, 6933 Kibæk (15.8 km, counted in); 15–40 km 7480, 7441, 7490, 6920, 7470, 7330, 7280,
  7442, 7550, 6973, 7540, 7361, 7270, 6971, 7500, 8600 Silkeborg, 7362 Hampen, 8653 Them (40.4 km, counted in);
  rest of Jutland 6000–9990; ferry islands (6720, 7884, 8305, 8592, 8789, 8799, 9940) and the rest of Denmark only
  pickup. If the client adds or moves a town, change the zone's postcodes.
- [ ] Pickup "ready" SMS/WhatsApp: the e-mail is done (`inc/pickup.php`); decide whether the shop also sends an SMS
  by hand from its phone or an SMS service sends it automatically (extra cost); tell Matej which.
- [ ] Live site: a real server cron (or a scheduled ping of `wp-cron.php` every 15 minutes) so the hourly pickup check
  also runs when visitors only get cached pages. Check what Simply offers.
- [ ] Marketing e-mails (Matej, 2026-10-27): the register form has an optional, unticked checkbox "Send me e-mails
  about deals and new products"; the date of the consent is saved in user meta `delicja_marketing_consent` (removed
  when the customer unticks it in Account details), the privacy checkbox in `delicja_privacy_accepted`
  (`inc/account.php`). Choose how the client sends the e-mails (newsletter tool) and sync or export these customers;
  every e-mail needs an unsubscribe link. Maybe the same checkbox at checkout. Customers who sign up with Google skip
  the form, so they can only opt in later in Account details.

**Statistics**
- [ ] Clarity dashboard (project z4by01ybij): Masking stays "Balanced"; after the first recordings check that the
  checkout fields are masked. Optional: IP blocking for the shop and our own IPs, link to Google Analytics.
- [ ] After launch: check Google Analytics Realtime on delicja.dk (GA4 property "Delicja ApS", G-X3VJVFR9B7, set up
  in Complianz → Statistics; on the test site only with `?dl-analytics=1`). Run the Complianz cookie scan so the
  cookie policy lists the `_ga` and Clarity (`_clck`, `_clsk`) cookies. Decide on shop events (product views, cart, purchases): the official
  "Google Analytics for WooCommerce" plugin. Search Console is Robo's (SEO).

**Deploy**
- [ ] Page cache after a deploy (asked by Matej, 2026-10-24): `inc/deploy.php` clears WP Fastest Cache on the first
  request that reaches PHP, but cached pages are served without PHP, so the homepage kept the old `site.js?ver=…`
  for a while (an iPhone still showed the old behaviour). Let the workflow trigger the purge itself after rsync
  (e.g. one request with a query string, or deleting the cache folder over SSH).

**Payment gateway (Frisbii Pay)**, when the client has done their part
- [ ] Keys, payment methods, logos, order status mapping; check webhooks. The client wants **Dankort, Visa/Mastercard,
  Apple Pay and Google Pay, no MobilePay** (2026-10-26). The footer icons and the homepage texts already show these.
- [ ] Test payments on test.delicja.dk (test cards: order → Authorised; Completed → Settled; partial; cancel).

**Language versions (WPML)**, when Matej says the client has chosen the main language and given access to the WPML account

Main language: **Danish** (the client's choice, 2026-10-26), no prefix; English `/en/`, Polish `/pl/`. Pages and
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
- [ ] Do not delete the products now on the site: they are real (Matej, 2026-10-27). The client adds the rest
  herself, in Danish, as agreed in the contract.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] Rich Results Test on the front page and a product.
- [ ] robots.txt on delicja.dk: search engines and AI search crawlers (OAI-SearchBot, PerplexityBot, ClaudeBot,
  Google-Extended) allowed; check `/llms.txt` and the sitemap there. The test site stays `noindex`.
- [ ] Pages still marked "Indhold mangler" (om-os, terms, delivery): fill in or remove `om-os` (About us is `/about/`).
  Kontakt and Deals (`/tilbud/`) are done (Matej, 2026-10-23).

**SEO with the main language** (when the Danish version is made)
- [ ] Meta titles and descriptions in Danish for all pages, categories and products; the English ones set on
  2026-10-27 (front page, About us, Shop, Contact, Deals) become the `/en/` version. llms.txt in Danish.
