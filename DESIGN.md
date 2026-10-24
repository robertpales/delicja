# Delicja: design (brand, pages, shop)

Read this before any design work. All rules in `CLAUDE.md` still apply.

## Visual reference
The approved look is Matej's block theme version of the site (theme `delicja` 0.5.6; preview:
https://claude.ai/artifact/HehG4vRV6szsbi4gs12PuJ). The Elementor site rebuilds it 1:1: same layout, spacing,
sizes and texts at 360, 768, 1024 and 1440 px. Copy is English for now. The block theme was removed from
test.delicja.dk after the rebuild; the Elementor site is now the reference.

### Copy
English for everyone, not only for Poles: a Polish dish or word gets an English name, or the English name with the
Polish one in brackets ("doughnuts", "dumplings (pierogi)"). Product and brand names stay as they are on the pack.
Delicja is a place where people from all over the world meet over Polish food, not a shop only for Poles and Danes.
Image alt texts follow the same rule.

## Brand: Polish tradition, warm and honest
A family deli that respects Polish tradition: home cooking, holidays, quality. Red and white like the Polish
flag, gold script logo, folk embroidery and paper cuts as small accents (edges, bands, grain), never wallpaper
behind text.

### Logo (`assets/images/logo/`)
Built from the shop's original wordmark SVG; do not redraw the lettering.
- `delicja-logo.svg`: round badge (footer, favicon). At least 96 px.
- `delicja-logo-wordmark.svg`: script + "POLSKI SKLEP" (header; also the Site Logo in Site Settings).

### Colours → Elementor Global Colors (*Site Settings → Global Colors*)
| Global | Hex | Use |
|---|---|---|
| Primary | #C8102E | buttons, links, prices on sale, announcement bar, hero and promo panels |
| Secondary | #A30D25 | hover on red |
| Text | #1C1B1A | text and headings |
| Accent | #6B6763 | muted text (result count, notes, old price) |
| White (`dlwhite`) | #FFFFFF | page background, cards |
| Surface (`dlsurface`) | #F5F4F2 | footer, image tiles, category tiles, search field |
| Tint (`dltint`) | #FCEDEE | tag badges, light hover |
| Border (`dlborder`) | #E7E4E0 | lines, card borders, separators |

Text must meet WCAG AA contrast.

### Fonts → Elementor Global Fonts (*Site Settings → Global Fonts*)
**Plus Jakarta Sans** for everything, **Anton** only for the big headline of the homepage hero and the contact page,
**Caveat** (handwriting) only for small handmade notes, photo captions and the handwritten line in those headlines. All are self-hosted in
`assets/fonts/` (OFL) and registered in Elementor by `inc/fonts.php`. Every global font has an explicit line height
and letter spacing. Fluid sizes are `clamp()`.

| Global font (id) | Size | Weight / line height | Use |
|---|---|---|---|
| Display headline (`dldisplay`) | 60–120 px | Anton 400, uppercase / 0.95 | homepage hero title, contact title (85 %) |
| Hero headline (`dlhero`) | 40–76 px | 700 / 1 | About us title |
| H1 (`dlh1`) | 34–52 px | 700 / 1.15 | shop, category and search titles, about page |
| H2 (`primary`) | 28–36 px | 700 / 1.15 | section titles, product name on the product page, page titles |
| H3 (`dlh3`) | 24–30 px | 700 / 1.15 | sub-sections |
| H4 (`dlh4`) | 18–20 px | 700 / 1.15 | small titles |
| Small heading (`dlh5`) | 16 px | 700 / 1.15 | related product names |
| Lead (`secondary`) | 18–20 px | 400 / 1.6 | intro text, price on the product page |
| Text (`text`) | 16 px | 400 / 1.6 | body text |
| Small text (`dlsmall`) / Small strong (`dlsmallb`) | 14 px | 400 or 600 / 1.6 | breadcrumbs, notes, card buttons |
| Eyebrow (`dleyeb`) | 14 px | 700 / 1.6 | small line above section titles |
| Button (`accent`) / Small button (`dlbtnsm`) | 16 / 14 px | 600 / 1.2 | buttons |
| Product name (`dlpname`) | 16 px | 600 / 1.35 | product card |
| Price (`dlprice`) | 18–20 px | 700 / 1.6 | product card |
| Badge (`dlbadge`) | 13 px | 700 / 1 | "-20%" and tag badges |
| Handwritten (`dlhand`) | 24–30 px | Caveat 600 / 1.1 | handmade notes ("Zapraszamy!"), polaroid captions (75 %) |
| Menu, Submenu, Mobile menu, FAQ question | | | header menus, FAQ |

