# Delicja: design (brand, homepage, shop)

Read this before any design work. All rules in `CLAUDE.md` still apply.

## Visual reference
Matej's first version was a block theme (repo `matejsalat/delicja`, branch `claude/sleepy-fermi-d7n225`).
Its homepage is the approved look: take its layout, spacing, card style and section rhythm and rebuild them
in Elementor. The brand files from it (fonts, logo, illustrations, icons, ornament) are already in this repo.

## Brand: Polish tradition, warm and honest
Mood: red Polish folk embroidery and wycinanki paper cuts on white, cobalt-blue folk linocuts on off-white
paper, black-and-white family photos with red embroidery on linen. The shop's existing identity: gold script
"Delicja", red "POLSKI SKLEP", gold double ring on the shop window. The site should feel like a family deli
that respects Polish tradition: home cooking, holidays, quality. Folk ornament is an accent (edges, dividers,
small details), never wallpaper behind text.

### Logo (`assets/images/logo/`)
Built from the shop's original wordmark SVG; do not redraw the lettering.
- `delicja-logo.svg`: round badge, ring in Polish flag colours, gold script and red "POLSKI SKLEP" in the
  middle. Footer, social, favicon. At least 96 px.
- `delicja-logo-wordmark.svg`: script + "POLSKI SKLEP" without the ring. Header and anything smaller.

Upload the logo and illustrations to the Media Library when they are used in Elementor widgets, so the owner
can swap them. Files in `assets/images/folk/` are used from CSS only.

### Colours → Elementor Global Colors
Names are what the owner sees in the editor. Set them in *Site Settings → Global Colors*.

| Elementor global | Name | Hex | Use |
|---|---|---|---|
| Primary (system) | Polsk rød | #C8102E | buttons, sale, links, folk ornament, announcement bar |
| Secondary (system) | Kobolt | #1E4A96 | footer, hero, mobile menu, dropdowns, focus outline |
| Text (system) | Blæk | #1F1A17 | text |
| Accent (system) | Guld | #C9A04A | small decorative details only (never text on light backgrounds) |
| custom | Creme | #FBF7EF | page background |
| custom | Hvid | #FFFFFF | header, cards |
| custom | Hør | #F3EADB | linen tiles, image backgrounds, icon buttons |
| custom | Kant | #E4D9C7 | lines, input borders |
| custom | Dæmpet tekst | #6B6058 | secondary text |
| custom | Mørkerød | #9B0D22 | hover on red |
| custom | Mørk kobolt | #15356E | hover on cobalt |
| custom | Lys guld | #F4E6C6 | pills, hover tiles, links on cobalt |

Text must meet WCAG AA contrast.

