<?php $paged = (get_query_var('page')) ? get_query_var('page') : 1; ?>

<section class="home-listagem">
  <div class="container">
    <h2 class="titulo"><?php the_field('listagem-titulo'); ?></h2>

    <?php $posts = new WP_Query(array(
      'post_type' => 'post',
      'orderby' => 'date',
      'order' => 'DESC',
      'posts_per_page' => get_option('posts_per_page'),
      'paged' => $paged
    )); ?>
    <?php if($posts->have_posts()) : ?>
      <div class="posts">
        <div class="row">
          <?php while($posts->have_posts()) : $posts->the_post(); ?>
            <?php $categorias = array_map(function($object) { return $object->name; }, get_the_terms(get_the_ID(), 'category')); ?>

            <div class="col-12 col-sm-6 col-lg-4">
              <?php get_template_part('partials/items/post', 'card'); ?>
            </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>

      <?php if($posts->max_num_pages > 1) : ?>
        <div class="paginacao">
          <?php echo paginate_links(array(
            'current' => $paged,
            'total' => $posts->max_num_pages,
            'prev_text' => '',
            'next_text' => ''
          )); ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>