<?php $siteName = htmlspecialchars(site('name')); ?>
<!doctype html>
<html lang="en">

<?php include path('partials') . '/head.php'; ?>

<body>
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <main>
      <?php if (!empty($hero_html)): ?>
        <?= $hero_html ?>
      <?php endif; ?>
      <section id="page-content">
        <div class="outer">
          <div class="inner" style="--inner-padding-block: var(--size-3);">
            <div class="content"><?= $content ?></div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
  <script>
    (function () {
      const btn = document.querySelector('[data-nav-toggle]')
      const nav = document.querySelector('[data-site-nav]')
      const inner = document.querySelector('[data-inner-header]')

      if (!btn || !nav) return

      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true'
        btn.setAttribute('aria-expanded', String(!expanded))
        nav.setAttribute('aria-expanded', String(!expanded))
        inner && inner.classList.toggle('nav-open', !expanded)
      })
    }())
  </script>
</body>

</html>