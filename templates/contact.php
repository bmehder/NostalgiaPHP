<?php include path('partials') . '/head.php'; ?>

<body class="contact">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <section>
        <div class="outer">
          <div class="inner">
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