### Ornament (`assets/images/folk/`)
Single-colour SVGs used from CSS only: `grain.svg` (paper grain on red panels), `band.svg` (red cross-stitch folk
band on the about page).

### Handmade touches (`assets/images/hand/`, `site.css` → `=== Handmade ===`)
Real photos of the shop and of Elżbieta beat any generated image: use them wherever people should feel who is behind
Delicja. Polaroid frames (`dl-polaroid` on an Image widget with a custom caption: white frame, tape, handwritten
caption, slightly rotated), a hand-drawn red underline (`<span class="dl-scribble">` inside a heading), handwritten
notes (`dl-hand-note`, global font *Handwritten*) with a drawn arrow. Use them sparingly: one or two per section.

### Hero table (`home.css` → `=== Hero ===`, `site.js` → `=== Hero ===`)
One styled photo of a Polish table on linen with red folk embroidery: three red apples with leaves, kabanosy and a
knife on a wooden board (the knife lies on the free board between the apples and the kabanosy, never on the food), an
unbranded farm-style glass bottle of milk (paper cap tied with twine) with a glass, a glazed doughnut on a porcelain
plate, a jar of dill pickles with dill, fried pierogi on an enamel plate (`delicja-hero-table-apples-2.webp`). No
bread (Delicja does not sell it) and no brand labels. It sits at the bottom right of the red panel and runs off its
bottom edge. Nothing moves on its own. Elementor **Hotspot** widget (`dl-hero-scene`): one invisible hotspot over
each product (`_id` paczek, milk, apples, kabanosy, pickles, pierogi; position in Elementor, size in `home.css`); the
tooltip is the product's story: name in bold, one short sentence (editable in Elementor). With the mouse on a product
(a tap on phones) the rest of the table dims a little, the story shows, and the product changes: the doughnut gets a
bite, the glass of milk is half drunk, the front apple is cut in half, the jar is opened, a dumpling is cut in half,
a kabanos is snapped. The changed state is a second, aligned photo (hidden Image widget `dl-hero-scene-after`,
`delicja-hero-table-apples-2-after.webp`) shown only around that product. **Versions to compare** until one is
chosen: `/?hero=stories` (story only), `/?hero=bites` (change only), `/` both. A new photo must be re-generated
together with its "after" photo, at the same size and alignment. `inc/hero.php` loads the table photo without lazy
loading; it stays the first image in the hero, so Elementor gives it the high fetch priority.

### Photos, not drawings
Product pictures in promo panels are real-looking photos (cut-outs on a transparent background, soft daylight from
the upper left) that run off the bottom edge of the panel: the New arrivals tile has a wooden crate of groceries
(`delicja-new-crate-kielbasa.webp`), the contact page's "Not in Herning?" panel an opened delivery box
(`delicja-delivery-box-kielbasa.webp`). On hover they rise a little. Only products Delicja sells (no bread).

### Crate (`site.css` → `=== Crate ===`)
An empty wooden crate (`delicja-cart-empty-crate.webp`) with the full crate photo on top, aligned (`dl-crate`, Image
widgets `dl-crate-empty` and `dl-crate-full`). Pointing at the crate, or at the button with `dl-crate-fill` in the same
section (`dl-crate-scene`), fills it up from the middle. Used by the empty cart and the 404 page. No cartoon drawings (they made the shop look like a toy
shop).

### Instagram posts (`site.css` → `=== Instagram post ===`)
`dl-ig-post` on an Image widget with a custom caption and a link to https://www.instagram.com/delicja.dk/: white card
with the round logo and "delicja.dk" on top, the photo (4:5), like/comment/share/save icons and the caption after
"delicja.dk", like a post in the Instagram app. Used in "Follow Delicja".

### Buttons
Red, rounded (12 px), white text; hover darker red. Outline variant: red border (`dl-btn-secondary`).
White on red panels (`dl-btn-white`), white outline on red (`dl-btn-light`), light pink pill on red
(`dl-btn-tint`, homepage hero). Underlined text links: dark (`dl-btn-link`), white on red (`dl-btn-link-light`). WooCommerce's own buttons look the
same (`site.css`, `=== Buttons ===`).

