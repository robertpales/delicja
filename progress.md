# Progress

What was done on the Delicja test site (test.delicja.dk), by date and by person.

- **Robo**: Róbert Páles. Commits as `robertpales`; also changes made in the database through Novamira.
- **Matej**: Matej's Claude. Commits as `Claude` (branches `claude/...`, merged into `main` automatically).

Code changes are in the git history. Changes marked *(database)* were made in WordPress/Elementor
through Novamira, so they are not in git.

---

## 2026-10-23

**Matej**
- Category photos: 12 new natural photos (Polish home kitchen, daylight, wooden table) with alt texts; the old
  cut-out images stay in the media library. *(database)* Category tiles show the photo in a rounded frame.
- Homepage feels more human: new section "A little Polish shop on Bethaniagade" with real photos of the shop and
  Elżbieta as polaroids, handwritten "Zapraszamy!" (self-hosted Caveat font) and a hand-drawn underline; the
  "Follow Delicja" box shows real photos instead of generated cut-outs. *(partly database)*

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
