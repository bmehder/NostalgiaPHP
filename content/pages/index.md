---
title: Welcome to NostalgiaPHP
hero: A tiny Markdown CMS.
hero_image: /assets/random-images/6.jpg
---

# NostalgiaPHP  

> A file-based CMS for people who survived WordPress and the JavaScript framework wars.  

---

## Why?  

Back in 2004, WordPress was simple:  
- A few PHP files.  
- A database you could actually understand.  
- A `header.php` and `footer.php` you edited right on the server.  

Then the web went sideways:  
- WordPress themes turned into small operating systems.  
- JavaScript discovered frameworks… then metaframeworks… then frameworks for those.  
- Suddenly your “simple site” needed `npm install` and 600MB of dependencies.  

All you wanted was a website.  

---

## What is NostalgiaPHP?  

It’s **not a framework**.  
It’s **not a static site generator**.  
It’s **definitely not WordPress**.  

👉 It’s a tiny flat-file CMS that:  
- Uses Markdown files as your database.  
- Routes everything through one `index.php`.  
- Renders with simple templates and partials.  
- Makes URLs pretty with `.htaccess`.  

That’s it.  

---

## Features  

- Write pages and collections in **Markdown**.  
- Drop in **partials** (`header.php`, `footer.php`).  
- Add **tags** if you feel fancy.  
- No database. No build step. No Node.  
- Runs anywhere PHP runs (which is… everywhere).  

---

## Installation  

1. Copy the files.  
2. Run `php -S localhost:8000`.  
3. Visit [http://localhost:8000](http://localhost:8000).  
4. Make pages. Done.  

---

## Why Markdown?

Because it’s the glue.  

Your content lives in **Markdown + front-matter**. That’s the portable layer.  
Write it once, and it can be rendered by just about anything:  

- **PHP** → NostalgiaPHP + Parsedown  
- **Python** → Flask + Markdown/Mistune + Jinja2  
- **Ruby** → Sinatra + Kramdown  
- **JavaScript** → Eleventy (11ty) + Markdown-it  
- **Haskell** → Hakyll (Pandoc under the hood)  
- **Go** → Hugo (Markdown + front-matter baked in)  

The runtime doesn’t matter. The content does.  
NostalgiaPHP just happens to be the tiniest, laziest way to get it on the web right now.

---

## Philosophy  

- Simple sites deserve simple tools.  
- Markdown first, HTML if you need it.  
- PHP is fine. Stop pretending it isn’t.  
- If your site needs a build system, you’re overthinking it.  