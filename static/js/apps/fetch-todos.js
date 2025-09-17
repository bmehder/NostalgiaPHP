import { always, pipe, map, filter, join } from 'https://esm.sh/canary-js@latest'
import { explicit, implicit, fx } from '../blink.js'

// Data
const todosRoute = 'https://jsonplaceholder.typicode.com/todos'

// Fetch helpers
const logError = console.error
const logDone = () => console.log('Fetch complete')
const toJson = response => response.json()

// Set state helpers
const setTodos = newValue => (todos.value = newValue)
const setFilter = newValue => (filterBtn.value = newValue)

// View helpers
const joinItems = join('')
const createListItems = map(
	({ id, title, completed }) => `
    <li>
      <div class="todo-row grid align-items-center" style="grid-template-columns: 4ch 1fr;">
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
	filterButtons: document.querySelectorAll('[data-filter]'),
}

// 2) State
const todos = explicit([])
const filterBtn = explicit('all') // 'all' | 'active' | 'completed'

const visibleTodos = implicit(() => {
	const filterLookup = {
		active: ({ completed }) => !completed,
		completed: ({ completed }) => completed,
		all: always(true),
	}[filterBtn.value]

	return filter(filterLookup)(todos.value)
})

// 3) Listen for events and set state
view.list.addEventListener('change', event => {
	const checkbox = event.target
	const id = checkbox.dataset.id
	const isChecked = checkbox.checked

	const newItems = map(todo =>
		String(todo.id) === id ? { ...todo, completed: isChecked } : todo
	)

	pipe(newItems, setTodos)(todos.value)
})

view.filterButtons.forEach(button => {
	button.onclick = () => {
		setFilter(button.dataset.filter)
	}
})

// 4) Run side effects whenever state changes
fx(() => {
	view.list.innerHTML = createTodoList(visibleTodos.value)

	view.filterButtons.forEach(btn => {
		btn.setAttribute('aria-pressed', btn.dataset.filter === filterBtn.value)
	})
})

// 5) Fetch data
fetch(todosRoute).then(toJson).then(setTodos).catch(logError).finally(logDone)
