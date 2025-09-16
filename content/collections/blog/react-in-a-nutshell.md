---
title: React in a Nutshell
description: This page shows you most of what you need to know to understand React.
date: 2025-09-15
excerpt: This page covers the essentials of React. A React component is simply a function that outputs HTML and manages its own state.
---
# React in a Nutshell

This page covers the essentials of React. A React component is simply a function that outputs HTML and manages its own state.

<iframe width="560" height="315" src="https://www.youtube.com/embed/OA5JAmTcTz4?si=3thxFpQE6-ESJ0aJ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

## Counter.jsx

```jsx
import { useState } from 'react'

const Counter = (props) => {
  const [count, setCount] = useState(0)

  // Callbacks
  const dec = prev => prev - 1
  const inc = prev => prev + 1
  const zero = () => 0

  return (
    <div>
      <p>The count is {count}, <strong>{props.word}</strong>.</p>
      <button onClick={() => setCount(dec)}>-</button>
      <button onClick={() => setCount(inc)}>+</button>
      <button onClick={() => setCount(zero)}>Reset</button>
    </div>
  )
}

export default Counter
```

## App.jsx (the root component)

```jsx
import Counter from './Counter.jsx'

const App = () => (
  <>
    <Counter word="first" />
    <Counter word="second" />
  </>
)

export default App
```