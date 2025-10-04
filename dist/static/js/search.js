// static/js/apps/search.js
const $ = s => document.querySelector(s)
const form = $('#site-search')
const qInp = $('#q')
const scopeInp = $('#in')
const status = $('#search-status')
const list = $('#search-results')

let INDEX = null

function esc(s) {
	return String(s).replace(
		/[&<>"']/g,
		c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c])
	)
}

async function loadIndex() {
	if (INDEX) return INDEX
	const res = await fetch('/static/data/search-index.json', {
		headers: { Accept: 'application/json' },
	})
	if (!res.ok) throw new Error('Failed to load search index')
	const data = await res.json()
	INDEX = data.items || []
	return INDEX
}

function scoreRow(row, needle) {
	const n = needle.toLowerCase()
	const title = (row.title || '').toLowerCase()
	const desc = (row.desc || '').toLowerCase()
	const text = (row.text || '').toLowerCase()
	let score = 0
	if (title.includes(n)) score += 100
	if (desc.includes(n)) score += 40
	if (text.includes(n)) score += 10
	return score
}

function paint(results) {
	if (!results.length) {
		list.innerHTML = `<li class="muted"><em>No results found.</em></li>`
		return
	}
	list.innerHTML = results
		.map(r => {
			const badge = r.type ? `<span class="badge">${esc(r.type)}</span>` : ''
			const date = r.date ? `<small class="muted">${esc(r.date)}</small>` : ''
			return `<li class="item-list-row">
      ${badge}
      <a class="item-list-link" href="${esc(r.url)}">${esc(r.title || r.url)}</a>
      ${date}
    </li>`
		})
		.join('')
}

async function run(pushState = false) {
	const q = qInp.value.trim()
	const scope = scopeInp?.value || 'all'

	const next = new URL(location.href)
	if (q) next.searchParams.set('q', q)
	else next.searchParams.delete('q')
	if (scope && scope !== 'all') next.searchParams.set('in', scope)
	else next.searchParams.delete('in')
	if (pushState) history.pushState(null, '', next)

	if (!q) {
		status.textContent = 'Type above to search.'
		list.innerHTML = ''
		return
	}

	status.textContent = 'Searching…'
	list.innerHTML = ''

	try {
		const items = await loadIndex()
		const needle = q.toLowerCase()

		const filtered = items
			.filter(r => {
				if (scope === 'pages' && r.type !== 'page') return false
				if (scope === 'items' && r.type === 'page') return false
				const s = scoreRow(r, needle)
				r._score = s
				return s > 0
			})
			.sort((a, b) => {
				if (b._score !== a._score) return b._score - a._score
				const ta = a.date ? Date.parse(a.date) || 0 : 0
				const tb = b.date ? Date.parse(b.date) || 0 : 0
				return tb - ta
			})

		status.textContent = `${filtered.length} result${
			filtered.length === 1 ? '' : 's'
		}`
		paint(filtered)
	} catch (e) {
		console.error(e)
		status.textContent = 'Search failed. Please try again.'
	}
}

// hydrate from URL + bind
;(function init() {
	const p = new URLSearchParams(location.search)
	qInp.value = p.get('q') || ''
	if (scopeInp) scopeInp.value = p.get('in') || 'all'

	form.addEventListener('submit', e => {
		e.preventDefault()
		run(true)
	})
	window.addEventListener('popstate', () => {
		const p = new URLSearchParams(location.search)
		qInp.value = p.get('q') || ''
		if (scopeInp) scopeInp.value = p.get('in') || 'all'
		run(false)
	})

	if (qInp.value) run(false)
})()
