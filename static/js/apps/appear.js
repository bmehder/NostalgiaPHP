;(() => {
	if (!('IntersectionObserver' in window)) {
		document
			.querySelectorAll('.appear')
			.forEach(el => el.classList.add('is-visible'))
		return
	}

	const io = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible')
					io.unobserve(entry.target)
				}
			})
		},
		{ threshold: 0.2 }
	)

	document.querySelectorAll('.appear').forEach(el => io.observe(el))
})()
