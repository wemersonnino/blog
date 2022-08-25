<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "9433840641e6ce07f8bdcff4771a0c078bc01486a1" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/single-post.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/single-post_2021-10-18-03-04-36.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<?php get_template_part('partials/templates/post/conteudo'); ?>

<?php get_template_part('partials/templates/post/comentarios'); ?>

<?php get_template_part('partials/blocos/newsletter'); ?>

<?php get_template_part('partials/blocos/postagens-sugeridas'); ?>

<?php get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_template_part('partials/blocos/compartilhar-lateral'); ?>

<?php get_footer(); ?>