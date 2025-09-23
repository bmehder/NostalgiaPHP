(() => {
  if (window.__themeMounted) return;
  window.__themeMounted = true;

  const THEMES = [
		{ name: 'theme-red', label: 'Red', swatch: 'var(--red-600)' },
		{ name: 'theme-blue', label: 'Blue', swatch: 'var(--blue-600)' },
		{ name: 'theme-green', label: 'Green', swatch: 'var(--green-600)' },
		{ name: 'theme-amber', label: 'Amber', swatch: 'var(--amber-600)' },
	]
  const STORAGE_KEY = 'nostalgia:theme';

  const root = document.documentElement;
  const body = document.body;

  const getThemeClasses = el => Array.from(el.classList).filter(c => c.startsWith('theme-'));
  const clearThemes = () => {
    getThemeClasses(root).forEach(c => root.classList.remove(c));
    getThemeClasses(body).forEach(c => body.classList.remove(c));
  };

  const applyTheme = (theme) => {
    clearThemes();
    if (theme) root.classList.add(theme);
    localStorage.setItem(STORAGE_KEY, theme || '');
    document.querySelectorAll('#theme-switcher [data-theme]').forEach(btn => {
      btn.setAttribute('aria-pressed', btn.dataset.theme === theme ? 'true' : 'false');
    });
  };

  const saved   = localStorage.getItem(STORAGE_KEY);
  const initial = THEMES.some(t => t.name === saved) ? saved : THEMES[0].name;
  applyTheme(initial);

  // Inject styles
  const style = document.createElement('style');
  style.textContent = `
    #theme-switcher{
      display:flex; gap:.4rem; align-items:center;
    }
    #theme-switcher button{
      appearance:none; border:0; background:none; margin:0; padding:0;
      inline-size:20px; block-size:20px; display:grid; place-items:center;
      border-radius:999px; cursor:pointer; outline:none;
    }
    #theme-switcher button:focus-visible{
      box-shadow: var(--focus-ring, 0 0 0 3px rgb(56 189 248 / .55));
    }
    #theme-switcher .dot{
      inline-size:14px; block-size:14px; border-radius:999px;
      box-shadow: inset 0 0 0 1px rgb(0 0 0 / .15);
    }
    #theme-switcher [aria-pressed="true"] .dot{
      box-shadow:
        0 0 0 2px var(--bg, #fff),
        0 0 0 4px var(--accent, currentColor);
    }
    #theme-switcher .sr-only{
      position:absolute; clip:rect(0 0 0 0); inline-size:1px; block-size:1px; margin:-1px; overflow:hidden;
    }
  `;
  document.head.appendChild(style);

  // Build switcher inside nav
  const wrap = document.createElement('div');
  wrap.id = 'theme-switcher';
  wrap.setAttribute('role', 'group');
  wrap.setAttribute('aria-label', 'Color theme');

  THEMES.forEach(t => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.dataset.theme = t.name;
    btn.setAttribute('aria-pressed', t.name === initial ? 'true' : 'false');
    btn.title = `Switch to ${t.label} theme`;

    const dot = document.createElement('span');
    dot.className = 'dot';
    dot.style.background = t.swatch;
    btn.appendChild(dot);

    const sr = document.createElement('span');
    sr.className = 'sr-only';
    sr.textContent = t.label;
    btn.appendChild(sr);

    btn.addEventListener('click', () => applyTheme(t.name));

    wrap.appendChild(btn);
  });

  const nav = document.getElementById('site-nav');
  if (nav) nav.appendChild(wrap);

  applyTheme(initial);
})();