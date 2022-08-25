<section class="home-sugeridos sugeridos-1">
  <div class="container">
    <div class="navegacao">
      <button class="botao-anterior" type="button">
        <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><g transform="rotate(180 15.5 15)"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></g></svg>
      </button>
      <h2 class="titulo"><?php the_field('sugeridos-1-titulo'); ?></h2>
      <button class="botao-proximo" type="button">
        <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></svg>
      </button>
    </div>

    <div class="posts">
      <div class="swiper-container">
        <div class="swiper-wrapper">
          <?php $sugeridos = get_field('sugeridos-1-postagens') ? array_slice(get_field('sugeridos-1-postagens'), 0, 4) : null; ?>
          <?php if ($sugeridos) : ?>
            <?php $posts = new WP_Query(array(
              'post_type' => 'post',
              'orderby' => 'post__in',
              'post__in' => $sugeridos,
              'posts_per_page' => 4
            )); ?>
            <?php if($posts->have_posts()) : ?>
              <?php while($posts->have_posts()) : $posts->the_post(); ?>
                <div class="swiper-slide"><?php get_template_part('partials/items/post', 'card-mini'); ?></div>
              <?php endwhile; wp_reset_postdata(); ?>
            <?php endif; ?>
          <?php endif; ?>
          <?php $postsLeft = 4 - ($sugeridos ? count($sugeridos) : 0); if ($postsLeft > 0) : ?>
            <?php $posts = new WP_Query(array(
              'post_type' => 'post',
              'orderby' => 'date',
              'order' => 'DESC',
              'category__in' => get_field('sugeridos-1-categorias'),
              'posts_per_page' => $postsLeft
            )); ?>
            <?php if($posts->have_posts()) : ?>
              <?php while($posts->have_posts()) : $posts->the_post(); ?>
                <div class="swiper-slide"><?php get_template_part('partials/items/post', 'card-mini'); ?></div>
              <?php endwhile; wp_reset_postdata(); ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  window.addEventListener('DOMContentLoaded', function() {
    jQuery(function($) {
      var swiper = new Swiper('section.home-sugeridos.sugeridos-1 .swiper-container', {
        slidesPerView: 1.5,
        spaceBetween: 30,
        simulateTouch: false,
        slidesOffsetAfter: 15,
        slidesOffsetBefore: 15,
        breakpoints: {
          992: {
            slidesPerView: 4,
            slidesOffsetAfter: 0,
            slidesOffsetBefore: 0
          },
          576: {
            slidesPerView: 2,
            slidesOffsetAfter: 0,
            slidesOffsetBefore: 0
          },
          320: {
            slidesPerView: 1.5
          }
        }
      });

      $('section.home-sugeridos.sugeridos-1 .botao-anterior').click(function(e) {
        e.preventDefault();
        swiper.slidePrev();
      });
      $('section.home-sugeridos.sugeridos-1 .botao-proximo').click(function(e) {
        e.preventDefault();
        swiper.slideNext();
      });
    });
  });
</script>