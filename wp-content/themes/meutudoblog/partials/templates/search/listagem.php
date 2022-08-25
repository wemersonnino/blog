<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>

<?php $posts = new WP_Query(array(
  'post_type' => 'post',
  'orderby' => 'date',
  'order' => 'DESC',
  'posts_per_page' => 9,
  's' => get_search_query(),
  'paged' => $paged,
  'relevanssi' => true
)); ?>
<?php if($posts->have_posts()) : ?>
  <section class="search-listagem">
    <div class="container">
      <h1 class="titulo"><?php echo sprintf(__('Post\'s relacionados a <span>"%s"</span>', 'meutudoblog'), get_search_query()); ?></h1>

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
    </div>
  </section>
<?php else : ?>
  <section class="search-listagem sem-resultados">
    <div class="container">
      <h1 class="titulo"><?php echo sprintf(__('Ainda não possuímos nenhum conteúdo relacionado a <span>"%s"</span>', 'meutudoblog'), get_search_query()); ?></h1>
      <img src="<?php echo get_template_directory_uri(); ?>/images/search-listagem-sem-resultados.png" alt="<?php _e('Não encontrado', 'meutudoblog'); ?>" class="imagem">
    </div>
  </section>
<?php endif; ?>