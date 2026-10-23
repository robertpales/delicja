# Delicja: design (brand, pages, shop)

Read this before any design work. All rules in `CLAUDE.md` still apply.

## Visual reference
The approved look is Matej's block theme version of the site (theme `delicja` 0.5.6; preview:
https://claude.ai/artifact/HehG4vRV6szsbi4gs12PuJ). The Elementor site rebuilds it 1:1: same layout, spacing,
sizes and texts at 360, 768, 1024 and 1440 px. Copy is English for now. The block theme was removed from
test.delicja.dk after the rebuild; the Elementor site is now the reference.

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
**Plus Jakarta Sans** for everything, **Anton** only for the homepage hero headline, **Caveat** (handwriting) only
for small handmade notes and photo captions. All are self-hosted in
`assets/fonts/` (OFL) and registered in Elementor by `inc/fonts.php`. Every global font has an explicit line height
and letter spacing. Fluid sizes are `clamp()`.

| Global font (id) | Size | Weight / line height | Use |
|---|---|---|---|
| Display headline (`dldisplay`) | 48–80 px | Anton 400, uppercase / 1.02 | homepage hero title |
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
Single-colour SVGs used from CSS only: `grain.svg` (paper grain on red panels), `band.svg` (folk band on the
about page).

### Handmade touches (`assets/images/hand/`, `site.css` → `=== Handmade ===`)
Real photos of the shop and of Elżbieta beat any generated image: use them wherever people should feel who is behind
Delicja. Polaroid frames (`dl-polaroid` on an Image widget with a custom caption: white frame, tape, handwritten
caption, slightly rotated), a hand-drawn red underline (`<span class="dl-scribble">` inside a heading), handwritten
notes (`dl-hand-note`, global font *Handwritten*) with a drawn arrow. Use them sparingly: one or two per section.

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
| Sets a sale price | Shows in "Weekly deals" with a "-20%" badge |
| Marks the product as featured | Shows in "Our favourites" |
| Publishes a new product | Shows first in "New arrivals" |
| Picks a category | Shows on that category page, in related products and in search |
| Adds a product tag (e.g. "New", "Bestseller") | Small pill badge on the card, linking to the tag page |
| Sets stock to 0 | Card button says "Sold out" and is disabled |
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
| Page (340) | Single page | all pages except the front page and About us |
| Cart (341) | Single page | cart |
| Checkout (342), Checkout header (343), Checkout footer (344, empty) | Single page / header / footer | checkout |
| 404 page (346) | 404 | not found |

Loop items (*Templates → Loop Items*): **Product card** (105, homepage and shop), **Related product card** (338,
WooCommerce's default card: related products and search results), **Category tile** (333: tall light tile with the
category name in red, the product count (`[dl_cat_count]`, nothing when empty) and the category image, shown whole,
at the bottom; the first tile is red, and the red moves to the tile under the mouse). Category images are cut-outs on a
transparent background that look like real photos: daylight, natural textures, no labels, no glossy render look
(`delicja-category-<slug>-cutout.webp`, 800 px).
The front page (11) and About us (118) are pages built in Elementor (template *Elementor Full Width*).

## Product card (homepage and shop)
Loop Item 105. Square image on a light rounded tile, badges top-left (sale as "-20%", product tags as pills),
product name (weight is part of the name, e.g. "Kabanosy 250 g"), price (on sale: new price + struck-through
old price), full-width "Add to cart" button. After adding it turns into a stepper (− "2 in cart" +) with a
"View cart" link; products already in the cart show the stepper right away. Pieces from
`inc/woocommerce.php` (shortcodes `[dl_product_badges]`, `[dl_add_to_cart]`), styles in `site.css`
(`=== Product card ===`), the stepper in `assets/js/site.js` (WooCommerce Store API).

## Homepage, section by section (front page, CSS in `assets/css/pages/home.css`)
1. Announcement bar and header (Theme Builder): wordmark, product search, account and mini cart, menu
   Shop · Categories (all categories) · Deals · About us · Contact, address link. Mobile: hamburger menu.
2. Hero on a red panel with paper grain: "Polish delicacies in the heart of Denmark" in Anton, a "Browse the shop"
   pill, a "Visit us in Herning" link (About us → Visit us) and the basket photo.
3. "Shop by category" with a "See all" link to the shop, then the category tiles (live, category order, images
   and product counts from WooCommerce), 6 per row on desktop, horizontal scroll below 1024 px.
4. "Weekly deals" (live, products on sale, carousel). Message when nothing is on sale.
5. Two promo panels ("The taste of a Polish Sunday", "Sweets like at babcia's").
5a. "A little Polish shop on Bethaniagade" (`dl-story`): three real photos as polaroids (coffee and cake, pączki,
   the shop window), a handwritten "Zapraszamy!", the story from About us, buttons Our story and Get directions.
6. "Our favourites" (live, featured products) with category chips.
7. "New arrivals" (live, newest products) with a red promo tile.
8. Four promises (genuine Polish products, delivery, payment, family shop) [TODO confirm with the owner].
9. "Questions and answers" (FAQ accordion) and a "More questions?" box [TODO final answers].
10. "Follow Delicja" with the Instagram link and two real photos as polaroids.
11. Footer (Theme Builder): round logo, short text, payment icons, menus Shop and Information, contact,
    copyright line. Company name + CVR [TODO] (required by Danish law), opening hours [TODO].

## Other pages
- **Shop, category and tag pages:** breadcrumbs, title, result count and sorting, 4-column card grid
  (2 on mobile), numbered pagination, a note with a link to the whole shop when a category is empty.
- **Product page:** breadcrumbs, gallery with zoom and lightbox, name, price, short description, quantity and
  "Add to cart", SKU and category, tabs Description / Reviews, related products (first in-stock products of the
  same category or tag, by name). Ingredients, allergens and nutrition are in the product description
  (EU regulation 1169/2011).
- **Search results:** WooCommerce's default layout (title "Search results for: …", 3-column grid of default
  cards, a search field when nothing is found).
- **Cart and checkout:** WooCommerce's Cart and Checkout blocks. The checkout has a small header (site title and
  cart icon) and no footer, like WooCommerce's own checkout. Payment, shipping and tax logic belong to Robo.
- **My account, legal pages, Deals, Contact:** title + page content (Page template). Legal texts [TODO].
- **About us:** page built in Elementor, CSS in `assets/css/pages/about.css`.
- **404:** "Nothing found."
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
