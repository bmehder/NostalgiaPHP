let subscriber = null

export const explicit = value => {
	const subscriptions = new Set()

	return {
		get value() {
			if (subscriber) {
				subscriptions.add(subscriber)
			}
			return value
		},
		set value(newValue) {
			value = newValue
			subscriptions.forEach(fn => fn())
		},
	}
}

export const implicit = fn => {
	const _implicit = explicit()
	fx(() => {
		_implicit.value = fn()
	})
	return _implicit
}

export const fx = fn => {
	subscriber = fn
	fn()
	subscriber = null
}

export const tick = fn => Promise.resolve().then(() => requestAnimationFrame(fn))
