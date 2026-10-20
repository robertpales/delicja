# Delicja: how we work

delicja.dk runs on WordPress with Elementor (Hello Elementor theme, PRO Elements) and WooCommerce.
This repo is the **child theme `delicja-child`**. It holds only our own code and brand files: CSS, JS,
PHP, fonts, logo and illustrations. Everything else is built in Elementor.

## Getting started (Matej)
1. Accept the GitHub invitation and clone the repo: `git clone git@github.com:robertpales/delicja.git`.
2. In claude.ai → Settings → Connectors, add the **Novamira** connector for test.delicja.dk and log in with your
   WordPress account (`adminMato`). The connector URL is in wp-admin → Novamira (Connect page).
3. Open **Claude Code in the cloned folder** (terminal or desktop app) and start a new session, so it loads
   `CLAUDE.md` and the connector. Then ask Claude what to do next.

Work in a local Claude Code session on `main`: only a push to `main` deploys. Cloud sessions (claude.ai/code) push
to their own `claude/...` branch, which does not deploy until it is merged into `main`.

## Who does what
- **Matej:** design. His Claude builds the site in Elementor and writes the code in this repo; Matej reviews.
- **Robo:** hosting, WooCommerce, payments, shipping, VAT, email, backups, production deploys.
- **Shop owner:** products, prices, stock, orders, texts.

## Where we work
1. **test.delicja.dk:** the whole site is built here (password protected, not indexed).
2. When it is finished, Robo moves it once to **delicja.dk** (backup, database, files).
3. After launch we work directly on delicja.dk.

The test site has WooCommerce **Coming soon** mode on: logged-out visitors see a "coming soon" page on the
shop pages. Check the site while logged in.

## What goes where

| What | Where | History |
|---|---|---|
| Layout, sections, texts, images | Elementor (via the Novamira connector or the editor) | Backups in the database (see `docs/novamira.md`) |
| Header, footer, shop, category page, product page | Elementor → Theme Builder templates (with display conditions) | same |
| Menus (items and their order) | WordPress → Appearance → Menus; shown in the header/footer by Elementor's Nav Menu widget | same |
| Colours and fonts | Elementor → Site Settings → Global Colors / Global Fonts | same |
| Global CSS (buttons, cards, header, footer, shop) | `assets/css/site.css` | git |
| CSS for one page | `assets/css/pages/<page-slug>.css` | git |
| Scripts | `assets/js/site.js` | git |
| PHP (WooCommerce tweaks etc.) | `inc/*.php` (loaded automatically) | git |
| Fonts, logo, illustrations, icons | `assets/fonts/`, `assets/images/` | git |

**Rule:** all CSS and custom code lives in files in this repo. Elementor's "Custom CSS" fields (kit, page and
element) and the WPCode plugin are not used, otherwise the change has no history.

## How a code change reaches the site
1. Edit the file locally.
2. Commit with a clear message, e.g. `Product card: sale badge`, and push to `main` or to a `claude/...` branch.
   A push to a `claude/...` branch (e.g. from Claude Code on the web) is **merged into `main` automatically**
   (Actions → "Auto-merge Claude branches"). On a merge conflict the job fails and the branch has to be merged by hand.
3. GitHub deploys `main` to **test.delicja.dk automatically** (Actions → "Deploy to test", about a minute).
   The page cache is cleared after each deploy.
4. Reload the page (or the Elementor preview) and check the result.

Production (delicja.dk) is deployed by Robo, not automatically.

Never edit theme files on the server. The next deploy overwrites them.

## Header, footer and menus
- **Header and footer** are Elementor **Theme Builder** templates (*Templates → Theme Builder*) with the display
  condition *Entire Site*. They are not built on each page, and Hello Elementor's own header/footer is not used.
- **Shop, category and product pages** are Theme Builder templates too (Products Archive, Single Product).
- **Menus** are WordPress menus in *Appearance → Menus* (main menu, footer menus). The header and footer only show
  them with Elementor's **Nav Menu** widget. To add, remove or reorder a menu item, edit the menu, not the template,
  so the owner can change it without Elementor.

## CSS
- **Global CSS** = anything used on more than one page. Add rules to the right block in `site.css`
  (`/* === Header === */`, `/* === Shop === */`…), not at the end of the file.
- **Page CSS** = a file named after the page slug (the part of the URL). The front page has the slug `home` →
  `assets/css/pages/home.css`; `delicja.dk/om-os/` → `om-os.css`. It loads only on that page. Rename the file
  if the slug changes.
- Theme Builder templates (header, footer, product card, product page) have no URL of their own, so their CSS
  goes to `site.css`.
- Class prefix `dl-` (e.g. `dl-btn`, `dl-card`, `dl-hero`). In Elementor: *Advanced → CSS Classes*.
- Colours and fonts only through Elementor variables, e.g. `var(--e-global-color-primary)`,
  `var(--e-global-typography-<id>-font-size)`. No hardcoded hex colours or font names.
- One declaration per line, comments only as block headers.

## Colours and fonts
See `DESIGN.md` for the brand (palette, fonts, ornament, logo) and how it maps to Elementor globals.
Fonts are self-hosted (GDPR): Poltawski Nowy and Lato are in the theme and appear in Elementor's font list.
In *Elementor → Settings → Advanced* set **Google Fonts: Disable**.

## Careful
- **Do not update Elementor or PRO Elements** without asking Robo. A new Elementor version already took down
  another of our sites. Automatic updates are off.
- When Claude edits a page through Novamira, nobody may have that page open in the Elementor editor.
  Saving from the editor overwrites Claude's changes.
- Only one Claude works on the site at a time.
- Never invent prices, opening hours, CVR numbers, legal texts or reviews. Mark unknowns as `[TODO]`.

## One-time setup (Robo)
1. GitHub → Settings → Secrets and variables → Actions, add the secrets listed in
   `.github/workflows/deploy-test.yml`.
2. Add the deploy public key to the server user's `~/.ssh/authorized_keys`.
3. Push to `main` (or run the workflow by hand), then activate **Delicja Child** in *Appearance → Themes*.
