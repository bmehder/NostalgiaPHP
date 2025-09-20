---
title: Don't Blink!
date: 2025-09-20
tags: js, slank, blink, reactivity
excerpt: Blink (Slank) is a minimalist reactivity library. It’s not React, it’s not Svelte — just a few lines of code that make small sites feel alive without build tools.
---

# Don't Blink!

## Blink: A Tiny Reactivity Layer

Sometimes you want a little bit of state and reactivity in your project — but you don’t want to bring in a whole framework, a bundler, or a giant runtime. That’s where Blink (published on npm as Slank) comes in.

Blink is only a few lines of JavaScript. You can copy-paste it into a file, or install it from npm if you like. It gives you three exports: explicit, implicit, and fx.

```js
let subscriber = null

export const explicit = value => {
  const subscriptions = new Set()

  return {
    get value() {
      if (subscriber) {
        subscriptions.add(subscriber)
      }
      return value
    },
    set value(newValue) {
      value = newValue
      subscriptions.forEach(fn => fn())
    },
  }
}

export const implicit = fn => {
  const _implicit = explicit()
  fx(() => {
    _implicit.value = fn()
  })
  return _implicit
}

export const fx = fn => {
  subscriber = fn
  fn()
  subscriber = null
}
```

---
## How it works
- `explicit()` creates a reactive value.
Think of it as a simple signal — `count = explicit(0)` — that notifies subscribers when it changes.
- `fx()` sets up a reactive effect.
When you access a signal inside an `fx` function, that effect is re-run whenever the signal changes.
- `implicit()` is just sugar. It creates a derived signal, automatically re-computing its value whenever its dependencies change.

That’s it. No virtual DOM, no compiler, no JSX. Just getters, setters, and a subscription set.

---
## Example

Check out some demos:

- [Counter](/about/blink)
- [Data Fetching](/about/fetch)

---

## Why Blink?

Blink is not trying to be React or Svelte. It’s not even trying to be a framework. It’s just a tiny reactivity core that makes otherwise static sites feel alive.

That makes it a perfect companion for projects like Nostalgia, where you mostly want flat-file content, but you occasionally need a sprinkle of dynamic behavior without introducing complexity.

---

## Further reading
- [Slank on npm (Blink’s package name)](https://www.npmjs.com/package/slank)
