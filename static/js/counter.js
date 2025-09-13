// Mental model of what is going on here.

// view = blink(state)

// 0. Import blink - a dead simple signals (observables) system
import { explicit, implicit, fx } from './blink.js'

// 1. Get the DOM elements
const view = {
	decrement: document.querySelector('[data-decrement]'),
	increment: document.querySelector('[data-increment]'),
	reset: document.querySelector('[data-reset]'),
	value: document.querySelector('[data-counter-value]'),
	doubled: document.querySelector('[data-counter-doubled]'),
}

// 2. Create component state
const count = explicit(0)
const doubled = implicit(() => count.value * 2)

// 3. Bind the state to the view elements inside an fx function
fx(() => {
	view.value.textContent = count.value
	view.doubled.textContent = doubled.value
})

// 4. Toggle the state - don't even need an event listener. No bubbles.
view.decrement.onclick = () => count.value--
view.increment.onclick = () => count.value++
view.reset.onclick = () => count.value = 0
