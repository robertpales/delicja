# Working on the WordPress site through Novamira

How Claude works on test.delicja.dk (and later delicja.dk) through the Novamira MCP connector.
Lessons from earlier Elementor + WooCommerce projects. Read it before the first write to the site.

---

## 1. Connection

- Novamira is a WordPress plugin that exposes an MCP endpoint. Connect it in claude.ai as a custom connector
  (OAuth, log in with your own WordPress admin account). Application Password clients use `/wp-json/mcp/novamira`.
- **Claude Code loads connectors only when a session starts.** After adding one, restart the session.
- A connector error `404 mcp_endpoint_not_found` means the connection failed (plugin, permalinks, server), not
  that access is missing. Tell the user so they can fix it.
- Do not use the Novamira CLI (`@novamira/cli`); its `auth login` hangs. Use the MCP tools.
- **Never update, deactivate or delete the Novamira plugin**, and never touch the OAuth connections or Application
  Passwords of the user you are connected as. That cuts your own connection.

### Tools
Called through `mcp-adapter-execute-ability` with `ability_name` + `parameters`.
`mcp-adapter-discover-abilities` lists them.

| Ability | Use |
|---|---|
| `novamira/execute-php` | The main tool. Runs PHP with the full WordPress environment (`$wpdb`, all functions and plugins). Returns the `return` value, echoed output and warnings. |
| `novamira/read-file`, `write-file`, `edit-file`, `delete-file`, `list-directory` | Files. `write-file` only for small UTF-8 text; `.php` only inside `wp-content/novamira-sandbox/`. |
| `novamira/create-upload-link` | Temporary upload endpoint for binaries, ZIPs, large JSON. |
| `novamira/create-admin-access-link` | One-time wp-admin login for browser automation. |
| `elementor/*` | Elementor abilities. **They only edit V4 (atomic) elements.** Classic V3 widgets and containers are changed through `_elementor_data` in PHP. |

**Theme files are never written through Novamira.** They come from this repo via the deploy.

---

## 2. Safe workflow (always)

1. **Read first:** page IDs, kit, Theme Builder templates and their conditions, active plugins.
2. **Back up** before every write:
   ```php
   update_option( 'delicja_backup_data_<ID>_<YYYYMMDD-HHMM>', get_post_meta( $id, '_elementor_data', true ), false );
   ```
   Autoload `false`. For the kit back up `_elementor_page_settings`. In the write step, refuse to continue if
   the backup is missing (`if ( ! get_option( … ) ) return 'no backup';`).
3. **Check `_edit_lock`.** If the lock is younger than about 150 s, someone has the document open in the
   Elementor editor, and **their next save overwrites your changes** (this has happened three times).
   Ask them to close the editor, or to reload it without saving after your change.
   ```php
   $l = explode( ':', get_post_meta( $id, '_edit_lock', true ) ); return time() - (int) $l[0];
   ```
4. **Write** (section 3).
5. **Clear caches** (Elementor CSS + WP Fastest Cache).
6. **Verify** on the front end (fetch the HTML or take a screenshot), not only the PHP return value.
7. **Clean up** after approval: drafts, backups in `wp_options` (ask first), work files in the sandbox.

Writing `_elementor_data` through PHP does **not** create Elementor revisions. The backups in step 2 are the only
way back.

---

## 3. Elementor through PHP

### Read and write `_elementor_data`
```php
$d = json_decode( get_post_meta( $id, '_elementor_data', true ), true );
$w = function ( &$els ) use ( &$w ) {
	foreach ( $els as &$e ) {
		if ( 'abc1234' === $e['id'] ) {
			$e['settings']['title'] = 'New title';
		}
		if ( ! empty( $e['elements'] ) ) {
			$w( $e['elements'] );
		}
	}
};
$w( $d );
update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $d ) ) );
delete_post_meta( $id, '_elementor_css' );
delete_post_meta( $id, '_elementor_element_cache' );
\Elementor\Plugin::$instance->files_manager->clear_cache();
if ( function_exists( 'wpfc_clear_all_cache' ) ) {
	wpfc_clear_all_cache( true );
}
return 'ok';
```
- **Always `wp_slash()`** when saving JSON with `update_post_meta`, otherwise WordPress strips backslashes.
- Walk the tree recursively by reference and change elements by `id`.
- Do not build a whole page as a PHP string. Generate the JSON locally, upload it with an upload link into
  `wp-content/novamira-sandbox/delicja-work/`, then `file_get_contents` + `json_decode` check + `update_post_meta`.
