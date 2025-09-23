---
title: Quickstart for Non-Devs
description: How to do this for people that have no idea.
date: 2025-09-10
image: /static/media/about-nostalgia-php.jpg
template: main
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

## Step 1. Check if Your Computer Already Speaks PHP

- On **Mac**, open **Terminal** (in Applications → Utilities). Type:
  ```bash
  php -v
  ```

- On **Windows**, Open **Command Prompt** (press Win + R, type `cmd`, then hit `Enter`). Type:
  ```bash
  php -v
  ```

- On **either**:
  
  If you see something like: `PHP 8.2.12 (cli) ...`
  🎉 You’re ready!
  Skip to Step 3.

  If you see `command not found` → continue to Step 2.

---

## Step 2. Teach Your Computer PHP (one-time)

- On **Mac**, download this friendly little app:  
👉 [https://herd.laravel.com/](https://herd.laravel.com/)

  Open the installer, let it finish. Done. Your Mac speaks PHP now.

- On **Windows**, download this friendly little app:  
👉 [https://laragon.org/download](https://laragon.org/download)

  Open the installer, let it finish. Done. Your PC speaks PHP now.

>ℹ️ You won't be using the app you just installed, it is just the easiest way to install PHP on your computer.

---

## Step 3. Enter the Cave
- **On Mac:** Use Terminal to move into the project folder. Example:
  ```bash
  cd ~/Desktop/NostalgiaPHP
  ```

  (Change the path if you put the folder somewhere else.)

- **On Windows:** Use Command Prompt to move into the project folder. Example:
  ```bash
  cd JOHNDOE\Desktop\NostalgiaPHP
  ```

  (Change the path if you put the folder somewhere else, e.g. Documents\NostalgiaPHP.)

---

## Step 4. Light the Fire 🔥

- Still in **Terminal** (on Mac), or **Command Prompt** (on Windows), type:

  ```bash
  php -S localhost:8000
  ```

- This means “serve this folder at http://localhost:8000”.

---

## Step 5. Open Browser, See Magic ✨

- Go to [http://localhost:8000](http://localhost:8000) in your favorite browser.  

- That’s your site, live!  

- Now edit any `.md` file in the `content/` folder, save, refresh browser → instant change.

---

## Cave Tips

- **Stop the fire** → Press `Ctrl + C` in Terminal or Command Prompt to stop serving your site.
- **Edit safely** → Markdown files are just text. You can use **TextEdit** (Mac) or **Notepad** (Windows) if you want — they’ll work, but everything looks like plain text.

  >ℹ️ For an easier time, download a free editor like [VS Code](https://code.visualstudio.com/download) (Mac/Windows) or [Notepad++](https://notepad-plus-plus.org/) (Windows). These highlight your code, which makes things easier.
- **Don’t panic** → If a page disappears, you probably broke the `--- front matter ---` at the top. Fix it and reload.

- **Backups** → Copy the folder. That’s it.

---

📝 That’s all!  
**Make site. Fast. Files good. Framework bad.**
