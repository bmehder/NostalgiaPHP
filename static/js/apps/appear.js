;(() => {
	const prefersReduced = () =>
		window.matchMedia('(prefers-reduced-motion: reduce)').matches

	// If no IO or reduce motion, just reveal immediately and bail
	if (!('IntersectionObserver' in window) || prefersReduced()) {
		document.querySelectorAll('.appear, [data-appear-children]').forEach(el => {
			if (el.hasAttribute('data-appear-children')) {
				;[...el.children].forEach(c => (c.style.transitionDelay = ''))
			}
			el.classList.add('is-visible')
		})
		return
	}

	const reveal = el => {
		const stagger = parseInt(el.getAttribute('data-stagger') || '70', 10)
		if (el.hasAttribute('data-appear-children')) {
			;[...el.children].forEach((child, i) => {
				child.style.transitionDelay = `${i * stagger}ms`
			})
		}
		el.classList.add('is-visible')
	}

	const makeObserver = el => {
		const threshold = parseFloat(el.getAttribute('data-threshold') || '0')
		const rootMargin = el.getAttribute('data-root-margin') || '0px 0px -10% 0px'
		return new IntersectionObserver(
			entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						reveal(entry.target)
						if (!entry.target.hasAttribute('data-appear-repeat')) {
							entry.target.__io?.unobserve(entry.target)
						} else {
							// clear stagger so repeat feels fresh
							if (entry.target.hasAttribute('data-appear-children')) {
								;[...entry.target.children].forEach(
									c => (c.style.transitionDelay = '')
								)
							}
							entry.target.classList.remove('is-visible') // will re-add next intersect
						}
					}
				})
			},
			{ root: null, rootMargin, threshold }
		)
	}

	const observe = el => {
		el.__io = makeObserver(el)
		el.__io.observe(el)
	}

	document.querySelectorAll('.appear, [data-appear-children]').forEach(observe)

	// Optional: auto-observe nodes added later
	if ('MutationObserver' in window) {
		new MutationObserver(muts => {
			muts.forEach(m => {
				m.addedNodes.forEach(n => {
					if (!(n instanceof Element)) return
					if (n.matches?.('.appear, [data-appear-children]')) observe(n)
					n.querySelectorAll?.('.appear, [data-appear-children]').forEach(observe)
				})
			})
		}).observe(document.documentElement, { childList: true, subtree: true })
	}

	// Optional: tasteful hero reveal on first paint
	const hero = document.querySelector('[data-hero-entrance]')
	if (hero) requestAnimationFrame(() => hero.classList.add('is-visible'))

	// If user toggles reduced-motion in OS while page is open, respect it
	const mq = window.matchMedia('(prefers-reduced-motion: reduce)')
	mq.addEventListener?.('change', e => {
		if (e.matches) {
			document
				.querySelectorAll('.appear, [data-appear-children]')
				.forEach(el => el.classList.add('is-visible'))
		}
	})
})()
