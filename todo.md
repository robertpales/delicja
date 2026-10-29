# To do

What is still to be done on Delicja, split by who does it. Finished items move to `progress.md`.

- **Client**: Delicja ApS (the owner). Matej asks for everything in this section and passes it on.
- **Matej**: design in Elementor, communication with the client.
- **Robo**: technical setup (plugins, WooCommerce, code, server).

---

## Client (Matej asks, then sends it to Robo)

**Page texts** (needed before launch; the payment provider checks these pages)
- [ ] Terms and conditions: received in Danish and on the page (2026-10-28). Open points for the client (her text, we
  do not change it): the file says "Udkast til gennemgang" (draft), is it final? It does not mention pant (charged on
  top of the price for drinks in cans and bottles), the 2-year right to complain (reklamationsret) is only "efter
  købelovens regler", and for goods without an exception (e.g. cosmetics) it does not say how to use the 14-day
  right of withdrawal. Two new rules from it are not on the site yet: minimum 499 kr. for delivery, and delivery
  orders at least 24 hours before the delivery day (see Robo → WooCommerce and Matej).
- [ ] Delivery and returns → page `delivery-and-returns`.
- [ ] Privacy policy → page `privacy-policy`. Matej asked for a draft (2026-10-29): English draft written from what
  the site really collects (orders, accounts, Google login, payments, order messages, e-mails about deals, statistics,
  the services that get data), in Claude Docs "Delicja – Privacy policy (draft)"
  (https://claude.ai/artifact/Qa78P55WvtoL3JVzPJkXRL); Matej sends it to the client as a PDF to confirm (she is the
  data controller). Then it goes on the page in Danish (Matej). Still to decide later and add to it: after how many
  years inactive accounts are deleted, the newsletter service, a courier if one delivers some orders.
- [ ] Terms and Delivery & returns: the client's responsibility (she is the seller); she gets them from a lawyer or a
  template service if needed. (Terms received 2026-10-28.)
- [ ] Confirm or correct the texts we wrote on the site: the four promises and the FAQ answers on the homepage,
  the About us story.
- [ ] Homepage hero picture: confirm the short product stories in it (e.g. the doughnut "filled with rose jam").
  (Apples and fresh milk: the shop sells them, 2026-10-26.)
- [ ] Customer accounts: should customers be able to create an account, and log in with Google or Facebook? (Robo
  switches it on.)

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
- [ ] Later (Matej, 2026-10-29: leave it for now): products sold by weight (cheese, cold cuts from the counter);
  online they need a fixed pack size and price, or something we add for them later.
- [ ] Language of the products (client asks if she may write them in Polish, 2026-10-29): the food information
  (ingredients, allergens, nutrition, storage) must be in Danish on a Danish shop, and WPML's automatic translation
  ("Translate everything") only starts from the main language, Danish. Product names may stay Polish. If she writes in
  Polish, every product must be sent to translation by hand and the Danish allergens checked. Easiest for her: copy
  the Danish label (sticker) on the pack. Robo decides the WPML setup. Matej, 2026-10-29: the Danish product
  information is solved later with an AI (LLM) plugin.
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
  card and invoice her. 2026-10-29: she has written to her bank; she pays herself (no invoice through us, VAT).

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
- [ ] Delivery texts on the site (homepage FAQ, the "Not in Herning?" panel, Delivery & returns) once the client
  confirms the terms: minimum 499 kr. for delivery, delivery orders at least 24 hours before the delivery day.
- [ ] Legal and shop information pages (Terms and conditions, Privacy policy, Delivery & returns, Cookies): put the
  client's texts on them when she sends them (Robo, 2026-10-27: Matej may edit these pages). Privacy Policy still
  shows WordPress's sample text ("Suggested text: …", comments, Gravatar); Terms and Delivery & returns say
  "[TODO] Indhold mangler". The pages already have their design (template Information page). The terms page is
  already set in WooCommerce and the sample page `refund_returns` is deleted.
  - Delivery & returns must say the same as the checkout: Herning 49 kr. (free from 799 kr.), up to 15 km 79 kr.
    (free from 999 kr.), 15–40 km 129 kr. (free from 1,299 kr.), deliveries Wednesday and Friday 15:00–17:00; rest
    of Jutland 219 kr., time agreed with the customer; further away by arrangement. Pickup in the shop free, ready in
    about two hours, collect within 3 days the shop is open after the "ready" e-mail, otherwise the order is
    cancelled; the card is charged at pickup or delivery. Towns in each zone: Robo's list below (Robo → WooCommerce).
