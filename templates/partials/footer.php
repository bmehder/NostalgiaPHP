<?php include path('partials') . '/env-badge.php'; ?>
<footer>
  <div class="outer">
    <div class="inner">
      <div class="content spread-apart">
        &copy; <?= date('Y') ?> <?= htmlspecialchars(site('name')) ?>. All rights reserved.
        <a href="#top" data-back-to-top>⬆ Back to Top</a>
        <script>
          document.addEventListener('DOMContentLoaded', () => {

            document.querySelector('[data-back-to-top]').addEventListener('click', e => {
              e.preventDefault()
              window.scrollTo({ top: 0 })
            })
          })
        </script>
      </div>
    </div>
  </div>
</footer>

</body>

</html>