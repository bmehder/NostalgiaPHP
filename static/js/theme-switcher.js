;(() => {
	if (window.__themeMounted) return
	window.__themeMounted = true

	const THEMES = ['theme-red', 'theme-blue', 'theme-green', 'theme-amber']
	const STORAGE_KEY = 'nostalgia:theme'

	const root = document.documentElement
	const body = document.body

	const getThemeClasses = el =>
		Array.from(el.classList).filter(c => c.startsWith('theme-'))
	const clearThemes = () => {
		getThemeClasses(root).forEach(c => root.classList.remove(c))
		getThemeClasses(body).forEach(c => body.classList.remove(c))
	}

	const applyTheme = theme => {
		clearThemes()
		if (theme) root.classList.add(theme)
		localStorage.setItem(STORAGE_KEY, theme || '')
		updateButton(theme)
	}

	// Build the floating toggle
	const btn = document.createElement('button')
	btn.type = 'button'
	btn.id = 'theme-toggle'
	btn.setAttribute('aria-label', 'Switch theme')
	btn.style.cssText = `
    position: fixed; top: .75rem; right: .75rem; z-index: 9999;
    display: flex; align-items: center; gap: .5rem;
    padding: .4rem .7rem; border-radius: .5rem;
    border: 1px solid var(--border, #d1d5db);
    background: var(--bg, #fff); color: var(--text, #111);
    box-shadow: var(--shadow-2, 0 3px 5px rgb(0 0 0 / 0.075));
    cursor: pointer; font: inherit;
  `
	const swatch = document.createElement('span')
	swatch.style.cssText = `
    width: 1rem; height: 1rem; border-radius: 50%;
    border: 1px solid rgba(0,0,0,.15); flex-shrink: 0;
  `
	btn.appendChild(swatch)
	const label = document.createElement('span')
	label.textContent = 'Theme'
	btn.appendChild(label)

	const updateButton = theme => {
		// map theme → representative color
		const colors = {
			'theme-red': 'var(--red-600)',
			'theme-blue': 'var(--blue-600)',
			'theme-green': 'var(--green-600)',
			'theme-amber': 'var(--amber-500)',
		}
		swatch.style.background = colors[theme] || '#ccc'
	}

	btn.addEventListener('click', () => {
		const current = getThemeClasses(root)[0] || THEMES[0]
		const idx = THEMES.indexOf(current)
		const next = THEMES[(idx + 1) % THEMES.length]
		applyTheme(next)
	})

	document.body.appendChild(btn)

	// Restore saved theme
	const saved = localStorage.getItem(STORAGE_KEY)
	applyTheme(THEMES.includes(saved) ? saved : THEMES[0])
})()
