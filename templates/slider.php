<?php include path('partials') . '/head.php'; ?>

<style>
  .inner.slider {
    --inner-padding-block: 0 var(--size-3);
    min-inline-size: 100%;
    display: grid;

    .carousel {
      .card {
        min-inline-size: 100%;
        aspect-ratio: 18/8;
        border-radius: 0px;
      }

      &::scroll-button(left) {
        translate: 110%;
      }

      &::scroll-button(right) {
        translate: -110%;
      }
    }
  }
</style>

<body class="full-width">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <section>
      <div class="outer">
        <div class="inner slider">
          <div class="carousel">
            <div class="card"><img src="https://picsum.photos/1200/533?random=1" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=2" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=3" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=4" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=5" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=6" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=7" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=8" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=9" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=10" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=11" width="1200" height="533" alt /></div>
            <div class="card"><img src="https://picsum.photos/1200/533?random=12" width="1200" height="533" alt /></div>
          </div>
        </div>
      </div>
    </section>
    <main id="main">
      <section>
        <div class="outer">
          <div class="inner">
            <div class="content flow">
              <h1>CSS-only Slider</h1>
              <p>
                This is the same component as the carousel on the <a href="about/components">components page</a> but with the following scoped css
                changes.
              </p>
              
              <pre><code class="language-javascript">main .inner {
  --inner-padding-block: 0 var(--size-3);

  min-inline-size: 100%;
  display: grid;
  
  .carousel {
    .card {
      min-inline-size: 100%;
      aspect-ratio: 18/7;
      border-radius: 0px;
    }
  
    &::scroll-button(left) {
      translate: 110%;
    }
  
    &::scroll-button(right) {
      translate: -110%;
    }
  }
}</code></pre>
            </div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>