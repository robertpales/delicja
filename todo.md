# To do

What is still to be done on Delicja, split by who does it. Finished items move to `progress.md`.

- **Client**: Delicja ApS (the owner). Matej asks for everything in this section and passes it on.
- **Matej**: design in Elementor, communication with the client.
- **Robo**: technical setup (plugins, WooCommerce, code, server).

---

## Client (Matej asks, then sends it to Robo)

**Page texts** (needed before launch; the payment provider checks these pages)
- [ ] Delivery and returns → page `delivery-and-returns`. (Terms and conditions and the privacy policy are final and
  on their pages, 2026-10-30.)
- [ ] Privacy policy, to decide later and then add to it: after how many years inactive customer accounts are deleted,
  the newsletter service, a courier if one delivers some orders.
- [ ] Confirm or correct the texts we wrote on the site: the four promises and the FAQ answers on the homepage,
  the About us story.
- [ ] Homepage hero picture: confirm the short product stories in it (e.g. the doughnut "filled with rose jam").
  (Apples and fresh milk: the shop sells them, 2026-10-26.)
- [ ] Customer accounts: should customers be able to create an account, and log in with Google or Facebook? (Robo
  switches it on.)

**Food rules** (required for selling food online in Denmark)
- [ ] Fødevarestyrelsen answered (2026-10-29): no problem, the client only adds online sale to the shop's existing food
  registration. To do before launch. (The link to the shop's inspection reports on findsmiley.dk is in the footer.)
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
  Polish, every product must be sent to translation by hand and the Danish allergens checked. WPML is set up so
  products written in Danish are translated into English and Polish automatically (2026-11-02). Easiest for her: copy
  the Danish label (sticker) on the pack. Matej, 2026-10-29: the Danish product
  information is solved later with an AI (LLM) plugin.
- [ ] When she adds a product, in the main language (Danish), from the pack or the supplier's data sheet: name, price
  incl. moms, weight or volume, category, photo, ingredients with allergens, nutrition, producer or importer, country
  of origin where required, and storage (keep chilled, frozen, or cool and dry),
  allergens it contains and may contain, vegetarian/vegan if stated. The product page shows them as icons.
  The products now on the site are real and stay (Matej, 2026-10-27); their allergens are filled in from the
  ingredient lists (to be checked), storage is still empty.

**Online presence** (Google, local search, AI answers)
- [ ] Manager access to the Google Business Profile, so opening hours, photos and the website link match the site.
- [ ] Other profiles to link besides Instagram @delicja.dk and TikTok @delicja.herning (Facebook, …).
- [ ] May we show their Google reviews on the site (real reviews only)?
- [ ] More real photos: the shop inside, shelves, the counter, people. People in photos must agree to it (GDPR);
  confirm this for the photos already used (Elżbieta, the customer at the counter).
- [ ] A sharp photo of Elżbieta for the About us opening (asked by Matej, 2026-10-27): now it is a blurry frame from
  the fair video. Best: in the shop by the shelves or the counter, landscape, daylight, the phone's main camera (not
  a screenshot from a video), looking at the camera.


**Payment gateway (Flatpay / Frisbii Pay)**
- [ ] Ask Flatpay to activate online payments (Frisbii Pay) for the website delicja.dk. The client has asked Flatpay
  for online payments and the API key and waits for their reply (2026-10-27; no answer yet on 2026-10-29, she contacts
  them again).
- [ ] Invite `pales.robo@gmail.com` as a user in the Frisbii (Billwerk+) admin. If that is not possible: send the Live Private Key and a test key or test account.
- [ ] Accounting: which system (e-conomic, Dinero, Billy, …)? Should orders go there automatically?

**For search and AI answers** (SEO audit 2026-11-05)
- [ ] Own short description (1–2 sentences: taste, use, producer) for each product instead of "Importeret fra Polen…",
  starting with the 10–20 best sellers; Danish ingredient text where it is only in Polish.
- [ ] Manager access to the Google Business Profile (opening hours, photos, website link) and may we show the real
  Google reviews on the site?
- [ ] Check name, address, phone and e-mail (shop@delicja.dk) on Krak, De Gule Sider and Facebook.

