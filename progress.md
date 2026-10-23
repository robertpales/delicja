# Progress

What was done on the Delicja test site (test.delicja.dk), by date and by person.

- **Robo**: Róbert Páles. Commits as `robertpales`; also changes made in the database through Novamira.
- **Matej**: Matej's Claude. Commits as `Claude` (branches `claude/...`, merged into `main` automatically).

Code changes are in the git history. Changes marked *(database)* were made in WordPress/Elementor
through Novamira, so they are not in git.

---

## 2026-10-23

**Matej**
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
