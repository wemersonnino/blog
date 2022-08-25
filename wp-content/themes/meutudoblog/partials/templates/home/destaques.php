<section class="home-destaques">
  <div class="container">
    <div class="swiper-container">
      <div class="swiper-wrapper">
        <?php if (get_field('destaques-postagens')) : ?>
          <?php foreach (get_field('destaques-postagens') as $i => $post) : ?>
            <?php $categoria = get_primary_taxonomy_term($post); ?>

            <div class="swiper-slide">
              <a href="<?php the_permalink(); ?>" class="destaque-item">
                <div class="row align-items-end">
                  <div class="col-12 col-lg-auto">
                    <?php if(has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) : ?>
                      <img src="<?php the_post_thumbnail_url('postagem-460x330'); ?>" alt="<?php echo get_the_title($post); ?>" class="imagem" width="460" height="330">
                    <?php else : ?>
                      <img src="https://dummyimage.com/460x330/eeeeee/cccccc" alt="<?php echo get_the_title($post); ?>" class="imagem" width="460" height="330">
                    <?php endif; ?>
                  </div>
                  <div class="col-12 col-lg">
                    <?php if($categoria) : ?><span class="categoria"><?php echo $categoria['title']; ?></span><?php endif; ?>
                    <h2 class="nome"><?php echo get_the_title($post); ?></h2>
                    <p class="resumo"><?php echo get_the_excerpt($post); ?></p>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <?php wp_reset_query(); ?>
      </div>
      <div class="navigation">
        <div class="swiper-button-prev">
          <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><g transform="rotate(180 15.5 15)"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></g></svg>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next">
          <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></svg>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  window.addEventListener('DOMContentLoaded', function() {
    jQuery(function($) {
      var swiper = new Swiper('section.home-destaques .swiper-container', {
        loop: true,
        spaceBetween: 30,
        autoplay: {
          delay: 5000,
        },
        pagination: {
          el: '.swiper-pagination'
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    });
  });
</script>