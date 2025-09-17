// gallery.js
const createGallery = domTarget => {
	const images = domTarget.getAttribute('data-gallery')

	domTarget.innerHTML = images
		? images
				.split(',')
				.map(
					image =>
						`<img src="/static/${image.trim()}" loading="lazy" />`
				)
				.join('')
		: '<p>No images found</p>'
}

// Create image elements for each gallery on the page.
document.querySelectorAll('[data-gallery]').forEach(createGallery)
