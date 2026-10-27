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
- [ ] Pickup orders: collect within **3 days** (Matej's proposal, 2026-10-27; the client confirms). Ready in about two
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

---

## Robo

**WooCommerce**
- [ ] Tax rate: remove the city limit (Herning) so the 25 % moms applies to all of Denmark.
- [ ] Pickup location: add the street address (Bethaniagade 5).
- [ ] Delivery methods and prices from the client (2026-10-26). Now the zone "Denmark" has only free shipping on.
  - Herning: 49 kr., free from 799 kr.
  - Up to 15 km from the shop: 79 kr., free from 999 kr.
  - 15–40 km from the shop: 129 kr., free from 1,299 kr.
  - Rest of Jutland: 219 kr. flat (no free threshold); time arranged with the customer.
  - Deliveries on Wednesdays and Fridays, Herning and up to 40 km between 15:00 and 17:00.
  - Pickup in the shop (Bethaniagade 5): free, ready about two hours after the order, subject to availability.
  WooCommerce zones work by postcode, not distance: either postcode lists for the 15 km and 40 km rings around
  the shop or a distance-based shipping plugin. We make the postcode lists ourselves (distance from Bethaniagade 5);
  the client does not need to check them (Matej, 2026-10-26). Jutland = postcodes 6000–9990 (Funen 5000–5999 and
  the islands are not Jutland). Outside Jutland only by arrangement: the site says "Live even further away? Get in
  touch and we will arrange it together." (Matej, 2026-10-27), so the checkout could say the same when no shipping
  method fits the address. Put the same rules on the Delivery & returns page with the client's texts. The site texts already
  show these prices (announcement bar, homepage feature and FAQ, delivery panel on About us and Contact).
- [ ] Pickup orders not collected within 3 days: cancel them (releases the card reservation); WooCommerce does not do
  this by itself, so the shop does it by hand or a small scheduled task.
- [ ] Pickup in the shop: when the order is ready, the customer gets an e-mail and an SMS or WhatsApp message
  (Matej, 2026-10-26). E-mail: an order status like "Ready for pickup" with its own customer e-mail, sent when the
  shop sets it. SMS/WhatsApp: decide whether the shop sends it by hand from its phone or an SMS service sends it
  automatically (extra cost); tell Matej which, so the checkout and Delivery & returns texts say the same.
- [ ] Put the client's texts on the legal pages; set the terms page in WooCommerce; remove the sample page `refund_returns`.
  Privacy Policy still shows WordPress's sample text ("Suggested text: …", comments, Gravatar); Terms and Delivery &
  returns say "[TODO] Indhold mangler". The pages already have their design (template Information page).
- [ ] Customer accounts (asked by Matej, 2026-10-23: My account has only "Log in", no way to create an account):
  decide and switch on *WooCommerce → Settings → Accounts & Privacy* → "Allow customers to create an account on the
  My account page" (and whether at checkout, and the password by e-mail link). The design is ready: the register
  form appears as a second card next to the login, in the same style (`assets/css/pages/my-account.css`).
- [ ] Marketing e-mails (Matej, 2026-10-27): the register form has an optional, unticked checkbox "Send me e-mails
  about deals and new products"; the date of the consent is saved in user meta `delicja_marketing_consent` (removed
  when the customer unticks it in Account details), the privacy checkbox in `delicja_privacy_accepted`
  (`inc/account.php`). Choose how the client sends the e-mails (newsletter tool) and sync or export these customers;
  every e-mail needs an unsubscribe link. Maybe the same checkbox at checkout. Customers who sign up with Google skip
  the form, so they can only opt in later in Account details.
- [ ] Log in with Google or Facebook on My account (asked by Matej, 2026-10-24). Needs a social login plugin and
  an OAuth app at Google and at Meta (client ID and secret, privacy policy link, consent screen); logins and keys
  are Robo's area. Only makes sense together with customer accounts above. If added, Matej styles the buttons in
  `my-account.css` to match the login card.

