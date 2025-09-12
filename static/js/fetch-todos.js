// 0. Import blink - a dead simple signals (observables) system
const { explicit, fx } = window.slank

// 1. Get the DOM elements
const view = {
	list: document.querySelector('[data-todos]'),
}

// 2. Create component state
const todos = explicit([])

// 3. Bind the state to the view elements inside an fx function
fx(() => {
	view.list.innerHTML = todos.value?.map(({ title }) => `<li>${title}</li>`).join('')
})

// Helpers
const toJson = response => response.json()
const setTodos = json => (todos.value = json)
const logError = console.error
const handleFinally = () => console.log('Fetch complete')

// 4. Fetch the data, and assign the result to the state
fetch('https://jsonplaceholder.typicode.com/todos')
	.then(toJson)
	.then(setTodos)
	.catch(logError)
	.finally(handleFinally)