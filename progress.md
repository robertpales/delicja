# Progress

What was done on the Delicja test site (test.delicja.dk), by date and by person.

- **Robo**: Róbert Páles. Commits as `robertpales`; also changes made in the database through Novamira.
- **Matej**: Matej's Claude. Commits as `Claude` (branches `claude/...`, merged into `main` automatically).

Code changes are in the git history. Changes marked *(database)* were made in WordPress/Elementor
through Novamira, so they are not in git.

---

## 2026-10-27

**Robo**
- Shipping by the client's prices: zones by postcode and road distance from the shop (Herning 49 kr., up to 15 km
  79 kr., 15–40 km 129 kr., rest of Jutland 219 kr.; free from 799 / 999 / 1,299 kr.), pickup in the shop in every
  zone, the old "free shipping for all of Denmark" removed. With free delivery the paid one is hidden; outside the
  delivery area only pickup and a note to get in touch (`inc/shipping.php`). *(database: zones)*
- Checked: 25 % moms applies to all of Denmark (no city limit).
- Pickup orders: new order status "Ready for pickup" (also as a bulk action and a button in the order list) with an
  e-mail to the customer: collect by the end of the third day the shop is open, opening hours, amount charged at
  pickup. Reminder after the second day; after the third the order is cancelled automatically (the card reservation is
  released, the customer gets the cancelled e-mail with the reason). Pickup orders not marked ready within a day: one
  e-mail to the shop. Checked with test orders (`inc/pickup.php`).
- WhatsApp message "ready for pickup" from the shop's own WhatsApp (free, no plugin): a button in the order list and
  in the order opens WhatsApp with the message and collect-by date filled in; the link is also added as an order note,
  so it can be used from the WooCommerce mobile app.
- Search results in the shop layout: the same product cards, grid and sorting as the categories (before: plain cards
  in three columns); nothing found → a short note, "See the whole shop" and the category tiles. *(database: template
  "Product search results")*
- Live search: results with picture, name and price appear under the header search field while typing (WooCommerce
  Store API, no plugin), keyboard and screen reader friendly; "See all results" opens the results page
  (`inc/search.php`, `site.js`).
- Deals page slug is now `deals`, Contact `contact` (also Delivery & returns: `delivery-and-returns`). The page CSS, the Deals menu
  item and its noindex no longer depend on the Danish slug; page CSS uses the English slug (`deals.css`, `contact.css`); schema finds Contact the same way.
- Privacy fix: a cached page showed a guest's name, address, e-mail, phone and cart to every visitor (the mini cart
  block prints them into the page and WP Fastest Cache stored it). Now visitors with a WooCommerce session get no
  cached pages and create none (WPFC Exclude cookies *(database, .htaccess)* and `DONOTCACHEPAGE`), and the addresses
  are no longer printed at all (`inc/cache.php`). Checked in a browser with a test guest.
- Microsoft Clarity (project z4by01ybij): loads only after the visitor allows statistics, with Consent Mode v2;
  cookies erased when the consent is revoked; only on delicja.dk (test site with `?dl-analytics=1`).
- Google Analytics did not load after consent: Complianz renamed the held-back gtag.js tag a second time
  (`data-cmplz-data-cmplz-src`). Fixed; checked in a browser: nothing before consent or after Deny, GA and Clarity
  after Accept (`inc/analytics.php`).
- SEO check of Matej's changes: kept the meta, share images, `noindex` on cart/checkout/account/summary, open shop
  pages in Coming soon mode, the full cache clear on product changes, 404 and Information page. Changed: front page
  description without Polish words (pierogi explained), About us description from the new lead, meta title and
  description for Contact and Deals (slug `/kontakt/` stays: Danish is the main language). *(database; backup
  `delicja_backup_seo_20261027`)*
- Schema: opening hours (`openingHoursSpecification`, from the same list as the pickup e-mails); Rank Math's stale
  front page image (old hero, 200 × 200) dropped. Deals page `noindex` while nothing is on sale (`inc/seo.php`).
