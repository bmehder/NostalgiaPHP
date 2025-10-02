;(() => {
	const prefersReduced = window.matchMedia(
		'(prefers-reduced-motion: reduce)'
	).matches
	if (prefersReduced) return

	const STAGGER_DEFAULT = 70 // ms per child

	const onEnter = el => {
		// If container with children, apply stagger via inline delays
		if (el.hasAttribute('data-reveal-children')) {
			const step = parseInt(el.getAttribute('data-stagger') || STAGGER_DEFAULT, 10)
			;[...el.children].forEach((child, i) => {
				child.style.transitionDelay = `${i * step}ms`
			})
		}
		el.classList.add('is-visible')
	}

	const io = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					onEnter(entry.target)
					// unobserve once revealed
					io.unobserve(entry.target)
				}
			})
		},
		{
			root: null,
			rootMargin: '0px 0px -10% 0px', // start a bit before fully in view
			threshold: 0.2,
		}
	)

	// Single elements
	document.querySelectorAll('.reveal').forEach(el => io.observe(el))
	// Containers that stagger their children
	document.querySelectorAll('[data-reveal-children]').forEach(el => io.observe(el))

	// Optional: tasteful hero on first paint (no scroll needed)
	const hero = document.querySelector('[data-hero-entrance]')
	if (hero) requestAnimationFrame(() => hero.classList.add('is-visible'))
})()
