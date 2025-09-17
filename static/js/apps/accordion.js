// Mental model of what is going on here.

// view = blink(state)

// 0. Import blink - a dead simple signals (observables) system
import { explicit, fx } from '../blink.js'

document.querySelectorAll('[data-scope]').forEach(scope => {
	// 1. Get the DOM elements
	const view = {
		btn: scope.querySelector('[data-toggle]'),
		panel: scope.querySelector('[data-panel]'),
	}

	// 2. Create component state
	const state = {
		isOpen: explicit(false)
	}

	// 3. Bind the state to the view elements inside an fx function
	fx(() => {
		view.btn.setAttribute('aria-expanded', String(state.isOpen.value))
		view.panel.hidden = !state.isOpen.value
		view.btn.textContent = state.isOpen.value ? 'Close' : 'Open'
	})

	// 4. Toggle the state - don't even need an event listener. No bubbles.
	view.btn.onclick = () => (state.isOpen.value = !state.isOpen.value)
})