- llms.txt: opening hours, delivery prices and zones, pickup, payment methods. *(database)*
- The old block "Pickup at store" switched off: the checkout is the classic one, pickup is a shipping method in each
  zone. *(database)*

**Matej**
- Share image with the shop front (1200 × 630, media 504) prepared for link previews; setting it in Rank Math is Robo's.
  *(database: media)*
- Footer logo: the red rounded square with the "D" (as in the browser tab) instead of the round badge, a bit smaller
  (80 px instead of 112). *(database: footer; size in git)*
- Google Analytics 4 (property "Delicja ApS", G-X3VJVFR9B7) set up in Complianz → Statistics: it starts only after
  the visitor allows statistics in the cookie banner (nothing is loaded from Google before), and it collects only on
  delicja.dk; on the test site only with `?dl-analytics=1` for checking. Checked: no Google requests before consent
  or after "Deny"; after "Accept" the page view reaches GA. Clarity is Robo's. *(database: Complianz; gate in git)*
- Client's answers (via Matej): no alcohol online; launch as soon as the site is finished (no fixed date); we manage
  the domain delicja.dk and have access; the client sends the legal texts herself; the products now on the site are
  real and stay. Proposed pickup deadline: 3 days.
- Pant (Danish deposit) for drinks in cans and bottles: a "Pant" select on the product (A 1 kr., B 1,50 kr., C 3 kr.;
  also in bulk edit). The price stays without pant, "+ pant …" shows under it, and the cart adds one "Pant" line
  (with moms, outside the free-delivery subtotal) that goes into the order, e-mails and invoice.
- My account: login and registration in one card instead of two side by side. A switch "Log in / Register" on top
  shows one form at a time, and "Continue with Google" is there only once, under both. After a failed registration
  the register form stays open.
- Register form: two checkboxes instead of the long privacy text: "I have read the privacy policy" (required) and
  "Send me e-mails about deals and new products" (optional). Both are saved with the date; customers can change the
  e-mail consent in Account details. The newsletter tool itself is Robo's.
- About us → How it started: the new Instagram video, Elżbieta showing the shop a year after opening, instead of the
  fair video (made playable on iPhones, with a poster from the video); caption and the "Watch the reel on Instagram"
  link changed to it. *(database)*
- Delivery texts: "Live even further away? Get in touch and we will arrange it together." in the "Not in Herning?"
  panel (About us, Contact) and in the homepage delivery FAQ (with e-mail and phone). *(database)*
- Browser tab icon: instead of the round badge (unreadable at 16 px), the "D" from the Delicja script logo in white
  on a red rounded square; also the icon on phone home screens. *(database: Site Icon; source in git)*
- No sharper photo of Elżbieta for the About us opening (the video frames are blurry or mid-word); asked the client
  for one (`todo.md`).

## 2026-10-26

**Matej**
- Client questions settled by Matej: the shop sells apples and fresh milk (hero picture stays), the opening hours on
  the site are right, keeping chilled goods cold on deliveries is the shop's own matter, the delivery postcode lists
  are made by us without the client, and a customer is told by e-mail and SMS or WhatsApp that a pickup order is
  ready (to Robo).
- Footer: link to the shop's food inspection reports on findsmiley.dk, under the opening hours on every page;
  required for a food webshop. It is Fødevarestyrelsen's official "Inspection Report" badge (the magnifier with the
  smileys), as their guide recommends; the Danish "Se kontrolrapport" badge is in the media library for the Danish
  version. Checked that the page is Delicja's (CVR 45266575). Rounded corners like the payment icons.
  *(database; size in git)*
- Opening hours on the site instead of "see Google Maps": Tue–Wed 11:00–17:00, Thu–Fri 11:00–17:30, Sat–Sun
  10:00–16:00, Monday closed (from the Google profile). As a small list next to the clock icon on the Contact card,
  About us → Visit us and in the footer, and in the homepage FAQ answer. *(partly database)*
