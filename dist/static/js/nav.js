// nav.js - for mobile menu
const btn = document.querySelector('[data-nav-toggle]')
const nav = document.querySelector('[data-site-nav]')
const inner = document.querySelector('[data-inner-header]')

if (btn && nav) {
	btn.addEventListener('click', () => {
		const expanded = btn.getAttribute('aria-expanded') === 'true'
		btn.setAttribute('aria-expanded', String(!expanded))
		nav.setAttribute('aria-expanded', String(!expanded))
		inner && inner.classList.toggle('nav-open', !expanded)
	})
}