**Language versions**
- [ ] Before launch someone who reads English and Polish checks the English and Polish Terms and conditions and
  Privacy policy (translated 2026-11-02 from the client's Danish texts). The client checks the Danish product names (rule in DESIGN.md, Product names in three
  languages).

---

## Matej

- [ ] Homepage hero: the client chose the picture of real products (2026-10-31), so the hover stories are gone. Remove
  the unused code: the `?hero=` switch and the reveal for the hero (`site.js`, Hero) and the hero masks (`home.css`).
  Replace the generated pierogi in the Ready meals tile when the client sends a real photo (the hero has none now).
- [ ] Continue the design, but now unify and finish it rather than change it: give the site its final look,
  keep what is already done and avoid big changes.
  - [ ] Style the product page (template Single Product) in the site style.
  - [ ] Related product cards (template Related product card) in the same style as all the other product cards on the site.
- [ ] A short guide for the client on adding a product (which fields, allergens and storage icons, photo size,
  category), so every product page is complete.
- [ ] Delivery texts on the site (homepage FAQ, the "Not in Herning?" panel, Delivery & returns): minimum 499 kr. for
  delivery, delivery orders at least 24 hours before the delivery day (the client confirmed her terms are final,
  2026-10-29).
- [ ] Legal and shop information pages (Terms and conditions, Privacy policy, Delivery & returns, Cookies): put the
  client's texts on them when she sends them (Robo, 2026-10-27: Matej may edit these pages). Terms and Privacy policy
  done (2026-10-28, 2026-10-30); Delivery & returns still says "[TODO] Indhold mangler". It now exists in three
  languages (English 71, Danish 761 "Levering og retur", Polish 744): write the Danish text and translate it. The pages already have their design (template Information page). The terms page is
  already set in WooCommerce and the sample page `refund_returns` is deleted.
  - Delivery & returns must say the same as the checkout: Herning 49 kr. (free from 799 kr.), up to 15 km 79 kr.
    (free from 999 kr.), 15–40 km 129 kr. (free from 1,299 kr.), deliveries Wednesday and Friday 15:00–17:00; rest
    of Jutland 219 kr., time agreed with the customer; further away by arrangement. Pickup in the shop free, ready in
    about two hours, collect within 3 days the shop is open after the "ready" e-mail, otherwise the order is
    cancelled; the card is charged at pickup or delivery. Towns in each zone: WooCommerce → Settings → Shipping (zones by postcode). The checkout
    takes orders for the first delivery day at least 24 hours away (Tuesday 15:00 for Wednesday, Thursday 15:00 for
    Friday) and needs 499 kr. for delivery (Robo, 2026-11-02).
---

## Robo

**WooCommerce**
- [ ] Live site: a real server cron (or a scheduled ping of `wp-cron.php` every 15 minutes) so the hourly pickup check
  also runs when visitors only get cached pages. Check what Simply offers.
- [ ] Marketing e-mails (Matej, 2026-10-27): the register form has an optional, unticked checkbox "Send me e-mails
  about deals and new products"; the date of the consent is saved in user meta `delicja_marketing_consent` (removed
  when the customer unticks it in Account details), the privacy checkbox in `delicja_privacy_accepted`
  (`inc/account.php`). Choose how the client sends the e-mails (newsletter tool) and sync or export these customers;
  every e-mail needs an unsubscribe link. Maybe the same checkbox at checkout. Customers who sign up with Google skip
  the form, so they can only opt in later in Account details.

**Cookies and privacy** (found while writing the privacy policy draft, 2026-10-29)
- [ ] WooCommerce → Settings → Accounts & Privacy → Personal data retention is empty: set it when the client answers
  the privacy policy draft (inactive accounts after how many years).

**Statistics**
- [ ] After launch: check Google Analytics Realtime on delicja.dk (GA4 property "Delicja ApS", G-X3VJVFR9B7, set up
  in Complianz → Statistics; on the test site only with `?dl-analytics=1`). Run the Complianz cookie scan so the
  cookie policy lists the `_ga` and Clarity (`_clck`, `_clsk`) cookies. Decide on shop events (product views, cart, purchases): the official
  "Google Analytics for WooCommerce" plugin. Search Console is Robo's (SEO).