- When the user has edited a page in the editor, any local generator is outdated. From then on change only
  specific elements from the current data in the database.

### Kit (Site Settings)
- The kit is an `elementor_library` post; its ID is `get_option( 'elementor_active_kit' )`.
- `_elementor_page_settings` of the kit holds `system_colors`, `custom_colors`, `system_typography`,
  `custom_typography` (each item has an `_id`). On this project its `custom_css` stays empty.
- A global on an element: `$e['settings']['__globals__']['title_color'] = 'globals/colors?id=primary'`, or
  `'globals/typography?id=<_id>'`.
- **If a global is missing, Elementor falls back to the raw value stored on the element**, so never delete that raw value.
- Widgets without their own typography inherit from the *Primary* global. If it has e.g. letter spacing
  -1.5 px, it spreads everywhere, so **every global font has an explicit letter spacing and line height**.
- The Button widget defaults to the *Accent* global. Give buttons their own "Button" global font.
- Font sizes in px for desktop / tablet / mobile (`typography_font_size`, `_tablet`, `_mobile`).
- Kit changes affect the whole site. Tell the user before writing.

### Theme Builder conditions
```php
$cm = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager();
$cm->save_conditions( $tpl_id, [ [ 'type' => 'include', 'name' => 'product_archive', 'sub_name' => 'product_cat' ] ] );
// One page only:
$cm->save_conditions( $tpl_id, [ [ 'type' => 'include', 'name' => 'singular', 'sub_name' => 'page', 'sub_id' => (string) $page_id ] ] );
```
- Takes **an array of parts, not a string**, and rewrites the cache option `elementor_pro_theme_builder_conditions` itself.
- Back up the conditions (option) before changing them.
- `product_archive` sub-conditions: `shop_page`, `product_search`, `product_brand`, `product_cat`, `product_tag`.
- Preview a template without conditions: `?elementor_library=<slug>` (with `preview_id` in its settings).
- The WooCommerce shop page is rendered by the archive template: to redesign the shop, edit the template, not the page.

### Loop Grid and dynamic data
- A Loop Item template can be used in many places. Check where before editing; copy it if in doubt.
- Query controls such as `product_query_include_term_ids` take **term_taxonomy_id**, not term_id.
- `product_query_post_type: current_query` makes a grid follow the current archive.
- Extra data on a card: post meta + a Text Editor widget with the `post-custom-field` dynamic tag.

### Elementor gotchas
- Containers: `--display` is overridden per element, so grids need a direct `display: grid`. Elementor turns on
  flex-wrap on mobile.
- Links in headings inherit the kit's link typography → `a { font: inherit; }`.
- An underline from a parent `<a>` cannot be removed on a child; put `text-decoration: none` on the `<a>`.
- Nav menu: set item padding for `:hover`, `:focus` and `.highlighted` too (otherwise 20 px). SmartMenus often runs
  in touch mode and headless browsers are always touch, so do not test hover there. Open submenus with CSS
  `:hover` / `:focus-within` as well, no `overflow: hidden` on the nav.
- `backdrop-filter` on the header clips the off-canvas cart to the header height.
- Entrance animations (`elementor-invisible`, opacity 0) wait for `elementor-frontend.js`. Use Elementor's native
  animations, not custom keyframes with opacity.
- Link images straight to the file, not to the attachment page (its 301 gets cached by browsers).
- **Elementor 4.3.3 took down another site** (fatal error in `modules/mcp`). Do not update Elementor or PRO Elements.
- Known on this site: with WooCommerce "Coming soon" on, PRO Elements 4.3.0 prints a PHP warning
  (`document.php` line 356) on shop pages for logged-out visitors. Harmless; caused by the plugin.

