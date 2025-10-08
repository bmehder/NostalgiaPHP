---
title: You Might Not Want to Use NostalgiaPHP
description: A critical look at NostalgiaPHP, weighing its promise of simplicity against the complexities of modern web development.
date: 2025-10-08
image: /static/media/not-nostalgia-php.jpg
template: main
---

# You Might Not Want to Use NostalgiaPHP

Every few years the web development world rediscovers a seductive idea: *what if we could just keep things simple?* Write Markdown files, sprinkle in some templates, glue it together with a few hundred lines of PHP, and voilà — a CMS without the CMS. NostalgiaPHP is the latest to embody this dream, and the appeal is understandable.  

Many developers have spent years building tools like SvelteKit, Next.js, and others to combat complexity, so there is sympathy for this approach. But it’s worth interrogating the “return to simplicity” narrative before canonizing it as the future.  

## The false binary

There’s a tendency to frame modern frameworks as bloated monstrosities, weighed down by build tools, hydration, reactive state libraries, and endless npm dependencies. And then, by contrast, something like NostalgiaPHP seems almost heroic: *no build step, no database, just files on disk and some elegant PHP helpers*.  

But the choice is not binary. Complexity doesn’t accrue because developers are masochists; it accrues because requirements do. Accessibility, security, offline support, progressive enhancement, internationalization, streaming, SSR, hydration, edge deployment, scalability — these are not optional for many teams. Pretending otherwise risks setting people up for a painful awakening when “the simple thing” no longer meets their needs.  

## The persistence of state

NostalgiaPHP is primarily a server-side rendering (SSR) framework, with static site generation (SSG) offered as a side option. Static site generation is powerful. It reduces runtime complexity and infrastructure cost. But real-world apps aren’t just documents; they’re conversations between user and server, perpetually changing. Forms, authentication, dashboards, collaborative editing, live data streams — these are not accidental. They’re what the web *is*.  

A framework that only concerns itself with “content in, HTML out” risks relegating itself to brochureware. That’s not a criticism if brochureware is all you need. But calling it a general solution to the web is misleading.  

## Reinventing the wheel, again

One of the ironies of NostalgiaPHP is that it touts its *lack* of frameworks as a virtue — and then slowly re-implements framework features by hand: routing, tags, search indexes, breadcrumbs, page animations. At some point, you’ve built… a framework.  

The problem is, unlike established frameworks, you don’t benefit from the ecosystem’s accumulated wisdom: performance profiling, accessibility audits, cross-browser quirks, security research, battle-tested plugins. You’re on your own. That’s a heavy price for nostalgia.  

## The real lesson

This is not to sound dismissive. Experiments like this remind us that complexity isn’t inevitable, and that it’s possible to ship real sites with almost nothing. But caution is warranted against romanticizing it into a movement.  

The future of the web is not about rejecting frameworks or databases or build tools. It’s about making them disappear into the background, giving developers the *illusion* of simplicity without sacrificing power. That’s the direction many modern tools are moving towards — zero-config builds, compile-time reactivity, tiny runtime footprints.  

NostalgiaPHP is a nice reminder that HTML is still HTML, CSS is still CSS, and Markdown is a joy. But the future won’t come from rewinding the tape. It’ll come from reimagining what developer experience can be in the face of real-world complexity.
