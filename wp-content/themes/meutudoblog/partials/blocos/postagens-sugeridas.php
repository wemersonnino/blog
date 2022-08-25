<?php $sugeridos = get_field('postagens-sugeridas-postagens') ? array_slice(get_field('postagens-sugeridas-postagens'), 0, 4) : null; ?>
<?php $categorias = array_map(function($categoria) { return $categoria->term_id; }, get_the_terms(get_the_ID(), 'category')); ?>

<?php $bloco = new WP_Query(array(
  'post_type' => 'bloco',
  'name' => 'bloco-postagens-sugeridas',
  'posts_per_page' => 1
)); ?>
<?php if($bloco->have_posts()) : $bloco->the_post(); ?>

  <?php if ($sugeridos) : ?>
    <?php $posts_sugeridos = new WP_Query(array(
      'post_type' => 'post',
      'orderby' => 'post__in',
      'post__in' => $sugeridos,
      'posts_per_page' => 4
    )); ?>
  <?php endif; ?>
  <?php $posts_categorias = new WP_Query(array(
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'category__in' => $categorias,
    'posts_per_page' => 4 - ($sugeridos ? count($sugeridos) : 0)
  )); ?>

  <?php if (($sugeridos ? $posts_sugeridos->post_count : 0) + $posts_categorias->post_count > 0) : ?>

    <section class="bloco-postagens-sugeridas">
      <div class="container">
        <div class="navegacao">
          <button class="botao-anterior" type="button">
            <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><g transform="rotate(180 15.5 15)"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></g></svg>
          </button>
          <h2 class="titulo"><?php the_field('titulo'); ?></h2>
          <button class="botao-proximo" type="button">
            <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31 30"><ellipse cx="15.5" cy="15" rx="15.5" ry="15" fill="rgba(210,38,136,0.1)"/><path d="M12.822 8.714a1.111 1.111 0 0 1 1.541-1.6l7.224 6.941a1.111 1.111 0 0 1 0 1.6l-7.224 6.948a1.112 1.112 0 1 1-1.541-1.6l6.391-6.147z" fill="#d22688"/></svg>
          </button>
        </div>

        <div class="posts">
          <div class="swiper-container">
            <div class="swiper-wrapper">
              <?php if ($sugeridos) : ?>
                <?php if($posts_sugeridos->have_posts()) : ?>
                  <?php while($posts_sugeridos->have_posts()) : $posts_sugeridos->the_post(); ?>
                    <div class="swiper-slide"><?php get_template_part('partials/items/post', 'card-mini'); ?></div>
                  <?php endwhile; wp_reset_postdata(); ?>
                <?php endif; ?>
              <?php endif; ?>
              
              <?php if($posts_categorias->have_posts()) : ?>
                <?php while($posts_categorias->have_posts()) : $posts_categorias->the_post(); ?>
                  <div class="swiper-slide"><?php get_template_part('partials/items/post', 'card-mini'); ?></div>
                <?php endwhile; wp_reset_postdata(); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script>
      window.addEventListener('DOMContentLoaded', function() {
        jQuery(function($) {
          var swiper = new Swiper('section.bloco-postagens-sugeridas .swiper-container', {
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

          $('section.bloco-postagens-sugeridas .botao-anterior').click(function(e) {
            e.preventDefault();
            swiper.slidePrev();
          });
          $('section.bloco-postagens-sugeridas .botao-proximo').click(function(e) {
            e.preventDefault();
            swiper.slideNext();
          });
        });
      });
    </script>

  <?php endif; ?>

<?php endif; wp_reset_postdata(); ?>