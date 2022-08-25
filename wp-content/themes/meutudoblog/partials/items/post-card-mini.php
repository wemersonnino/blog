<?php $categoria = get_primary_taxonomy_term(); ?>

<a href="<?php the_permalink(); ?>" class="post-card mini">
  <?php if(has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) : ?>
    <img src="<?php the_post_thumbnail_url('postagem-400x280'); ?>" alt="<?php echo get_the_title($post); ?>" class="imagem" width="400" height="280">
  <?php else : ?>
    <img src="https://dummyimage.com/400x280/eeeeee/cccccc" alt="<?php echo get_the_title($post); ?>" class="imagem" width="400" height="280">
  <?php endif; ?>
  <?php if($categoria) : ?><span class="categoria"><?php echo $categoria['title']; ?></span><?php endif; ?>
  <h3 class="nome"><?php the_title(); ?></h3>
  <span class="ler-mais">Leia mais</span>
</a>