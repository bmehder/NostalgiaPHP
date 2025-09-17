// gallery.js
const createGallery = domTarget => {
	const images = domTarget.getAttribute('data-images')
	
	if (!images) return

	const createImage = image =>
		`<img src="/static/${image.trim()}" loading="lazy" />`

	domTarget.innerHTML = images.split(',').map(createImage).join('')
}

document.querySelectorAll('[data-gallery]').forEach(createGallery)