- Client's orders e-mail: `shop@delicja.dk` (an alias of `info@delicja.dk`); noted for Robo, who switches the order
  e-mails to it before launch.
- Client's delivery terms on the site: announcement bar "Free delivery in Herning from 799 kr.", homepage feature
  "Delivered to your door" and the FAQ answer with all prices (Herning 49 kr., up to 15 km 79 kr., up to 40 km
  129 kr., rest of Jutland 219 kr., free from 799 / 999 / 1,299 kr.; Wednesdays and Fridays 15:00–17:00; pickup ready
  in about two hours), and the "Not in Herning?" panel on About us and Contact. Shipping in the checkout is Robo's.
  *(database)*
- Client's answers: online payment methods Dankort, Visa/Mastercard, Apple Pay and Google Pay (no MobilePay); main
  language Danish. Footer payment icons now Dankort, Visa, Mastercard, Apple Pay, Google Pay (official logos from the
  Frisbii plugin), and the homepage "Pay your way" and FAQ texts name these methods. *(database)*
- Shop sorting made to fit the site: instead of the browser's select box, a white pill "Sort: Our picks" that opens a
  small list (Our picks, Most popular, Newest, Price: low to high, Price: high to low) with a red tick on the current
  one; also in product search. Sorting by price and by popularity did not work before (the shop grid fell back to
  newest first); fixed.

## 2026-10-25

**Matej**
- Language switcher prepared in the header: three little handwritten greetings "Hej! · Hi! · Cześć!" next to the
  account and cart icons (on phones at the top of the open menu). It does nothing yet; once WPML is set up the
  greetings link to the Danish, English and Polish pages, the main language first. *(partly database)*
- Language switcher: each greeting has its flag as a small round sticker (Denmark, United Kingdom, Poland).
- Language switcher made quiet: instead of the handwritten greetings, a small grey pill with the flag and "EN" that
  opens a small list Dansk · English · Polski with flags.
- Header icons tidied: language, account and cart sit on one line, all the same height and dark colour, with the
  same white round hover; the cart's item count sits on the corner of the cart icon.
- About us: the title uses the same lettering as the homepage and contact page ("A taste of home" in Anton,
  "made from the heart" handwritten), and "How it started" sits on a linen band with soft waves, so the move from
  the first part to the second is smooth and clearly separated. *(partly database)*
- About us: all two-column parts use the same grid (two equal columns, same gap), so texts and pictures start on the
  same lines in every section.
- About us and Contact: the opening texts start at the same height on both pages, with the same title size and
  spacing; the About photo is a little lower (5:4), so text and photo end together.
- About us, "Visit us": the pale pink "Not in Herning?" box under the photo is now the same red delivery panel as on
  Contact (full width, with the opened box of groceries), and the text and shop window photo sit side by side, centred.
  The panel is one shared piece for both pages. *(partly database)*

## 2026-10-24

**Matej**
- Homepage hero: three red apples are now the centre of the board (on hover the front one is cut in half), and the
  milk is an unbranded farm-style glass bottle with a paper cap and twine. Stories "Apples" and "Fresh milk".
  *(partly database)*
- Homepage hero: the knife no longer lies on the kabanosy pointing at the pickle jar; it lies on the free board
  between the apples and the kabanosy (in both pictures, so it stays put on hover). *(database)*
- Homepage hero: on hover the front apple stays whole and a wedge is cut out of it (the wedge lies on the board
  beside it, just behind the knife), instead of the apple cut in half, which did not look natural. *(partly database)*
- Hero and contact shop front on iPhone: a tap now shows the change (bite, cut, open jar…), not only the story.
  Safari reports a tap as a mouse click, so the change was skipped.
- Homepage hero on phones: the tap marks on the products are now tiny soft white glints that slowly breathe, instead
  of red dots with a white ring.
