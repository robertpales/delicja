# Delicja – instructions for Claude

Read `README.md` first (workflow, who does what, what goes where), then `DESIGN.md` (brand) and
`docs/novamira.md` (how to work on the WordPress site safely).

## Stack
- WordPress + Hello Elementor + Elementor / PRO Elements + WooCommerce. Site language Danish; product names may stay Polish.
- This repo is only the child theme `delicja-child`. It is deployed to test.delicja.dk automatically on every push to `main`.
  A push to a `claude/...` branch is merged into `main` automatically and then deployed, so `main` is always what runs on
  the test site. Start new work from the latest `main`; if the auto-merge fails on a conflict, tell the user.
- The WordPress site is reached through the Novamira MCP connector for test.delicja.dk (its name depends on the
  user's claude.ai account). If no such connector is available, stop and point the user to README → Getting started.
- When the user asks what to do, follow the build order in `DESIGN.md` and propose the next unfinished step;
  check the site first to see what already exists.

## Two places, two tools
- **Elementor (database), via Novamira:** layout, sections, texts, images, Theme Builder templates (header, footer,
  shop, category and product pages, with display conditions), global colours and fonts. Back up before every
  write, check `_edit_lock`, clear caches (see `docs/novamira.md`).
- **Menus are WordPress menus** (Appearance → Menus), created and edited with the WordPress menu API; the header
  and footer show them with Elementor's Nav Menu widget. Never hardcode menu links in templates.
- **Files in this repo, via git:** all CSS, JS and PHP. Commit and push; the deploy does the rest.
- Never write CSS into Elementor's Custom CSS fields (kit, page, element) or into WPCode.
- Never write theme files on the server directly (no `write-file` into the theme folder). Change the repo and push.

## Conventions
- Colours and fonts are Elementor globals. In CSS use `var(--e-global-color-…)` / `var(--e-global-typography-…)`,
  never hex values or font names. Every global font has an explicit line height and letter spacing.
- Global CSS → `assets/css/site.css` (into the matching block). Page CSS → `assets/css/pages/<slug>.css`.
- Class prefix `dl-`. One declaration per line, comments only as block headers (`/* === Header === */`).
- Use Elementor widgets for normal content (texts, images, buttons, products) so the owner can edit them.
  Code (HTML widget) only for things Elementor cannot do.
- Product sections are live (Loop Grid / WooCommerce widgets from the database). Never hardcode products, prices or stock.
- PHP goes to `inc/<area>.php` (auto-loaded). `functions.php` only loads files.
- JS without jQuery, only when a feature needs it.
- Fonts are self-hosted (`assets/fonts/`). No Google Fonts or other third-party requests (GDPR, Denmark).
- Do not update Elementor or PRO Elements.
- Never invent prices, opening hours, CVR, legal texts or reviews. Mark unknowns as `[TODO]`.
- One commit per change with a clear message. Code comments in English.

## Progress log
- After every finished change, add a line to `progress.md` and commit it with the change (or right after it).
- Put it under today's date (newest date on top) and the person's name: **Robo** (robertpales) or **Matej**.
  Create the date heading if it is missing.
- One short line per change, written for people, not a copy of the commit message. Group related commits.
- Mark changes made in WordPress/Elementor through Novamira with *(database)*: they are not in git.
- Open tasks are in `todo.md`, split by who does them (Client via Matej, Matej, Robo). Tick off and move finished
  tasks to `progress.md`; add new open points to the right person.