---

## 4. Files, media, uploads

- **Sandbox:** `wp-content/novamira-sandbox/` is the only place for PHP and work files (`delicja-work/`). Clean up when done.
- **Binaries and large files:**
  1. `novamira/create-upload-link` with `{"path":"wp-content/novamira-sandbox/delicja-work/file.zip","overwrite":true,"expires_in":3600}`
  2. `curl -s -X PUT -H "X-Novamira-Upload-Token: <token>" --data-binary @file.zip https://test.delicja.dk/wp-json/novamira/v1/upload`
  3. Unpack and register in PHP:
  ```php
  require_once ABSPATH . 'wp-admin/includes/image.php';
  $z = new ZipArchive();
  $z->open( WP_CONTENT_DIR . '/novamira-sandbox/delicja-work/file.zip' );
  $dir = wp_upload_dir()['path'];
  for ( $i = 0; $i < $z->numFiles; $i++ ) {
  	$n    = $z->getNameIndex( $i );
  	$dest = "$dir/$n";
  	file_put_contents( $dest, $z->getFromIndex( $i ) );
  	$id = wp_insert_attachment( [ 'post_mime_type' => 'image/webp', 'post_title' => '…', 'post_status' => 'inherit' ], $dest );
  	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
  	update_post_meta( $id, '_wp_attachment_image_alt', '…' );
  }
  ```
- `write-file` does not take binaries.
- `media_sideload_image` fails on URLs without a file extension → `wp_remote_get` + `wp_upload_bits` with your own file name.
- Some hosting firewalls block saves that contain `<svg` or `data:image/svg`. Use SVG files with `<img>` / `url()`
  instead of inline SVG in HTML widgets.
- Images as webp; card images at `medium_large`, not `full`.

---

## 5. CSS conventions

- **Fonts and colours are always Elementor globals**, applied to elements in Elementor. One global font per kind of
  text ("Hero", "Section title", "Lead", "Badge", "Button"…). In CSS refer to them with
  `var(--e-global-color-<id>)` / `var(--e-global-typography-<id>-font-size)`. No font sizes or hex colours in CSS.
- **In Elementor:** backgrounds, images, normal widgets. **In code:** anything reusable (buttons, cards, leads,
  grids, section padding via a class). Fully custom things (counter, SVG animation, script) = HTML widget + code.
- Class prefix `dl-`. Buttons `dl-btn` + `dl-btn-primary` / `dl-btn-secondary` (outline) / `dl-btn-light`.
- **Global CSS** (`assets/css/site.css`) = shared across the site. **Page CSS** (`assets/css/pages/<slug>.css`)
  = sections of one page only.
- Structure: `:root` first, then blocks with one header comment per part of the site (`/* === Base === */`,
  `/* === Header === */`, `/* === Shop === */`), no sub-comments, in English. Add new rules to the matching block.
- One declaration per line, comma-separated selectors on separate lines. No one-line rules.
- Use `body .elementor .elementor-element.` only when Elementor's own styles must be overridden.
- No tables for layout (e.g. opening hours) → divs.
- No eyebrow above every heading; use it where it helps.

---

## 6. Cache

- After a database write: `\Elementor\Plugin::$instance->files_manager->clear_cache()` + `wpfc_clear_all_cache( true )`.
- After a code deploy the theme clears the page cache by itself (`inc/deploy.php`).
- WP Fastest Cache "Combine JS" can load scripts twice; keep it off.

---

## 7. WooCommerce

- Write products through the WC API in PHP (`wc_get_product`, `$p->set_*()`, `save()`).
- Payments, shipping, VAT and transactional email belong to Robo. Do not change them.
- When a draft replaces a live page, move the content **into the original ID** (never change the WooCommerce page
  IDs or the front page).

---

## 8. Working with the user

- **Audit or proposal first, changes after approval.**
- People edit pages in the Elementor editor at the same time. Before writing, check `_edit_lock`; after writing,
  tell them to reload without saving.
- Store IDs of important things (draft pages, templates) in options (`delicja_*_id`) so they can be found later.
- After each step, briefly: what changed, where the backup is, what is left.