- Category tiles: the first one (Meat) is no longer red on its own, which looked like it was selected; a tile turns
  red only under the mouse.
- Shop page: shows all products right away, with the categories as small tiles in one row above them (also on
  category pages, where the category being viewed is red; on phones the row scrolls sideways). The big category
  tiles template "Shop – categories" is no longer used. *(partly database)*
- Homepage on phones: "Customer favourites" and "New arrivals" have the same small scroll bar under the row as the
  categories, so it is clear they scroll sideways.
- About us: the lead under "A taste of home, made from the heart" is one short sentence now ("A Polish deli in the
  centre of Herning, where you can taste home."); the longer text repeated the "How it started" part. *(database)*
- About us on phones, "How it started": the three moments sit in a light card as a small timeline (icons in white
  circles joined by a dashed red line), and the two Instagram buttons are full width and equal.
- Homepage hero without bread (Delicja does not sell bread): a ring of Olewnik kiełbasa podwawelska is the centre of
  the board (on hover a few thick slices are cut off) and a bottle of Łaciate milk with a glass stands by the
  doughnut (on hover the glass is half drunk, the doughnut gets a bite). "Bread" is gone from the hero line, the
  "Genuine Polish products" promise and About us. *(partly database)*
- Fixes found in the site check: the FAQ's "terms and conditions" link pointed to a missing page; the homepage
  category chips and "Shop sweets" went through redirects; the terms page was hidden behind "Almost ready";
  "See all new arrivals" showed the category tiles instead of the newest products. *(partly database)*
- No bread anywhere else either: the wooden crate (New arrivals, empty cart) and the delivery box (Contact, "Almost
  ready") now hold a ring of kiełbasa, and the Bakery tile shows doughnuts with jam and cheesecake. *(database)*
- Information pages (Terms, Privacy, Delivery & returns, Cookies): own template "Information page" with a big
  title, readable text and a linen side card with the Information menu and "Questions? Call or write". *(partly
  database)*
- 404 page: "This shelf is empty – sorry!" with product search, "Browse the shop" and the empty crate that fills up
  when you point at it (the same crate as the empty cart, now one shared piece). *(partly database)*
- The header cart icon shows the number of items for visitors too (while ordering is not open yet it is a plain
  link to the cart).
- Empty categories (Bakery, Drinks, Spices, Cosmetics have no products yet) are left out of the menus and the
  homepage tiles until they get products; two homepage chips now lead to Ready meals and Pantry. *(partly database)*

## 2026-10-25

**Robo**
- Google login (Nextend Social Login, free): Google app in Robo's Google Cloud, plugin set up, customer registration on. *(database)*
- "Continue with Google" buttons under the login and registration forms in My account and at the top of the checkout
  (`inc/social-login.php`), styled like the site's fields. Facebook follows after Meta's business verification.

## 2026-10-23

**Matej**
- Category tiles: a click anywhere on the tile, also on the picture, opens the category (before, only the name was a
  link).
- "Browse the shop" works for visitors: the shop, categories, tags, product search and the cart are open while the
  store is in Coming soon mode; only checkout (and My account) wait until it is switched off. The header cart shows
  for visitors too.
- My account in the shop's style: "Hello" with a handwritten "witamy!", a polaroid of Elżbieta ("Elżbieta says
  hi!") and the login form on a linen card with a strip of tape: soft white fields, a full-width red "Log in",
  "Lost your password?" below. Lost password, the register form and the account pages (side menu, orders,
  addresses) got the same look. Creating an account is still off: that is Robo's decision (in `todo.md`).
  *(partly database)*
- Checkout while ordering is not open yet: the "Almost ready" screen now says that ordering online opens soon and
  leads back to the cart. *(database)*
- Cart page in the shop's style: big "Your cart" with a handwritten "good choices!", product pictures on linen tiles
  that tilt on hover, round − and + buttons (the cart updates by itself, no "Update cart" button), the coupon as a
  ticket, and the order summary as a paper receipt with a zigzag edge and a handwritten "Dziękujemy – thank you!".
  While online ordering is not open yet, a short note says so. An empty cart shows an empty wooden crate with
  straw that fills up with groceries when you point at it, and a "Browse the shop" button. *(partly database)*
- New Deals page (`/tilbud/`): big "Deals" with a handwritten "while they last" and all products on sale; with no
  sale it says so and points to the shop. The "Deals" menu item shows only while at least one product is on sale,
  and the page cache is cleared when sales start or end. The red "Sunday" promo's button now leads to ready meals
  instead of the empty deals. *(partly database)*
- Contact shop front works like the homepage hero: each spot really changes the photo and means a way to Delicja.
  The shelves fill up (see the shop), the door opens with a doormat (directions), coffee and cake appear in the
  window with rising steam (our story), a delivery bike rolls in (shop online). Small pulsing dots show where to
  look. The hero and the contact page share one "Picture reveal" code. *(partly database)*
- Shop, categories, cart and checkout no longer show WooCommerce's grey "Great things are on the horizon" while the
  shop is in Coming soon mode, but Delicja's own "Almost ready, we're packing the first boxes" page (Elementor
  template, editable). *(partly database)*
- "Added to your cart" message is now a small card: the product's picture with a red check, a handwritten "Added to
  your cart", the product name and a red "View cart" button; it slides in, without the thick black frame.
- Contact page: the drawing is replaced by a realistic photo of the real shop front, made from Google Street View
  (Bethaniagade 5, May 2025) and the window logo photo. With the mouse on it the shop lights up as in the evening,
  and the shelves, the door and the window tell a one-line story. The contact cards are clickable as a whole and
  each icon moves its own way on hover (pin drops, phone rings, letter flies, clock turns, camera flashes).
  *(partly database)*
- Product facts: the full nutrition table moved from the Description tab under the nutrition tiles, so it is on the
  page only once.
- Homepage promo panels show real products from the shop instead of generated food: Krakus Żurek, Pierogi ruskie
  and Olewnik Myśliwska; E. Wedel Ptasie Mleczko, Wawel Krówka and Michałki (the shop's own product photos). They
  lift a little on hover. *(partly database)*
- Product pages: "Product facts" with hand-drawn icons under SKU and category: net quantity, From Poland, storage,
  diet, "Contains" and "May contain" allergens, and nutrition tiles per 100 g with % of the daily reference intake,
  read from the nutrition table in the description. Works the same for every product; the owner picks the
  attributes per product. New attributes Origin, Storage, Diet, Allergens, May contain; allergens of the test
  products filled in from their ingredient lists. *(partly database)*
- About us chapters: instead of one wavy line, a red-and-white baker's twine runs zigzag from photo to photo and
  draws itself while scrolling; the number stamps press in as each chapter appears.
- Header no longer generic: linen background like a tablecloth with a red cross-stitch band along the bottom, a
  stitched line between the rows, hand-drawn red underline in the menu, "Come say hi!" by the address, the Shop
  dropdown shows the category pictures, and the search says "Search for sausages, sweets…". *(partly database)*
- Header: the cross-stitch band and the stitched line are gone again (too much); the header is calm and clean,
  the red handwritten details and the category pictures give it character.
- New arrivals tile and the contact page's "Not in Herning?" panel: real-looking photos (a wooden crate of groceries,
  an opened delivery box) at the bottom of the red panel instead of the cartoon bag; the bag drawing and its two
  colours are removed. *(partly database)*
- "Follow Delicja": the two photos are brighter (also on About us) and look like Instagram posts (logo, delicja.dk,
  icons, caption); a click opens Instagram. *(partly database)*
- Product cards: on hover the product is lifted and tilted a little, as if picked up from the shelf (no zoom).
- Homepage hero: one styled table with all the products (bread and kabanosy on a board, doughnut on its own plate,
  pickles with dill, pierogi on an enamel plate). Nothing moves until the mouse is on a product: it tells a one-line
  story and changes (a bite from the doughnut, a slice of bread, an opened jar, a cut dumpling, a snapped kabanos).
  Three versions to compare: `/?hero=stories`, `/?hero=bites`, `/` both. *(partly database)*
- Hero: the changed state now shows whole things lying next to a product too (the jar lid, the cut slice of bread,
  the snapped kabanos).
- Homepage hero: the products got a setting. A round wooden board on linen with red Polish folk embroidery runs off
  the bottom of the red panel like a table; bread and pierogi lie on it, the other products float above it with soft
  shadows on the board. *(partly database)*
- Homepage hero: instead of the cartoon bag, real-looking products (rye bread, dill pickles, a doughnut, kabanosy,
  pierogi, dill) that pop in one by one, float slowly and follow the mouse a little. The bag stays in the New
  arrivals tile and on the contact page. *(partly database)*
- Small delivery van icon in the red bar "Order online, we deliver to your door".
- While the fonts load, text no longer flashes in a serif font (buttons, handwritten lines); the handwriting font is
  preloaded on the homepage like Anton.
- Contact page (`/kontakt/`): "Come say hi" with a hand-drawn picture of the shop front, buttons for directions and
  calling, cards for address, phone, e-mail, opening hours (Google Maps), Instagram and company details, and a red
  "Not in Herning? Order online" panel with the Delicja bag. The Page template no longer applies to it. *(database)*
- About us, "It all starts with a taste": the four chapters are joined by a hand-drawn red line with round number
  stamps on it; photos in white frames with tape, slightly tilted (straighten on hover).
- Homepage hero: "Polish deli in the heart of Herning" in Anton with a handwritten line and a hand-drawn underline,
  a short lead, and an animated drawing of a Delicja shopping bag (hops, the sausage jumps out, it blinks).
  New global colours for the drawing: Bread crust, Dill green. *(partly database)*
- New arrivals tile: same height as the product cards, paper grain, the Delicja bag peeks out and hops on hover.
- Copy without unexplained Polish words: English names or an explanation in brackets (doughnuts, dumplings,
  sour rye soup, "Come say hi!"); the story speaks to people from all over the world, not only Poles and Danes;
  "Customer favourites"; the follow box says "photos from Delicja"; shorter "What you will find" on About us;
  image alt texts the same way. *(database)*
- Product pages open for everyone even while the shop is in WooCommerce "Coming soon" mode (shop, categories,
  cart and checkout stay hidden until the mode is switched off).
- Homepage "Weekly deals" only shows while at least one product has a sale price; it appears by itself.
- Category images: 12 new cut-outs that look like real photos (natural light and textures, no glossy AI look) with
  alt texts; the category tiles keep their design, and the products are shown whole (no longer cut off at the
  bottom). Older images stay in the media library. *(database)*
- Homepage feels more human: new section "A little Polish shop on Bethaniagade" with real photos of the shop and
  Elżbieta as polaroids, handwritten "Zapraszamy!" (self-hosted Caveat font) and a hand-drawn underline; the
  "Follow Delicja" box shows real photos instead of generated cut-outs. *(partly database)*
- SEO/GEO: front page, About us and Shop got a real meta title/description and share image (the front page showed
  a placeholder text); cart, checkout, my account and purchase summary are `noindex`; Rank Math's llms.txt module is on
  (`/llms.txt`: pages, products, categories, shop facts for AI answer engines). *(database)* Anton is preloaded on the
  homepage so the hero headline shows sooner.
- `todo.md`: questions for the client reviewed and completed (food rules: smiley, product information, alcohol,
  pant; delivery of chilled food, pickup, order e-mail; product list and stock; Google Business Profile, reviews,
  photo consent; launch; payment methods and accounting).

**Robo**
- Schema.org structured data (`inc/schema.php`): company (GroceryStore + OnlineStore with CVR, VAT, address, GPS),
  website with product search, pages, products, shop and categories. Rank Math and WooCommerce duplicates switched off.
  Unknown data (opening hours, delivery, returns, brands) is left out and listed in the file header.
- Category URLs without `product-category` (e.g. `/dairy/`) returned 404; rewrite rules refreshed. *(database)*
- Shop page shows all categories as tiles (new template "Shop – categories"); category pages keep the product grid.
- Main menu: categories moved under **Shop**, the separate "Categories" item removed. *(database)*
- Order e-mails (new, cancelled, failed) go to web@digistudio.sk while testing; sender is info@delicja.dk. *(database)*
- `CLAUDE.md`: keep this file updated after every change.
- Purchase summary (order received page): site fonts and colours, thank-you box with a check icon, order overview, order table and address cards in the site style.
- `todo.md`: plan for the language versions (WPML: DA, EN, PL).
- `todo.md`: legal page texts needed from the client.
- `todo.md` split by who does it (client via Matej, Matej, Robo); the open list moved there from this file.
- `todo.md`: the client chooses the main language; order of work for the language versions (client → Matej → Robo → Matej).
- `todo.md`: Matej finishes and unifies the design (product page, related product cards).
- `CLAUDE.md` and README: SEO/GEO/AEO, security and UX changes after the design are Robo's areas, not Matej's.

## 2026-10-22

**Robo**
- Cart and checkout widgets styled like the mini cart: fonts, colours, fields, buttons, card rows on phones.
- Gap between the cart/checkout columns on desktop.
- Checkout header: logo instead of the site title, divider line, shadow and more padding. *(partly database)*
- Footer: "Delicja ApS · CVR 45266575" in the contact column. *(database)*
- Cookie banner (Complianz): short texts, buttons and colours set. *(database)*

## 2026-10-21

**Matej**
- Unused folk ornaments removed; DESIGN.md: the Elementor site is now the reference (block theme removed).
- Product card: quantity stepper after adding to cart, clearer View cart link.
- Product page: tags next to SKU and category.
- Homepage hero: Anton headline (self-hosted), light pink shop button, Visit us link.
- Category tiles like the approved reference, with product count (`[dl_cat_count]`).

**Robo**
- WooCommerce checked: Danish VAT 25 % (moms), prices incl. VAT, pickup in store, cash on delivery. *(database)*
- WP Mail SMTP: must use `websmtp.simply.com`. *(database)*
- Company data checked in the CVR register.

## 2026-10-20

**Matej**
- Header rebuilt: product search, account and mini cart (from WordPress/WooCommerce blocks), mobile menu.
- Footer columns and logo; mini cart drawer.
- Product card shortcodes: add-to-cart button, tag badges, empty Weekly deals message.
- Front page in Elementor: hero, category tiles, product rows, promos, promises, FAQ, follow box.
- Shop, category and tag pages: breadcrumbs, title, result count, sorting, product grid, pagination, empty state.
- About us page, product page, page/cart/checkout templates, checkout header, search results and 404 page.
- DESIGN.md and README updated to describe the Elementor site.

**Robo**
- Header search: button centred, no clipped focus ring.
- FAQ: centred plus/minus icon; the accordion no longer jumps when it opens.

## 2026-10-16

**Robo**
- Auto-merge: `claude/**` branches are merged into `main` and deployed to test automatically.

## 2026-10-15

**Matej**
- Fonts: Plus Jakarta Sans replaces Poltawski Nowy and Lato.
- Base CSS: design tokens, focus outline, button variants.

## 2026-10-06

**Robo**
- Child theme `delicja-child` created: structure, fonts, brand assets, docs, automatic deploy to test.
- Matej's block-theme design moved into Elementor: header, footer, front page, product card and product rows.
- Test products created. *(database)*
- Docs for Matej: getting started, Theme Builder for header/footer, menus in Appearance → Menus.

---

## Open

See `todo.md`.
