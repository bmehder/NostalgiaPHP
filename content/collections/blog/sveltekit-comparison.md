---
title: NostalgiaPHP vs SvelteKit
description: A comparison between SvelteKit and NostalgiaPHP.
date: 2025-09-07
image: static/uploads/svelte.png
---

# NostalgiaPHP vs SvelteKit

Two very different tools… with the same nostalgic goal:  
👉 **Make building websites feel simple again.**

---

## 🐘 NostalgiaPHP
- **No build step.** PHP runs directly in Apache or Nginx.  
- **Markdown for content.** Write files, save, done.  
- **File-based routing.** Pages and collections map directly to the file system.  
- **Zero dependencies.** Just PHP. No npm, no package-lock.json, no node_modules.  
- **Portable.** Your project folder *is* the site. Move it, back it up, deploy it.  

---

## 🔥 SvelteKit
- **File-based routing.** Drop files into `src/routes`, instant pages.  
- **SSR + client hydration.** Modern interactivity with server-rendered performance.  
- **Build tooling.** Vite, npm, adapters for deployment targets.  
- **Ecosystem.** Rich integrations, plugins, and community support.  
- **Portable(ish).** Your source folder is portable, but it requires a build to deploy.  

---

## ✨ The Similarity
Both exist because developers got tired of **complex, overbuilt stacks**.  
- PHP in 2003: upload a file, refresh, instant site.  
- SvelteKit in 2020s: create a route file, save, instant site (with hot reload).  

---

## 🚀 The Difference
- **NostalgiaPHP** = caveman simple. Drop Markdown + PHP files, and you’ve shipped a site.  
- **SvelteKit** = modern simple. Rich app framework, but still needs Node, npm, builds, adapters.  

---

### TL;DR
If you want a **content-heavy site** that deploys in a minute:  
👉 *Make site. Files good. Framework bad.* (NostalgiaPHP)  

If you want a **modern interactive app** with transitions, API routes, and full SPA feel:  
👉 *SvelteKit.*  