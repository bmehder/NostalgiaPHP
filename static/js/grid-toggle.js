// /static/js/grid-toggle.js
import { explicit, fx } from '/static/js/blink.js'

const logError = console.error

// Utility to read from local storage
const readInitialMode = () => {
	try {
		const stored = localStorage.getItem('grid-list:view')
		if (stored === 'list' || stored === 'grid') return stored
	} catch (error) {
		logError('Read Local Storage Error:', error.message)
	}
	return 'grid'
}

// 1) State: 'cards' (default) or 'list'
const mode = explicit(readInitialMode())
const setMode = newValue => (mode.value = newValue)

// 2) Find the <h1> and insert the button
const heading = document.querySelector('h1')
const grid = document.querySelector('[data-card-grid]')

if (heading && grid) {
	const btn = document.createElement('button')
	btn.type = 'button'
	btn.dataset.gridToggle = ''
	btn.setAttribute('aria-pressed', 'false')
	btn.style.marginBlockEnd = 'var(--size-3)'
	btn.textContent = 'List'
	heading.insertAdjacentElement('afterend', btn)

	// 3) Bind state → DOM
	fx(() => {
		const isList = mode.value === 'list'
		grid.style.gridTemplateColumns = isList ? 'unset' : ''
		btn.setAttribute('aria-pressed', String(isList))
		btn.textContent = isList ? 'Grid' : 'List'
	})

	// 4) Event - update component state & update local storage
	btn.onclick = () => {
		setMode(mode.value === 'list' ? 'grid' : 'list')

		try {
			localStorage.setItem('grid-list:view', mode.value)
		} catch (error) {
			logError('Set Local Storage Error:', error.message)
		}
	}
}
