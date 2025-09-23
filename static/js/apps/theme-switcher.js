(() => {
  if (window.__themeMounted) return;
  window.__themeMounted = true;

  const THEMES = [
    { name: 'theme-red',   label: 'Red',   swatch: 'var(--red-600)' },
    { name: 'theme-blue',  label: 'Blue',  swatch: 'var(--blue-600)' },
    { name: 'theme-green', label: 'Green', swatch: 'var(--green-600)' },
    { name: 'theme-amber', label: 'Amber', swatch: 'var(--amber-600)' },
  ];
  const STORAGE_KEY = 'nostalgia:theme';

  const root = document.documentElement;
  const body = document.body;

  const getThemeClasses = el => Array.from(el.classList).filter(c => c.startsWith('theme-'));
  const clearThemes = () => {
    getThemeClasses(root).forEach(c => root.classList.remove(c));
    getThemeClasses(body).forEach(c => body.classList.remove(c)); // legacy cleanup
  };

  const applyTheme = (theme) => {
    clearThemes();
    if (theme) root.classList.add(theme);
    localStorage.setItem(STORAGE_KEY, theme || '');
    // update pressed states
    document.querySelectorAll('#theme-switcher [data-theme]').forEach(btn => {
      btn.setAttribute('aria-pressed', btn.dataset.theme === theme ? 'true' : 'false');
    });
  };

  const saved = localStorage.getItem(STORAGE_KEY);
  const initial = THEMES.some(t => t.name === saved) ? saved : THEMES[0].name;
  applyTheme(initial);

  // Inject minimal styles
  const style = document.createElement('style');
  style.textContent = `
    #theme-switcher{
      position:absolute; inset-block-start:.75rem; inset-inline-end:.75rem; z-index:9999;
      display:flex; gap:.4rem; padding:.35rem .45rem;
      background: color-mix(in oklab, var(--bg, #fff) 88%, black 0%);
      border:1px solid var(--border, #d1d5db);
      border-radius:.75rem; box-shadow: var(--shadow-2, 0 3px 5px rgb(0 0 0 / .08));
      backdrop-filter:saturate(1.2) blur(2px);
    }
    #theme-switcher button{
      appearance:none; border:0; background:none; padding:.15rem; margin:0; cursor:pointer;
      inline-size:22px; block-size:22px; display:grid; place-items:center; border-radius:999px;
      outline: none;
    }
    #theme-switcher button:focus-visible{
      box-shadow: var(--focus-ring, 0 0 0 4px rgb(56 189 248 / .55));
    }
    #theme-switcher .dot{
      inline-size:14px; block-size:14px; border-radius:999px; box-shadow: inset 0 0 0 1px rgb(0 0 0 / .15);
    }
    #theme-switcher [aria-pressed="true"] .dot{
      box-shadow:
        0 0 0 2px var(--bg, #fff),
        0 0 0 4px var(--accent, currentColor);
    }
    #theme-switcher .label{
      position:absolute; clip:rect(0 0 0 0); inline-size:1px; block-size:1px; margin:-1px; overflow:hidden;
    }
    @media print { #theme-switcher{ display:none } }
  `;
  document.head.appendChild(style);

  // Build palette
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
    dot.style.background = t.swatch; // swatch uses your palette vars
    btn.appendChild(dot);

    const sr = document.createElement('span');
    sr.className = 'label';
    sr.textContent = t.label;
    btn.appendChild(sr);

    btn.addEventListener('click', () => applyTheme(t.name));
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
        e.preventDefault();
        const idx = THEMES.findIndex(x => x.name === (getThemeClasses(root)[0] || initial));
        const next = e.key === 'ArrowRight'
          ? THEMES[(idx + 1) % THEMES.length].name
          : THEMES[(idx - 1 + THEMES.length) % THEMES.length].name;
        applyTheme(next);
        // move focus to newly active swatch
        const activeBtn = wrap.querySelector(`[data-theme="${next}"]`);
        activeBtn?.focus();
      }
    });

    wrap.appendChild(btn);
  });

  document.body.appendChild(wrap);
})();