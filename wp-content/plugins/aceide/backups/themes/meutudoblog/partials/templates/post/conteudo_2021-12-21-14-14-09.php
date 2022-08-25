<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "3b02b8d09ade54f37356fb8b291a3fa13353eb884c" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/templates/post/conteudo.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/templates/post/conteudo_2021-12-21-14-14-09.php" ) ) ) ) {
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
    <span class="data"><?php echo get_the_date(); ?></span>

    <?php if(has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) : ?>
      <img src="<?php the_post_thumbnail_url('postagem-1200x675'); ?>" alt="<?php echo get_the_title($post); ?>" class="imagem-destacada">
    <?php else : ?>
      <img src="https://dummyimage.com/1200x675/eeeeee/cccccc" alt="<?php echo get_the_title($post); ?>" class="imagem-destacada">
    <?php endif; ?>

    <div class="conteudo-wysiwyg">
      <?php the_content(); ?>
    </div>
  </div>
</section>