## The one rule
Everything that shows products, prices, stock or categories is LIVE: Elementor Loop Grid / Loop Carousel /
WooCommerce widgets filled from the database. Never hardcode product names, prices, images or stock.

Acceptance test: create a product "Test" in wp-admin and refresh. It must appear in every section whose rule
matches. Delete it and it must disappear.

## How the owner controls the shop (no code)
| Owner does this in wp-admin | Result on the site |
|---|---|
| Sets a sale price | Shows in "Weekly deals" with a "-20%" badge; the section appears only while something is on sale |
| Marks the product as featured | Shows in "Customer favourites" |
| Publishes a new product | Shows first in "New arrivals" |
| Picks a category | Shows on that category page, in related products and in search |
| Adds a product tag (e.g. "New", "Bestseller") | Small pill badge on the card, linking to the tag page |
| Sets stock to 0 | Card button says "Sold out" and is disabled |
| Picks Origin, Storage, Diet, Allergens, May contain on the product's Attributes tab | Icons on the product page (Product facts) |
| Keeps the nutrition table in the description (rows Energy … Salt) | Nutrition tiles with icons and % of the reference intake |
| Changes a category's order or image | Category tiles on the homepage follow |
| Edits a menu (*Appearance → Menus*) | Header and footer menus follow |

## Theme Builder templates (*Templates → Theme Builder*)
| Template | Type | Shown on |
|---|---|---|
| Header (21) | Header | entire site |
| Footer (26) | Footer | entire site |
| Products Archive (337) | Product archive | shop, category and tag pages |
| Product search results (347) | Product archive | product search |
| Single Product (339) | Single product | all products |
| Page (340) | Single page | all other pages |
| Information page (451) | Single page | Terms, Privacy Policy, Delivery & returns, Cookies, Om os |
| Cart (341) | Single page | cart |
| Checkout (342), Checkout header (343), Checkout footer (344, empty) | Single page / header / footer | checkout |
| 404 page (346) | 404 | not found |

Loop items (*Templates → Loop Items*): **Product card** (105, homepage and shop), **Related product card** (338,
WooCommerce's default card: related products and search results), **Category tile** (333: tall light tile with the
category name in red, the product count (`[dl_cat_count]`, nothing when empty) and the category image, shown whole,
at the bottom; the first tile is red, and the red moves to the tile under the mouse). Category images are cut-outs on a
transparent background that look like real photos: daylight, natural textures, no labels, no glossy render look
(`delicja-category-<slug>-cutout.webp`, 800 px).
The front page (11), About us (118), Contact (67), Deals (65) and My account (37) are pages built in Elementor
(template *Elementor Full Width*).

## Product card (homepage and shop)
Loop Item 105. Square image on a light rounded tile, badges top-left (sale as "-20%", product tags as pills),
product name (weight is part of the name, e.g. "Kabanosy 250 g"), price (on sale: new price + struck-through
old price), full-width "Add to cart" button. After adding it turns into a stepper (− "2 in cart" +) with a
"View cart" link; products already in the cart show the stepper right away. Pieces from
`inc/woocommerce.php` (shortcodes `[dl_product_badges]`, `[dl_add_to_cart]`), styles in `site.css`
(`=== Product card ===`), the stepper in `assets/js/site.js` (WooCommerce Store API). On hover the product is lifted
a little and tilted (left, every second card right), as if picked up from the shelf, with a soft shadow below; no
zoom. The related product cards do the same.

## Homepage, section by section (front page, CSS in `assets/css/pages/home.css`)
1. Announcement bar with a small delivery van icon, and header (Theme Builder): wordmark, product search ("Search for
   sausages, sweets…"), account and mini cart (WooCommerce hides the mini cart from visitors while the shop is in
   "Coming soon" mode; then a cart link with a red item count shows instead, `site.js`), menu Shop · Deals · About us ·
   Contact (Deals only while at least one product is on sale, `inc/woocommerce.php`; categories without products
   are left out of the menus, `inc/header.php`), "Come say hi!" (handwritten) with the address link.
   Calm and clean: linen background (`dlsurface`), thin lines between the rows and at the bottom; the character
   comes from the red handwritten details, not from ornaments (a folk band there felt kitschy). Menu: a hand-drawn
   red underline draws in on hover and stays under the current page. The Shop dropdown shows each category with its picture (the category
   image, `inc/header.php`), 3 columns; on phones 2 columns in the menu drawer. Mobile: hamburger menu.