---

## Robo

**WooCommerce**
- [ ] From the client's terms (2026-10-28): minimum order 499 kr. for delivery (pickup has no minimum?), and delivery
  orders at least 24 hours before the delivery day (Wednesday or Friday). Checkout and shipping should follow this.
- [ ] Delivery zones (the Delivery & returns page itself is Matej's, see above). Zones (2026-10-27, by road distance from Bethaniagade 5 to the town centre, OSRM ≈ Google Maps): Herning 7400;
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

**Cookies and privacy** (found while writing the privacy policy draft, 2026-10-29)
- [ ] Cookies before consent: on test.delicja.dk, before the visitor answers the cookie banner, the site already sets
  `sbjs_*` (WooCommerce order attribution) and `tk_ai` (Jetpack WooCommerce Analytics); after Accept also Jetpack
  Stats (`stats.wp.com`, `tk_*`), on the test site too. Danish rules: only necessary cookies before consent. Either
  turn off Order attribution (WooCommerce → Settings → Advanced → Features) and the Jetpack modules Stats and
  WooCommerce Analytics (Google Analytics and Clarity already cover statistics), or make them wait for consent (e.g.
  the WP Consent API plugin, which Complianz and WooCommerce support; check). The privacy policy draft lists them as
  "only with consent"; if they are turned off, tell Matej so he removes them from it.
- [ ] Complianz wizard says the site uses Google reCAPTCHA and Google Maps; it does not (none found), so the cookie
  policy lists services that are not there. Its company e-mail is `info@`, the terms use `shop@delicja.dk`.
- [ ] WooCommerce → Settings → Accounts & Privacy → Personal data retention is empty: set it when the client answers
  the privacy policy draft (inactive accounts after how many years).

**Statistics**
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
  Stock is the shop's matter (Matej, 2026-10-29): when an ordered product is missing, they refund it or call the
  customer, so check that the shop can charge less than the reserved amount or refund one item.

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
- [ ] Live search (`inc/search.php`): Store API results in the current language (check with WPML; add `lang` to the
  endpoint if needed).
- [ ] `inc/schema.php`: language, URLs and breadcrumbs from the current language.
- [ ] Rank Math per language: meta, `hreflang`, sitemaps. WooCommerce e-mails in the customer's language. Complianz banner in all languages.
- [ ] Test: a product added in the main language gets the other two versions; an order in each language.

**Before launch**
- [ ] Do not delete the products now on the site: they are real (Matej, 2026-10-27). The client adds the rest
  herself, in Danish, as agreed in the contract.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] After the move to delicja.dk: check that WP Fastest Cache → Exclude still has the three WooCommerce cookies
  and that `.htaccess` has them in "Start WPFC Exclude" (`inc/cache.php`); repeat the guest test (a second visitor
  must not see the first one's cart or address).
- [ ] Rich Results Test on the front page and a product.
- [ ] robots.txt on delicja.dk: search engines and AI search crawlers (OAI-SearchBot, PerplexityBot, ClaudeBot,
  Google-Extended) allowed; check `/llms.txt` and the sitemap there. The test site stays `noindex`.
- [ ] Pages still marked "Indhold mangler" (om-os, delivery): fill in or remove `om-os` (About us is `/about/`). Terms
  done (2026-10-28).
  Kontakt and Deals (`/deals/`) are done (Matej, 2026-10-23).

**SEO with the main language** (when the Danish version is made)
- [ ] Meta titles and descriptions in Danish for all pages, categories and products; the English ones set on
  2026-10-27 (front page, About us, Shop, Contact, Deals) become the `/en/` version. llms.txt in Danish.
- [ ] Share image of the front page (link previews in WhatsApp, Messenger, Facebook): Matej wants the shop front
  instead of the window photo (`delicja-about-shop-window.webp`), 2026-10-27. Prepared in the media library: media
  504, `delicja-share-shop-front.jpg`, 1200 × 630. Set it in Rank Math (front page → Social, Facebook and Twitter
  image). WhatsApp caches previews: test with a new link, e.g. `https://test.delicja.dk/?v=2`.
