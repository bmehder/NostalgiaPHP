import { pipe, map, filter, join } from 'https://esm.sh/canary-js@latest'
import { explicit, implicit, fx } from './blink.js'

// Fetch helpers
const toJson = response => response.json()
const logError = console.error
const logDone = () => console.log('Fetch complete')

// Set state helpers
const setTodos = newValue => (todos.value = newValue)
const setFilter = newValue => (filterBtn.value = newValue)

// View helpers
const joinItems = join('')
const createListItems = map(
	({ id, title, completed }) => `
    <li>
      <div class="todo-row">
        <span class="num">${id}.</span>
        <label for="todo-${id}">
          <input
            id="todo-${id}"
            type="checkbox"
            data-checkbox
            data-id="${id}"
            ${completed ? 'checked' : ''}
          >
          <span class="${completed ? 'linethrough' : ''}">${title}</span>
        </label>
      </div>
    </li>
  `
)
const createTodoList = pipe(createListItems, joinItems)

// 1) DOM
const view = {
	list: document.querySelector('[data-todos]'),
	filtersContainer: document.querySelector('[data-filters]'),
	filterButtons: document.querySelectorAll('[data-filter]'),
}

// 2) State
const todos = explicit([])
const filterBtn = explicit('all') // 'all' | 'active' | 'completed'

const visibleTodos = implicit(() => {
	const current = filterBtn.value
	
	return current === 'active'
		? filter(t => !t.completed)(todos.value)
		: current === 'completed'
		? filter(t => t.completed)(todos.value)
		: todos.value
})

// 3) Checkbox change (event delegation) - set Todos state
view.list.addEventListener('change', event => {
	const checkbox = event.target
	const id = checkbox.dataset.id
	const isChecked = checkbox.checked

	const updateItems = map(todo =>
		String(todo.id) === id ? { ...todo, completed: isChecked } : todo
	)

	setTodos(updateItems(todos.value))
})

// 4) Filter clicks — set state + toggle aria-pressed
view.filterButtons.forEach(button => {
	button.onclick = () => {
		// update state
		setFilter(button.dataset.filter)

		// reflect pressed state on all buttons
		view.filterButtons.forEach(btn =>
			btn.setAttribute('aria-pressed', String(btn === button))
		)
	}
})

// 5) Render whenever state or filter changes
fx(() => {
	view.list.innerHTML = createTodoList(visibleTodos.value)

	view.filterButtons.forEach(btn => {
		btn.setAttribute('aria-pressed', String(btn.dataset.filter === filterBtn.value))
	})
})

// 6) Fetch data
fetch('https://jsonplaceholder.typicode.com/todos')
	.then(toJson)
	.then(setTodos)
	.catch(logError)
	.finally(logDone)
