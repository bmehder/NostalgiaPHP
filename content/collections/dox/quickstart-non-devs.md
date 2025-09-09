---
title: Quickstart for Non-Devs
description: How to do this for people that have no idea.
layout: main
date: 2025-09-10
excerpt: Make site. Fast. This is the fun way to edit a website — no scary dashboards, no logins, just files and folders.
---

# 🪨 NostalgiaPHP Quickstart for Non-Devs

*Make site. Fast. Files good. Framework bad.*

This is the **fun way** to edit a website — no scary dashboards, no logins, just files and folders.

---

## Step 0. Get the Website Files
1. Go to the project on GitHub: 👉 https://github.com/bmehder/NostalgiaPHP
2. Click the green Code button, then click Download ZIP.
3. When it finishes downloading, unzip it (double-click the .zip file). You’ll now have a folder called NostalgiaPHP-main.
4. Move this folder somewhere easy to find, like your Desktop or Documents.

___

## Step 1. Check if Your Mac Already Speaks PHP

Open **Terminal** (in Applications → Utilities).  
Type:

```bash
php -v
```

If you see something like:

```
PHP 8.2.12 (cli) ...
```

🎉 You’re ready! Skip to Step 3.  
If you see “command not found” → continue to Step 2.

---

## Step 2. Teach Your Mac PHP (one-time)

Download this friendly little app:  
👉 [https://herd.laravel.com/](https://herd.laravel.com/)

Open the installer, let it finish. Done. Your Mac speaks PHP now.

---

## Step 3. Enter the Cave

Use Terminal to move into the project folder. Example:

```bash
cd ~/Desktop/NostalgiaPHP
```

(Change the path if you put the folder somewhere else.)

---

## Step 4. Light the Fire 🔥

Still in Terminal, type:

```bash
php -S localhost:8000
```

This means “serve this folder at http://localhost:8000”.

---

## Step 5. Open Browser, See Magic ✨

Go to [http://localhost:8000](http://localhost:8000) in Safari or Chrome.  
That’s your site, live!  

Now edit any `.md` file in the `content/` folder, save, refresh browser → instant change.

---

## Cave Tips

- **Stop the fire** → Press `Ctrl + C` in Terminal.
- **Edit safely** → Markdown files are just text. Open them in **TextEdit** or **VS Code**.
- **Don’t panic** → If a page disappears, you probably broke the `--- front matter ---` at the top. Fix it and reload.
- **Backups** → Copy the folder or put it on GitHub. That’s it.

---

📝 That’s all!  
**Make site. Fast. Files good. Framework bad.**
