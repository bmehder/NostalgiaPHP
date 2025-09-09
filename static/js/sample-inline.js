// 0. Dynamically import blink - a dead simple signals (observables) system
const { explicit, fx } = window.slank

document.querySelectorAll('[data-scope]').forEach(scope => {
	// 1. Get the DOM elements
	const btn = scope.querySelector('[data-toggle]')
	const panel = scope.querySelector('[data-panel]')

	// 2. Create component state
	const open = explicit(false)

	// 3. Associate the state w/ the DOM elements inside an effect (fx function)
	fx(() => {
		btn.setAttribute('aria-expanded', String(open.value))
		panel.hidden = !open.value
	})

	// 4. Toggle the state - don't even need an event listener.
	btn.onclick = () => (open.value = !open.value)
})