2. Hero on a red panel with paper grain: "Polish deli in the heart of Herning" in Anton (H1) with a handwritten line
   and a hand-drawn underline under "Herning", a short lead, a "Browse the shop" pill, a "Visit us in Herning" link
   (About us → Visit us) and the table with Polish food (see Hero table).
3. "Shop by category" with a "See all" link to the shop, then the category tiles (live, category order, images
   and product counts from WooCommerce), 6 per row on desktop, horizontal scroll below 1024 px.
4. "Weekly deals" (live, products on sale, carousel). Hidden while nothing is on sale (body class `dl-no-deals`
   from `inc/woocommerce.php`); it appears by itself when the owner sets a sale price.
5. Two promo panels ("The taste of a Polish Sunday", "Sweets just like grandma's").
5a. "A little Polish shop with a big heart" (`dl-story`): three real photos as polaroids (coffee and cake,
   doughnuts, the shop window), a handwritten "Come say hi!", the story from About us, buttons Our story and Get
   directions.
6. "Customer favourites" (live, featured products) with category chips.
7. "New arrivals" (live, newest products) with a red promo tile as tall as the cards: paper grain and a wooden crate
   of groceries at the bottom (see Photos, not drawings).
8. Four promises (genuine Polish products, delivery, payment, family shop) [TODO confirm with the owner].
9. "Questions and answers" (FAQ accordion) and a "More questions?" box [TODO final answers].
10. "Follow Delicja" with the Instagram link and two real photos as Instagram posts that link to Instagram (hidden
    below 782 px).
11. Footer (Theme Builder): round logo, short text, payment icons, menus Shop and Information, contact,
    copyright line. Company name + CVR [TODO] (required by Danish law), opening hours [TODO].

## Other pages
- **Shop, category and tag pages:** breadcrumbs, title, result count and sorting, 4-column card grid
  (2 on mobile), numbered pagination, a note with a link to the whole shop when a category is empty.
- **Product page:** breadcrumbs, gallery with zoom and lightbox, name, price, short description, quantity and
  "Add to cart", SKU and category, tabs Description / Reviews, related products (first in-stock products of the
  same category or tag, by name). Ingredients, allergens and nutrition are in the product description
  (EU regulation 1169/2011).
  **Product facts** under SKU and category (`inc/product-facts.php`, shortcode `[dl_product_facts]`, `site.css` →
  `=== Product facts ===`): hand-drawn icons (`assets/images/icons/`, named after the attribute term slug or the
  nutrient, coloured red by CSS) that explain the product. Chips: net quantity from the product name, "From Poland"
  (attribute Origin), Storage (Keep chilled / Keep frozen / Store in a cool, dry place), Diet (Vegetarian / Vegan /
  Gluten-free). "Contains" and "May contain" (attributes Allergens and May contain, the 14 EU allergens; traces in
  a lighter dashed style). Nutrition tiles read from the table in the description (rows Energy, Fat, of which
  saturates, Carbohydrate, of which sugars, Fibre, Protein, Salt; "per 100 ml" when the heading says so) with the
  % of the reference intake of an average adult (EU 1169/2011, Annex XIII). Missing data shows nothing; never
  fill an attribute from guesswork, only from the pack or the supplier's data sheet.
- **Search results:** WooCommerce's default layout (title "Search results for: …", 3-column grid of default
  cards, a search field when nothing is found).
