// appear.js (ES module or classic; works either way)

const injectCSS = () => {
	if (document.getElementById('appear-css')) return // avoid duplicates
	const css = `
.appear {
  --appear-translate: 128px;
  opacity: 0;
  transform: translateY(20px);
  transition: opacity .5s ease, transform .5s ease;
  will-change: opacity, transform;
}
.appear.is-visible {
  opacity: 1;
  transform: none;
}
/* Variants override the baseline transform */
.appear-up    { transform: translateY(var(--appear-translate)); }
.appear-down  { transform: translateY(calc(var(--appear-translate) * -1)); }
.appear-left  { transform: translateX(var(--appear-translate)); }
.appear-right { transform: translateX(calc(var(--appear-translate) * -1)); }
.appear-scale { transform: scale(.95); }
  `
	const st = document.createElement('style')
	st.id = 'appear-css'
	st.textContent = css
	document.head.appendChild(st)
}

const revealAllNow = () => {
	const show = () => {
		document
			.querySelectorAll('.appear')
			.forEach(el => el.classList.add('is-visible'))
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', show, { once: true })
	} else {
		show()
	}
}

// Respect reduced motion → don't inject the hiding CSS; just reveal.
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
	revealAllNow()
} else if (!('IntersectionObserver' in window)) {
	// No IO support → reveal immediately (again: no hiding CSS injected).
	revealAllNow()
} else {
	// JS present → inject CSS (so elements can start hidden) and observe
	injectCSS()

	const onIntersect = entries => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-visible')
				io.unobserve(entry.target)
			}
		})
	}

	const io = new IntersectionObserver(onIntersect, {
		threshold: 0.2,
	})

	const start = () => {
		const targets = document.querySelectorAll('.appear')
		if (!targets.length) return
		targets.forEach(el => io.observe(el))
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start, { once: true })
	} else {
		start()
	}
}
