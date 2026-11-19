# Third-Party Notices

Sodai's own code is MIT-licensed (see [LICENSE](LICENSE)). This file records
what else ships with the repository, where it came from, and why it is not
covered by that license. It was compiled by inspecting file headers,
`composer.lock`, `package.json` and the theme attribution already present in
the templates.

## Commercially licensed templates (the important part)

| Component | Author | Evidence | Files | Terms |
| --- | --- | --- | --- | --- |
| **UBold — Admin & Dashboard Template**, v8.0.0 | Coderthemes | Header of `resources/js/vendor.js`; `package.json` (`"name": "ubold"`, `"author": "Coderthemes"`); admin footer credit in `resources/views/admin/include/partials/footer.blade.php` | `resources/views/admin/**`, `resources/js/**`, `resources/scss/**`, `public/images/` admin artwork | ThemeForest commercial license |
| **Ekka — Ecommerce HTML Template**, v3.7 | ashishmaraviya | Header of `public/visitor/css/style.css` and `public/visitor/js/main.js` ("Copyright 2023/2024", `themeforest.net/user/ashishmaraviya`) | `public/visitor/**` (CSS, JS, SCSS, fonts, images, video) | ThemeForest commercial license |

**What this means:** both are paid ThemeForest items. A ThemeForest license is
granted per end product and does not transfer redistribution rights for the
template source. The templates are committed here because the application is
built on them and cannot be reviewed without them, but that does not make them
open source and it does not make them MIT. Reusing this repository means
obtaining your own license for the templates, or removing and replacing their
files. Do not republish the template files as your own.

The Blade views under `resources/views/admin/` and `resources/views/visitor/`
are Sodai's own markup. They are derived from the templates' demo markup and
use the templates' CSS and JS classes, so treat them as built on the licensed
templates rather than as standalone MIT assets.

## Bundled vendor code

| Location | What | License |
| --- | --- | --- |
| `vendor/` (installed via Composer, not committed) | Laravel framework, Spatie Laravel Permission, Intervention Image, PHPUnit and the rest of `composer.lock` | Each package's own license (predominantly MIT); see `vendor/<vendor>/<package>/LICENSE*` and the `license` key in `composer.lock` |
| `node_modules/` (installed via npm, not committed) | Bootstrap, Lucide, ApexCharts, DataTables, Pickr and the other front-end dependencies | Each package's own license, mostly MIT; some packages are dual MIT/GPL |
| `public/visitor/js/vendor/` | jQuery 3.5.1, jQuery Migrate 3.3.0, Bootstrap bundle, Popper, Modernizr 3.11.2, Magnific Popup, particles.js | MIT (jQuery, Bootstrap, Popper, Modernizr, Magnific Popup, particles.js); shipped as part of the Ekka template, so their own notices apply |
| `public/visitor/css/plugins/` | Animate.css, Bootstrap, Magnific Popup, noUiSlider, countdown timer | MIT / the respective project's license |
| `public/plugins/pdf.worker.min.js` | PDF.js worker | Apache-2.0 |
| `public/build/` | Vite output, generated from `resources/js` and `resources/scss` | Not committed (gitignored); contains the same third-party code as above |

## Fonts and media

| Location | What | Notes |
| --- | --- | --- |
| `public/visitor/fonts/` | Montserrat, Oswald, Poppins, Fjalla One, plus Ekka's `ecicons` icon font and the Unicons icon sets | Bundled with the Ekka template. The named families are Google Fonts, published under the SIL Open Font License; `ecicons` and the Unicons builds belong to their respective authors. No license file was shipped alongside them, so the authoritative terms come from the template package and each font's upstream project |
| `public/images/logo-*`, favicons | Sodai branding | Original artwork, covered by the repository's MIT license |
| Product, banner and category imagery under `public/images/` and `public/visitor/images/` | Demo media | Placeholder/demo content shipped with the templates or sourced for the demo; do not reuse as commercial product photography |

## What was checked

- Every file carrying a template header (`Coderthemes`, `UBold`, `Ekka`,
  `ashishmaraviya`, `ThemeForest`) was located and mapped to the tables above.
- `composer.json` declares `"license": "MIT"` and `README.md` states the same;
  both now point at this file for the exceptions.
- No `LICENSE` file existed in the repository before this audit, so there was
  no conflicting license text to reconcile.
- The admin footer already credits UBold to Coderthemes; it was left in place.

## If this ever becomes a product

The clean path to a fully open-source repository is replacing UBold and Ekka
with in-house or permissively licensed UI, or moving the template files out of
the public repository and documenting how to install them. Until then, the MIT
license covers Sodai's own code and the templates stay under their commercial
terms.