- **Cart** (template Cart 341, Elementor Cart widget set to update the cart by itself; `inc/cart.php`,
  `assets/css/pages/cart.css`, `site.js` → Cart): big "Your cart" with a handwritten "good choices!", product
  pictures on linen tiles that tilt on hover, round − and + buttons around the quantity, the coupon as a dashed
  ticket, and the order summary as a paper receipt (zigzag bottom edge, dashed lines, handwritten "Dziękujemy –
  thank you!"). While the store is in Coming soon mode a short note above the checkout button says online ordering
  opens soon. Empty cart: a section of the template with an empty wooden crate (`delicja-cart-empty-crate.webp`)
  over the full crate photo (aligned); pointing at the crate or the "Browse the shop" button fills it up.
- **Checkout:** WooCommerce's Checkout widget with a small header (logo and cart icon) and no footer. Payment,
  shipping and tax logic belong to Robo.
- **My account** (page built in Elementor, `assets/css/pages/my-account.css`): "Hello" with a handwritten
  "witamy!", a short line and a polaroid of Elżbieta ("Elżbieta says hi!") next to the login form, which sits on a
  linen card with a strip of tape. The register form (when Robo enables customer accounts), lost password and the
  account pages (menu as a linen side list, the current page white) use the same style. Logged in, the welcome is
  only the heading and the account pages get the full width.
- **Deals** (`/tilbud/`, page built in Elementor, `assets/css/pages/tilbud.css`): "Deals" with a handwritten "while
  they last", a lead and all products on sale (Loop Grid, product card); with no sale a note and "See the whole
  shop" (`[dl_no_deals]`).
- **Legal pages:** title + page content (Page template). Legal texts [TODO].
- **About us:** page built in Elementor, CSS in `assets/css/pages/about.css`. The four chapters of "It all starts
  with a taste": photos in white frames with tape, joined zigzag by a red-and-white baker's twine (`site.js`, About
  story) that draws itself while scrolling; round number stamps press in as each chapter comes into view. On phones
  only the stamps.
- **Contact** (`/kontakt/`): page built in Elementor, CSS in `assets/css/pages/kontakt.css`. "Come say hi" with a
  realistic photo of the real shop front (`delicja-shop-front-day.webp`, made from the Google Street View picture of
  Bethaniagade 5, May 2025, and the photo of the window logo) as a Hotspot widget with small pulsing dots. Each spot is
  a way to Delicja and changes the photo (Picture reveal, below): the shelves fill up ("Full shelves", link to the
  shop), the door opens with a doormat ("Come in!", directions), coffee and cake appear in the window with rising
  steam ("Stay for coffee", our story), a delivery bike rolls in ("Not in Herning?", shop online). The changed photo
  is `delicja-shop-front-open.webp` (hidden Image widget `dl-contact-shop-after`, aligned). Get directions and Call
  us; six cards (address, phone, e-mail, opening hours on Google Maps, Instagram, company and CVR): the whole card is
  the link, its icon moves its own way on hover and a hand-drawn arrow points in; a red "Not in Herning?" panel with
  an opened delivery box.
- **Picture reveal** (`site.js` → `=== Picture reveal ===`, `site.css` → `=== Picture reveal ===`): the system behind
  the homepage hero and the contact shop front. A Hotspot widget with the photo, plus a hidden Image widget with the
  same photo after a change (same size, aligned). With the mouse on a hotspot (a tap on phones) the changed photo
  shows only around it (mask per hotspot id in the page CSS), the rest dims, the tooltip tells the story.
- **Coming soon screen:** while the store is in WooCommerce's "Coming soon" mode, visitors can browse the shop,
  categories, tags, product search, product pages and the cart; only the checkout shows the Elementor template
  "Coming soon (shop)" (Templates → Saved Templates) instead of WooCommerce's default: a red panel "Almost ready,
  we're packing the first boxes", ordering online opens soon, Get directions and Back to your cart, the delivery box
  rocking now and then (`inc/woocommerce.php`, `site.css` → `=== Coming soon ===`).
- **404:** "This shelf is empty – sorry!", a short line, the product search, "Browse the shop" and "Back to the front
  page", and the crate (see Crate).
- **Information pages** (template Information page): "Good to know", the page title in Anton, the text in a readable
  column (headings h2/h3 smaller than the title) and a linen side card with the Information menu (Appearance → Menus,
  "Footer – Information"; the current page white) and "Questions? Call … or write …".
- **Notices** ("added to your cart", errors) use WooCommerce's block notice banners.

## Known differences from the block theme
- Weekly deals: the carousel arrows are hidden while nothing is on sale.
- Deals and Contact link to `/tilbud/` and `/kontakt/` (the block theme linked to pages that did not exist).
- Quantity +/− signs in the mini cart use the site font.
- In the cart's "New in store" list one image can be a few pixels larger in the block theme (a WordPress image
  sizing quirk there).

## Status and next steps
Done: everything above, checked at 360, 768, 1024 and 1440 px against the block theme.
Next: real texts from the owner ([TODO] places), CVR and opening hours, legal pages, payment and shipping
methods (Robo), the three missing products (DLC-010, DLC-011, DLC-035).
