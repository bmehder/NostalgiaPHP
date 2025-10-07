---
title: Understanding the Styles
description: When working with this site's stylesheets, it's helpful to understand how the CSS is organized to maintain clarity and scalability.
date: 2025-10-05
image: static/media/understanding-the-styles.jpg
---

# Understanding the Styles

When working with this site's stylesheets, it's helpful to understand how the CSS is organized to maintain clarity and scalability. The styles are broken down into five main files, each serving a distinct purpose:

## Stylesheet Overview

- **reboot.css** - This file contains baseline resets and foundational styles that normalize browser inconsistencies. Think of it as the groundwork that ensures a consistent look across browsers.

- **colors.css** - Colors.css serves as the color palette reference. It contains Tailwind-inspired CSS custom properties (tokens) such as `--stone-100`, `--amber-200`, and others. These tokens provide a consistent and reusable set of colors to choose from without having to generate colors on your own.

- **skins.css** - Skins.css holds design-specific tokens and themes. It defines the overall visual identity, including fonts, spacing, and other design tokens that can be customized or themed.

- **utilities.css** - Utilities.css contains layout primitives and helper classes (e.g., `.wrapper`, `.flow`, `.inner`, `.auto-fit`, `.spread-apart`) that are used repeatedly for structure and spacing across the site. These are not traditional Tailwind-style utilities but foundational classes for consistent layout.

- **components.css** - This file is dedicated to component-scoped styles. It uses CSS nesting to keep styles modular and encapsulated, making it easier to maintain and update individual UI components without affecting others.

## Philosophy

The organization reflects a clear separation of style concerns:

1. **Baseline Resets (reboot.css):** Establish a clean slate for styling by resetting default browser styles.
1. **Color Palette (colors.css):** Provide a centralized and consistent set of color variables inspired by Tailwind CSS for easy theming and maintenance.
1. **Design Tokens & Themes (skins.css):** Define the visual language and theming elements.
1. **Utility Classes (utilities.css):** Provide lightweight, single-purpose classes for quick layout or style adjustments without bloating component code.
1. **Component Styles (components.css):** Encapsulate styles for individual components to promote modularity.

By structuring these five stylesheets this way, developers can easily navigate, update, and extend the site's design system. If you are using this site as an example or starting point, following this pattern can help keep your CSS organized and scalable.
