<?php include path('partials') . '/head.php'; ?>

<body class="layout-sidebar">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <?php include path('partials') . '/breadcrumbs.php'; ?>
      <div class="outer">
        <?php if ($isItem): ?>
          <div class="inner" style="--inner-padding-block: var(--size-1-5)">
          <?php else: ?>
            <div class="inner">
            <?php endif; ?>
            <div class="content content-sidebar">
              <div>
                <?php if (is_collection_item_meta($meta)): ?>
                  <div class="post-meta">
                    <?= meta_date_tag($meta, 'M j, Y') ?>
                  </div>
                <?php endif; ?>
                <section class="content flow">
                  <?= $content ?>
                </section>
                <?php if (is_collection_item_meta($meta)): ?>
                  <?php $tags = render_tags($meta); ?>
                  <?php if (!empty($tags)): ?>
                    <hr />
                    <h3>Tagged:</h3>
                    <?= $tags ?>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <?php include path('partials') . '/sidebar.php'; ?>
            </div>
          </div>
        </div>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>