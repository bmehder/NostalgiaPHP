import { pipe, map, join } from 'https://esm.sh/canary-js@latest'

// 0. Import blink from Global
import { explicit, fx } from './blink.js'

// Fetch Helpers
const toJson = response => response.json()
const setTodos = json => (todos.value = json)
const logError = console.error
const logDone = () => console.log('Fetch complete')

// View Helpers
const createListItem = ({ title }) => `<li>${title}</li>`
const createListItems = map(createListItem)
const joinItems = join('')

// 1. Get the DOM elements
const view = {
	list: document.querySelector('[data-todos]'),
}

// 2. Create component state
const todos = explicit([])

// 3. Bind the state to the view elements inside an fx function
fx(() => {
	view.list.innerHTML = pipe (createListItems, joinItems) (todos.value)
})

// 4. Fetch the data, and assign the result to the state
fetch('https://jsonplaceholder.typicode.com/todos')
	.then(toJson)
	.then(setTodos)
	.catch(logError)
	.finally(logDone)