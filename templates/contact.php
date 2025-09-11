<!doctype html>
<html lang="en">

<?php include path('partials') . '/head.php'; ?>

<body class="contact">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <main>
      <?php if (!empty($hero_html)): ?>
        <?= $hero_html ?>
      <?php endif; ?>
      <section id="page-content">
        <div class="outer">
          <div class="inner" style="--inner-padding-block: var(--size-3);">
            <div class="content">
              <h1>Contact Us</h1>
              <p>We’d love to hear from you. Fill out the form below:</p>
              <?php include path('partials') . '/contact-form.php'; ?>
            </div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
</body>

</html>