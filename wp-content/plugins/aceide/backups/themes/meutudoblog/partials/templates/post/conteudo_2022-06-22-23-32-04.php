<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "e081ca82e9b081eba5378232f5c8c1047546d19c38" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/templates/post/conteudo.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/templates/post/conteudo_2022-06-22-23-32-04.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php $categorias = implode(', ', array_map(function($category) {
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
