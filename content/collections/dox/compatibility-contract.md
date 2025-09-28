---
title: Compatibility Contract
description: Compatibility contract in regards to Directory Structure and how to evolve code without breaking sites.
image: static/media/compatibility-contract.jpg
date: 2025-09-11
---

# Compatibility Contract

## Directory Structure
- content/pages/{slug}.md → /slug (or / for index.md)
- content/collections/{name}/{slug}.md → /{name}/{slug}
- static/ served at /static/...
- Front-matter keys (optional, don’t break if missing)
- Defaults (no surprises)
- If a key is missing, site still renders (infer title/desc or leave blank)
- Templates/partials API
- Templates receive: $title, $content, $meta, $path
- Partials inherit variables from callers; avoid renaming expected vars

## Upgrade Strategy (how to evolve code without breaking sites)
- No content migrations. Never require users to rename folders or files.
- Additive front-matter only. New keys are optional; old keys keep working.
- Graceful fallbacks. If a new feature is disabled/missing, render as before.
- Feature flags in config.php. (e.g., 'features' => ['sitemap_indexes' => true])
- Semantic-ish versioning. Bump a version constant when behavior changes.

## Files you can swap safely
- `functions.php` — new helpers, parsers, fallbacks. Keep function names/returns stable.
- `index.php` — routing. Don’t change URL shapes; add new routes behind flags.
- `config.php` — user merges. Provide defaults in functions.php so omissions don’t break.