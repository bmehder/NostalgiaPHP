// /assets/js/grid-toggle.js
import { explicit, fx } from '/static/js/blink.js' // adjust path if needed

// 1) Grab view elements
const view = {
	btn: document.querySelector('[data-grid-toggle]'),
	grid: document.querySelector('[data-card-grid]'),
}
if (!view.btn || !view.grid) {
	// Not on a collection page — bail quietly
	// (Keeps this safe to include site-wide)
	// No-op.
} else {
	// 2) State: 'cards' (default) or 'list'
	const mode = explicit(readInitialMode())

	// 3) Bind state → DOM
	fx(() => {
		const isList = mode.value === 'list'

		// Toggle inline style only; keep your CSS as source of truth.
		// 'list' => force single column via 'unset'
		// 'cards' => clear inline style so your CSS rule wins
		view.grid.style.gridTemplateColumns = isList ? 'unset' : ''

		// Accessible toggle button state + label
		view.btn.setAttribute('aria-pressed', String(isList))
		view.btn.textContent = isList ? 'Cards' : 'List'
	})

	// 4) Events — just flip state
	view.btn.onclick = () => {
		mode.value = mode.value === 'list' ? 'cards' : 'list'
		try {
			localStorage.setItem('grid:view', mode.value)
		} catch (e) {}
	}

	function readInitialMode() {
		try {
			const stored = localStorage.getItem('grid:view')
			if (stored === 'list' || stored === 'cards') return stored
		} catch (e) {}
		return 'cards' // default
	}
}