**Statistics**
- [ ] Microsoft Clarity (Matej sent Robo the project, 2026-10-27). Complianz's wizard takes only one statistics tool
  and Google Analytics is set there, so Clarity needs the manual Clarity Consent Mode snippet
  (https://complianz.io/manual-clarity-consent-mode/), only on delicja.dk like GA (`inc/analytics.php`). In Clarity:
  Settings → Setup → cookies off by default.
- [ ] After launch: check Google Analytics Realtime on delicja.dk (GA4 property "Delicja ApS", G-X3VJVFR9B7, set up
  in Complianz → Statistics; on the test site only with `?dl-analytics=1`). Run the Complianz cookie scan so the
  cookie policy lists the `_ga` cookies. Decide on shop events (product views, cart, purchases): the official
  "Google Analytics for WooCommerce" plugin. Search Console is Robo's (SEO).

**Deploy**
- [ ] Page cache after a deploy (asked by Matej, 2026-10-24): `inc/deploy.php` clears WP Fastest Cache on the first
  request that reaches PHP, but cached pages are served without PHP, so the homepage kept the old `site.js?ver=…`
  for a while (an iPhone still showed the old behaviour). Let the workflow trigger the purge itself after rsync
  (e.g. one request with a query string, or deleting the cache folder over SSH).

**Payment gateway (Frisbii Pay)**, when the client has done their part
- [ ] Keys, payment methods, logos, order status mapping; check webhooks. The client wants **Dankort, Visa/Mastercard,
  Apple Pay and Google Pay, no MobilePay** (2026-10-26). The footer icons and the homepage texts already show these.
- [ ] **Reserve at checkout, charge at handover** (client's question, 2026-10-27; Forbrugerombudsmanden: the card may be
  charged only when the goods are handed to a carrier or to the customer, also for click-and-collect). Frisbii Pay
  1.8.19 does this. Settings in *WooCommerce → Settings → Payments → Frisbii Pay*:
  - **Instant Settle: nothing selected.** Now "physical products" is selected on the test site, so orders are charged
    right away; this must be turned off.
  - Auto-settle: leave on ("Disable auto-settle" unticked): the plugin charges when the order status changes to
    Completed (`OrderFlow/OrderCapture.php`).
  - Status Frisbii Pay Authorised → **Processing**, Settled → **Completed** (now On hold / Processing; with
    "Settled = Processing" an order would be charged as soon as it is processing).
  - Flow: order → reserved, status Processing → the shop marks it **Completed** when the customer collects it or
    our driver delivers it (admin or the WooCommerce app; bulk "Mark complete" after a delivery round) → charged.
    Something missing: lower the order lines before Completed (only less than reserved can be charged). Cancelled →
    the plugin voids the reservation (required by the guidelines, 4.3).
  - An authorisation is normally valid about 7 days (Frisbii docs): Wednesday/Friday deliveries fit; pickup orders
    need a pickup deadline (client decides, e.g. 3 days), then cancel.
  - Ask Frisbii/Flatpay: Dankort, Apple Pay and Google Pay reservations work the same (card payments underneath)?
    After a lower capture, is the rest of the reservation released at once?
  - The terms must say: "the amount is reserved when you order and charged when you receive or collect the goods".
- [ ] Test payments on test.delicja.dk (test cards: order → Authorised; Completed → Settled; partial; cancel).
- [ ] Turn off cash on delivery.

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
- [ ] Turn off WooCommerce "Coming soon" mode.
- [ ] Order e-mails to the client's address `shop@delicja.dk` (an alias of `info@delicja.dk`, set up by the client
  2026-10-26; now `web@digistudio.sk` for testing). Check the sender address of the customer e-mails and that mail
  from the site is delivered (SPF/DKIM for delicja.dk).
- [ ] Do not delete the products now on the site: they are real (Matej, 2026-10-27). The client adds the rest
  herself, in Danish, as agreed in the contract.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] Rich Results Test on the front page and a product.
- [ ] robots.txt on delicja.dk: search engines and AI search crawlers (OAI-SearchBot, PerplexityBot, ClaudeBot,
  Google-Extended) allowed; check `/llms.txt` and the sitemap there. The test site stays `noindex`.
- [ ] Pages still marked "Indhold mangler" (om-os, terms, delivery): fill in or remove `om-os` (About us is `/about/`).
  Kontakt and Deals (`/tilbud/`) are done (Matej, 2026-10-23).

**SEO check of Matej's changes** (2026-10-23; SEO is Robo's area, so please review, keep or change)
- [ ] Changes made before the rule "SEO is Robo's" (backups `delicja_backup_rankmath_20261023-1309`,
  `delicja_backup_seo_pages_20261023-1310`): Rank Math meta title, description and share image for the front page,
  About us and Shop; excerpts for those pages; `noindex` on cart, checkout, my account and purchase summary;
  Rank Math's llms.txt module (`llms-txt`) with extra shop facts.
- [ ] Front page meta description has Polish words (kabanosy, pierogi, ptasie mleczko). Matej wants English names or
  an explanation in brackets, as on the site (see DESIGN.md → Copy).
- [ ] New front page H1 "Polish deli in the heart of Herning" and the contact page (`/kontakt/`, H1 "Come say hi",
  page title "Kontakt"): meta title and description, and whether the slug stays Danish.
- [ ] "Coming soon": shop, categories, tags, product search, product pages and the cart are open by code
  (`inc/woocommerce.php`, Matej asked that "Browse the shop" works); only the checkout stays closed. Fine for
  indexing once the mode is off?
- [ ] WP Fastest Cache: the whole cache is now cleared by code when a product is saved or a sale starts or ends
  (`inc/woocommerce.php`), so "Weekly deals" and the Deals menu item appear right away. Keep or narrow it?
- [ ] 404 page (H1 "This shelf is empty – sorry!") and the Information page template (eyebrow "Good to know") are new
  (Matej, 2026-10-24). Categories without products are now left out of the menus and the homepage tiles.
- [ ] New pages: Deals (`/tilbud/`, H1 "Deals while they last"; the menu item shows only while something is on sale)
  and My account (H1 "Hello witamy!"), cart H1 "Your cart": meta title and description, indexing of the Deals page
  while it is empty.
- [ ] Opening hours are on the site now (2026-10-26, from the Google Business Profile): Tue–Wed 11:00–17:00,
  Thu–Fri 11:00–17:30, Sat–Sun 10:00–16:00, Monday closed. `inc/schema.php` still lists them as unknown: add
  `openingHoursSpecification` to the store schema.
- [ ] About us lead shortened (2026-10-24): "A Polish deli in the centre of Herning, where you can taste home."
  The page's meta description or excerpt may still use the old, longer text.
- [ ] Share image of the front page (link previews in WhatsApp, Messenger, Facebook): Matej wants the shop front
  instead of the window photo (`delicja-about-shop-window.webp`), 2026-10-27. Prepared in the media library: media
  504, `delicja-share-shop-front.jpg`, 1200 × 630. Set it in Rank Math (front page → Social, Facebook and Twitter
  image). WhatsApp caches previews: test with a new link, e.g. `https://test.delicja.dk/?v=2`.
- [ ] Homepage hero picture changed (apples, farm milk; now `delicja-hero-table-apples-2.webp`, alt text updated).
  Rank Math's schema on the front page still lists the old picture `delicja-hero-table.webp` as its ImageObject
  (200 × 200), and the front page share image may be old too.
