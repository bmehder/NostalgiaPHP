// gallery.js
document.querySelectorAll('[data-gallery]').forEach(target => {
	const images = target.getAttribute('data-images')
	if (!images) return

	target.innerHTML = images
		.split(',')
		.map(image => `<img src="/static/${image.trim()}" loading="lazy" />`)
		.join('')
})
