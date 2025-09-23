---
title: NostalgiaPHP vs Early WordPress
description: A comparison of early WordPress and NostalgiaPHP–two content management systems with two different approaches.
date: 2025-09-06
image: static/media/wordpress.jpg
---

# NostalgiaPHP vs Early WordPress

## Similarities

1. **PHP everywhere**  
   - Early WordPress themes were just `.php` files mixing HTML + PHP.  
   - NostalgiaPHP does the same — e.g. `templates/main.php` looks a lot like an old WP `page.php` or `index.php`.  

2. **Front-matter / metadata**  
   - WordPress always had the “post metadata” idea (title, date, slug).  
   - NostalgiaPHP uses YAML-like front-matter at the top of Markdown files.  

3. **Templates + partials**  
   - WP has `header.php`, `footer.php`, `sidebar.php`.  
   - NostalgiaPHP has `partials/header.php`, `partials/footer.php`.  
   - Same philosophy: break things into chunks for reuse.  

4. **URL routing**  
   - Early WP “pretty permalinks” were powered by `.htaccess` rewrites to `index.php`.  
   - NostalgiaPHP does exactly that.  

5. **Markdown vibe**  
   - Early WordPress had a “write in plain text, let PHP render it” feeling.  
   - NostalgiaPHP leans harder on Markdown instead of a WYSIWYG editor.  

---

## Differences

1. **Database**  
   - WordPress stores everything in MySQL (posts, pages, options).  
   - NostalgiaPHP stores everything as flat files (Markdown + front-matter).  

2. **Admin panel**  
   - Even in v1.0, WP had an admin dashboard for writing posts.  
   - NostalgiaPHP has no GUI — you edit Markdown in your editor (like Astro or Jekyll).  

3. **Extensibility**  
   - WordPress was designed with plugins/hooks from the start.  
   - NostalgiaPHP is intentionally *not extensible* — it’s minimal, “what you see is what you get.”  

4. **Ecosystem**  
   - WP grew into a CMS/blogging empire with themes, plugins, hosting, etc.  
   - NostalgiaPHP is a micro-tool: no ecosystem, just simplicity.  

5. **Scope creep**  
   - WordPress evolved from “just blogging” to “can power e-commerce + headless APIs + membership sites.”  
   - NostalgiaPHP is a *time capsule* — it refuses to grow bloated, it stays small like early WP.  

---

## TL;DR

- **WordPress (early)** = flat PHP templates + MySQL database + admin dashboard.  
- **NostalgiaPHP** = flat PHP templates + Markdown files + no dashboard.  

If WP 1.0 felt like a “publishing tool for the everyman,” NostalgiaPHP is more like a “publishing tool for developers who *miss* the simplicity of 2004 but don’t want a database anymore.”  
