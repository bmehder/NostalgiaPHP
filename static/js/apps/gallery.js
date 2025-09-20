// // gallery.js
// const createGallery = domTarget => {
// 	const images = domTarget.getAttribute('data-gallery')

// 	domTarget.innerHTML = images
// 		? images
// 				.split(',')
// 				.map(
// 					image =>
// 						`<img src="/static/${image.trim()}" loading="lazy" />`
// 				)
// 				.join('')
// 		: '<p>No images found</p>'
// }

// // Create image elements for each gallery on the page.
// document.querySelectorAll('[data-gallery]').forEach(createGallery)

// Utility: normalize to /static/media
const normalizeSrc = raw => raw.trim() || null

// Singleton lightbox <dialog>
let lightbox
let lightboxImg
let closeBtn
let prevBtn
let nextBtn

// State for current gallery/session
let activeList = []
let activeIndex = 0

const ensureLightbox = () => {
	if (lightbox) return lightbox

	lightbox = document.createElement('dialog')
	lightbox.className = 'lightbox'
	lightbox.innerHTML = `
    <figure class="lightbox__frame">
      <img style="width: var(--lg)" class="lightbox__img" alt="">
      <button class="lightbox__btn lightbox__prev" aria-label="Previous">‹</button>
      <button class="lightbox__btn lightbox__next" aria-label="Next">›</button>
      <button class="lightbox__close" aria-label="Close">✕</button>
    </figure>
  `
	document.body.appendChild(lightbox)

	lightboxImg = lightbox.querySelector('.lightbox__img')
	closeBtn = lightbox.querySelector('.lightbox__close')
	prevBtn = lightbox.querySelector('.lightbox__prev')
	nextBtn = lightbox.querySelector('.lightbox__next')

	// Close on backdrop click
	lightbox.addEventListener('click', e => {
		const rect = lightboxImg.getBoundingClientRect()
		const clickedOutside =
			e.clientX < rect.left ||
			e.clientX > rect.right ||
			e.clientY < rect.top ||
			e.clientY > rect.bottom
		if (clickedOutside) lightbox.close()
	})

	// Buttons
	closeBtn.addEventListener('click', e => {
		e.stopPropagation()
		lightbox.close()
	})
	prevBtn.addEventListener('click', e => {
		e.stopPropagation()
		showAt(activeIndex - 1)
	})
	nextBtn.addEventListener('click', e => {
		e.stopPropagation()
		showAt(activeIndex + 1)
	})

	// Keyboard: Esc, ← →
	lightbox.addEventListener('cancel', e => {
		// Prevent default to avoid form behavior; close() is fine
		e.preventDefault()
		lightbox.close()
	})
	lightbox.addEventListener('keydown', e => {
		if (e.key === 'ArrowLeft') {
			e.preventDefault()
			showAt(activeIndex - 1)
		}
		if (e.key === 'ArrowRight') {
			e.preventDefault()
			showAt(activeIndex + 1)
		}
		if (e.key === 'Escape') {
			e.preventDefault()
			lightbox.close()
		}
	})

	// Click image to advance
	lightboxImg.addEventListener('click', () => showAt(activeIndex + 1))

	return lightbox
}

const showAt = idx => {
	if (!activeList.length) return
	// wrap-around
	activeIndex = (idx + activeList.length) % activeList.length
	const src = activeList[activeIndex]
	lightboxImg.src = src
}

const openLightbox = (list, startIndex = 0) => {
	ensureLightbox()
	activeList = list
	showAt(startIndex)
	lightbox.showModal()
	// move focus into dialog for keyboard nav
	nextBtn.focus()
}

// Build each gallery on the page
const createGallery = domTarget => {
	const raw = domTarget.getAttribute('data-gallery') || ''
	const list = raw.split(',').map(normalizeSrc).filter(Boolean)

	if (!list.length) {
		domTarget.innerHTML = '<p>No images found</p>'
		return
	}

	// Render thumbnails (or full images) – keep your existing grid classes
	domTarget.innerHTML = list
		.map(
			(src, i) =>
				`<button type="button" class="gallery__thumb" data-index="${i}" style="all:unset; display:block; cursor:zoom-in; border: 1px solid var(--gray-200)">
         <img src="${src}" loading="lazy" decoding="async" alt="">
       </button>`
		)
		.join('')

	// Wire click → open lightbox starting at clicked index
	domTarget.querySelectorAll('.gallery__thumb').forEach(btn => {
		btn.addEventListener('click', () => {
			const index = parseInt(btn.getAttribute('data-index') || '0', 10)
			openLightbox(list, index)
		})
	})
}

// Initialize all galleries
document.querySelectorAll('[data-gallery]').forEach(createGallery)