### Fonts → Elementor Global Fonts
Both by Polish designers, self-hosted in `assets/fonts/` (OFL), registered in Elementor by `inc/fonts.php`.
- Headings: **Poltawski Nowy** (revival of Adam Półtawski's 1920s Polish antikva), weights 400–700.
- Text and UI: **Lato** (Łukasz Dziedzic, Warsaw), 400, 400 italic, 700, 900.

One global font per kind of text, sizes in px for desktop / tablet / mobile, each with an explicit line height
and letter spacing:

| Global font | Family / weight | Desktop / tablet / mobile |
|---|---|---|
| Hero (H1) | Poltawski Nowy 700 | 56 / 46 / 36 |
| Section title (H2) | Poltawski Nowy 700 | 40 / 34 / 30 |
| Card title (H3) | Poltawski Nowy 700 | 28 / 26 / 24 |
| Eyebrow | Lato 900, uppercase, letter spacing 1.5 px | 14 / 14 / 13 |
| Lead | Lato 400 | 20 / 19 / 18 |
| Text | Lato 400 | 16 / 16 / 16 |
| Small | Lato 400 | 14 / 14 / 14 |
| Button | Lato 700 | 16 / 16 / 15 |
| Product name | Lato 700 | 16 / 16 / 15 |
| Price | Lato 900 | 20 / 20 / 18 |
| Badge | Lato 700 | 13 / 13 / 12 |

Price and product names stay in Lato for legibility; section titles in Poltawski Nowy.

### Ornament (`assets/images/folk/`)
Single-colour SVGs used as CSS masks and painted with a global colour.
- `folk-teeth.svg`: red sawtooth edge under the header.
- `folk-band.svg`: rosettes, heart and stars. Above the footer; between big sections sparingly (at most one per screen).

### Buttons
Red pill, white text (`dl-btn dl-btn-primary`). Outline variant: red border (`dl-btn-secondary`). On cobalt:
white outline (`dl-btn-light`).

## The one rule
Everything that shows products, prices, stock or categories is LIVE: Elementor Loop Grid / WooCommerce widgets
filled from the database. Never hardcode product names, prices, images or stock.

Acceptance test: create a product "Test" in wp-admin and refresh. It must appear in every section whose rule
matches. Delete it and it must disappear.

## How the owner controls the shop (no code)
| Owner does this in wp-admin | Result on the site |
|---|---|
| Sets a sale price | Shows in "Ugens tilbud" with a discount badge |
| Marks the product as featured | Shows in "Vores favoritter" |
| Picks a category | Shows on that category page and in category rows |
| Publishes a new product | Shows first in "Nyheder" |
| Adds the tag "Nyhed" or "Bestseller" | Small pill badge on the card |
| Sets stock to 0 | Card shows "Udsolgt", button disabled |

## Product card (one design, used everywhere)
One Loop Item template, used by every product section on the homepage and by the shop, category and
related-products grids. Top to bottom: square image on a light rounded tile, badges top-left (sale as "-20%",
product tags as pills), product name (weight is part of the name, e.g. "Kabanosy 250 g"), price (on sale: new
price + struck-through old price), full-width "Tilføj til kurv" button. Card CSS in `site.css`
(`/* === Shop === */`); the "-20%" badge and "Udsolgt" text are small filters in `inc/woocommerce.php`.

## Homepage, section by section
v1 = build now, v2 = later.

1. Announcement bar (v1, static): one line, e.g. "Bestil online, hent i butikken i Herning" [TODO].
2. Header (v1, Theme Builder): wordmark logo, product search, address link "Bethaniagade 5, Herning"
   (Google Maps), mini cart, account icon. Menu: Butik, Kategorier (submenu with all categories), Tilbud,
   Om os, Kontakt. Mobile: hamburger menu. Red folk edge underneath.
3. Hero (v1): cobalt rounded panel, eyebrow, headline "Polske delikatesser i hjertet af Danmark", one line of
   text, two buttons (Butik, Om os), basket illustration [TODO final copy].
4. Category tiles (v1): round linen tiles with icon + name for Kød, Mejeri, Bageri, Slik, Snacks, Grøntsager
   og frugter, Drikkevarer, Krydderier, Kosmetik. Horizontal scroll on mobile.
5. "Ugens tilbud" (v1, live): products on sale, 4–8 items, carousel.
6. Two promo banners (v1, static): real offers from the owner [TODO]. A real discount also needs a coupon (Robo).
7. "Vores favoritter" (v1, live): featured products, 8 items. Category chips link to the category pages.
8. "Shop by brands" (v2).
9. "Nyheder" (v1, live): cobalt promo tile + newest products, 3–4 items.
10. Feature boxes (v1, static): 4 promises that are true for Delicja [TODO confirm with owner], e.g.
    "Ægte polske varer", "Hent i butikken", "Betal som du vil". No claims like "100% organic" unless true.
11. Limited products with stock bars (v2).
12. Testimonials (v2): only real reviews from the owner or Google. Never invent reviews (illegal in the EU).
13. Second pair of promo banners (v2).
14. Category row (v2): reusable, category chosen in the editor.
15. Monthly deal (v2).
16. FAQ (v1): accordion about levering, afhentning, betaling, allergener, returret [TODO answers], plus a
    "Har du flere spørgsmål?" box with a Kontakt button.
17. "Følg Delicja" (v1): social links [TODO]. Newsletter later (v2, Robo).
18. Footer (v1, Theme Builder): cobalt, folk band on top, round logo, short text, columns Butik (categories),
    Information (Handelsbetingelser, Privatlivspolitik, Cookies, Levering og returnering, Om os), Kontakt
    (Bethaniagade 5, 7400 Herning, +45 51 43 03 21, info@delicja.dk, opening hours [TODO]), payment icons
    (MobilePay, Dankort, Visa, Mastercard), company name + CVR [TODO] (required by Danish law), social links [TODO].

Product rows scroll horizontally on mobile.

Illustrations (`assets/images/illustrations/`, `assets/images/categories/`) are linocut-style folk-art drawings,
on purpose not product photos, so the site never shows goods the shop does not sell.

## Shop pages (same card everywhere)
- Butik + category pages: title, category chips, sorting, result count, card grid, pagination.
- Product page: gallery left; right: name, price, short description, quantity + add to cart, stock status.
  Below: Ingredienser, Allergener, Næringsindhold, Nettoindhold and Opbevaring clearly visible before purchase
  (EU regulation 1169/2011). Related products use the same card.
- Cart, checkout, order confirmation, mini cart: styling only. Payment, shipping and tax logic belong to Robo.

## Build order (one step at a time, Matej reviews after each)
1. Elementor kit: global colours and fonts. Header and footer.
2. Product card + "Ugens tilbud". Run the acceptance test with a "Test" product.
3. Remaining v1 homepage sections: 3, 4, 6, 7, 9, 10, 16, 17, then 1.
4. Butik, category page, product page.
5. Cart, checkout, mini cart styling.
6. Mobile pass at 360, 768, 1024 and 1440 px.
