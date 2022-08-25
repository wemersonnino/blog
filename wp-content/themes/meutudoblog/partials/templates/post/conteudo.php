<?php $categorias = implode(', ', array_map(function($category) {
  return '<a href="' . esc_url(home_url('/categoria/' . $category->slug . '/')) . '" class="categoria">' . $category->name . '</a>';
}, get_the_terms(get_the_ID(), 'category'))); ?>

<section class="post-conteudo">
  <div class="container">
    <span class="categorias"><?php echo $categorias; ?></span>
    <h1 class="nome"><?php the_title(); ?></h1>
    <span class="detalhes"><span class="author"><?php echo get_the_author(); ?></span> em <span class="data"><?php echo get_the_date(); ?></span> às <span class="horario"><?php echo get_the_time(); ?></span></span>

    <?php if(has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) : ?>
      <img src="<?php the_post_thumbnail_url('postagem-1200x675'); ?>" alt="<?php echo get_the_title($post); ?>" width="1200" height="675" class="imagem-destacada">
    <?php else : ?>
      <img src="https://dummyimage.com/1200x675/eeeeee/cccccc" alt="<?php echo get_the_title($post); ?>" width="1200" height="675" class="imagem-destacada">
    <?php endif; ?>

    <div class="conteudo-wysiwyg">
      <?php the_content(); ?>
    </div>
  </div>
</section>
