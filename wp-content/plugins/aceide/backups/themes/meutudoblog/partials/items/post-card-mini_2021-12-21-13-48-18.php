<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "3b02b8d09ade54f37356fb8b291a3fa13353eb884c" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/items/post-card-mini.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/items/post-card-mini_2021-12-21-13-48-18.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php $categoria = get_primary_taxonomy_term(); ?>

<a href="<?php the_permalink(); ?>" class="post-card mini">
  <?php if(has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) : ?>
    <img src="<?php the_post_thumbnail_url('postagem-400x280'); ?>" alt="<?php echo get_the_title($post); ?>" class="imagem">
  <?php else : ?>
    <img src="https://dummyimage.com/400x280/eeeeee/cccccc" alt="<?php echo get_the_title($post); ?>" class="imagem">
  <?php endif; ?>
  <?php if($categoria) : ?><span class="categoria"><?php echo $categoria['title']; ?></span><?php endif; ?>
  <h3 class="nome"><?php the_title(); ?></h3>
  <span class="ler-mais">Leia mais</span>
</a>