**Hosting: 508 Resource Limit Is Reached** (Matej, 2026-11-04; not changed by Matej, hosting is Robo's)
- [ ] The site sometimes shows Simply.com's "508 Resource Limit Is Reached" (seen in the WooCommerce app at login,
  2026-11-04 09:19): the plan's limit of concurrent PHP processes is reached. Simply.com: raise it by upgrading to a
  plan with more PHP threads (Control Panel → Website: resource usage, also "restart PHP processes" as a quick fix).
  Checked 2026-11-04 (host linux392.unoeuro.com, PHP 8.5, OPcache on, no object cache, WP-Cron on page loads,
  Action Scheduler healthy: 0 overdue, 17 pending; 17 plugins; autoload 456 kB). Likely causes: bursts of uncached
  PHP requests (the app's REST calls at login, wp-admin with WPML ATE and Heartbeat polling, WP-Cron). Decide:
  plan upgrade (client pays), real server cron with DISABLE_WP_CRON (see WooCommerce above), object cache if Simply
  offers Redis, Heartbeat throttling.

**Deploy**
- [ ] Page cache after a deploy (asked by Matej, 2026-10-24): `inc/deploy.php` clears WP Fastest Cache on the first
  request that reaches PHP, but cached pages are served without PHP, so the homepage kept the old `site.js?ver=…`
  for a while (an iPhone still showed the old behaviour). Let the workflow trigger the purge itself after rsync
  (e.g. one request with a query string, or deleting the cache folder over SSH).

**Payment gateway (Frisbii Pay)**, when the client has done their part
- [ ] Delete the WordPress user `flatpay` (Shop manager, for Flatpay's setup check) when they are done; it stops
  working on 17 November 2026 anyway (`inc/access.php`).
- [ ] Keys, payment methods, logos, order status mapping; check webhooks. The client wants **Dankort, Visa/Mastercard,
  Apple Pay and Google Pay, no MobilePay** (2026-10-26). The footer icons and the homepage texts already show these.
- [ ] Test payments on test.delicja.dk (test cards: order → Authorised; Completed → Settled; partial; cancel).
  Stock is the shop's matter (Matej, 2026-10-29): when an ordered product is missing, they refund it or call the
  customer, so check that the shop can charge less than the reserved amount or refund one item.

**Language versions (WPML)**

Main language: **Danish** (the client's choice, 2026-10-26), no prefix; English `/en/`, Polish `/pl/`. Pages and
templates are translated by hand (no credits). Products, categories and attributes are translated automatically when saved,
including slugs and Rank Math meta title and description; the client only writes in the main language.
Prices, stock and images are shared.

Set up 2026-11-02 (see progress.md). Pages and templates are English originals with Danish and Polish translations
(Terms and Privacy policy: Danish originals); a change on a page must be made in each language.

- [ ] Product slugs after the new product names (Matej, 2026-11-02; slugs not changed, they are Robo's): the Danish
  slugs still follow the old automatic names (e.g. `twarog-fed-landhytteost-250-g`) and the English ones are the
  Polish names. Decide whether to update them (with redirects) before launch.
- [ ] Live search (`inc/search.php`): Store API results in the current language (check with WPML; add `lang` to the
  endpoint if needed).
- [ ] `inc/schema.php`: language, URLs and breadcrumbs from the current language.
- [ ] Rank Math per language: meta, `hreflang`, sitemaps. WooCommerce e-mails in the customer's language. Complianz banner in all languages.
- [ ] Test: an order in each language. (A product written in Danish gets its English and Polish versions
  automatically, checked 2026-11-02.)

**SEO audit 2026-11-05** (Matej ran the claude-seo audit on delicja.dk and test.delicja.dk; read-only, nothing
changed; score old site about 34/100, new site about 64/100 with the staging noindex ignored)
- [ ] Meta titles and descriptions in Danish/Polish on DA and PL pages (still English: Om os, Kontakt, Butik, Tilbud,
  Privatlivspolitik, and the Polish counterparts); category pages without description (e.g. Færdigretter).
- [ ] No `<link rel=canonical>` and no hreflang `<link>` tags in the head on any page (only the switcher links). Check
  whether Rank Math leaves them out because of noindex; they must be there on delicja.dk.
- [ ] Product schema: add `brand` and GTIN (`gtin13`) where known; Organization logo as PNG/WebP (SVG is not
  supported by Google); localise `additionalProperty` names on Danish products; later shippingDetails and
  hasMerchantReturnPolicy from the real terms.
- [ ] Product sitemap still lists a deleted demo product (`/pl/krakus-zurek-zupa-na-zakwasie-500-ml/`, 404): refresh
  the Rank Math sitemap cache. /pl/ home links to `/pl/snacks/` (untranslated slug next to `/pl/przekaski/`).
- [ ] robots.txt: explicit rules for AI crawlers; llms.txt without /en/ and /pl/ pages and with thin product lines.
- [ ] Speed: TTFB 2–5 s on test, `cache-control: no-cache, no-store`, home HTML 368 kB, 23 scripts; one 503 (see 508
  above). Page cache, object cache, Lighthouse on mobile before launch.
- [ ] Security headers missing (HSTS, CSP, X-Frame-Options, Referrer-Policy); `/wp-json/wp/v2/users` is public and
  shows the admin user slug.
- [ ] Old delicja.dk at launch: it has no products, but indexed demo pages with fake details (`/pl/contact-us/`
  "221B Baker St, London", `/pl/our-story/`, `/pl/our-bread/`, `/pl/workshops/`, sample-page, global-styles,
  posts-page, a demo post). Redirect map (301, no chains): `/da/` → `/`, `/pl/` → `/pl/`, `/en/` → `/en/`,
  `/pl/shop/` and `/shop/` → shop, `/pl/contact-us/` → contact, `/pl/our-story/` → about, cart/checkout/my-account →
  the new ones, the rest → home. E-mail: the old site shows info@delicja.dk, the new one shop@ (forwarding above).
- [ ] Reviews: no review source anywhere; link the Google Business Profile (real reviews only).

**Before launch**
- [ ] Delete the test customer Anna Nielsen (`test-kunde@example.com`, user meta `delicja_test_data`) with her 4 test
  orders (#544–#547, order meta `_delicja_test_data`) and her saved test card. Made by Matej on 2026-10-31 to design
  My account; no e-mails were sent.
- [ ] Do not delete the products now on the site: they are real (Matej, 2026-10-27). The client adds the rest
  herself, in Danish, as agreed in the contract.
- [ ] Delete old backups (`delicja_backup_*` options).
- [ ] After the move to delicja.dk: check that WP Fastest Cache → Exclude still has the three WooCommerce cookies
  and that `.htaccess` has them in "Start WPFC Exclude" (`inc/cache.php`); repeat the guest test (a second visitor
  must not see the first one's cart or address).
- [ ] Rich Results Test on the front page and a product.
- [ ] robots.txt on delicja.dk: search engines and AI search crawlers (OAI-SearchBot, PerplexityBot, ClaudeBot,
  Google-Extended) allowed; check `/llms.txt` and the sitemap there. The test site stays `noindex`.
- [ ] Page still marked "Indhold mangler": Delivery & returns (Matej, see above).
- [ ] Mail forwarding info@delicja.dk → shop@delicja.dk in Simply.com (the site only shows shop@ since 2026-11-02).
- [ ] WPML on delicja.dk: register the production site key (`afd199d9ee`, site `https://delicja.dk` on wpml.org),
  delete the development key of test.delicja.dk and move its remaining automatic-translation words to the live site.

**SEO with the main language**
- [ ] Meta titles and descriptions in Danish: the front page (Forside) is done; Om os, Kontakt and Tilbud still have
  the English texts on their Danish versions, Butik has none; Polish versions the same. Categories and products are
  translated automatically. llms.txt in Danish.
- [ ] Share image of the front page (link previews in WhatsApp, Messenger, Facebook): Matej wants the shop front
  instead of the window photo (`delicja-about-shop-window.webp`), 2026-10-27. Prepared in the media library: media
  504, `delicja-share-shop-front.jpg`, 1200 × 630. Set it in Rank Math (front page → Social, Facebook and Twitter
  image). WhatsApp caches previews: test with a new link, e.g. `https://test.delicja.dk/?v=2`.
