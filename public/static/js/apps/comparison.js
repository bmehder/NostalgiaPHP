// comparison.js (type="module")
const SELECTOR = '[data-comparison]'

function mountComparison(root) {
	const range = root.querySelector('input[type="range"]')
	if (!range) return

	const set = v => root.style.setProperty('--pos', String(v))

	// initialize from current input value (fallback to attribute → 50)
	set(range.value || range.getAttribute('value') || 50)

	// keep CSS var in sync with slider
	range.addEventListener('input', e => set(e.currentTarget.value), { passive: true })
	range.addEventListener('change', e => set(e.currentTarget.value), {
		passive: true,
	})
}

// Auto-mount all on the page
export function mountAllComparisons(selector = SELECTOR) {
	document.querySelectorAll(selector).forEach(mountComparison)
}

// If you just <script type="module" src="comparison.js">:
mountAllComparisons()
