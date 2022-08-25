<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "a37456eae3b8aca1c8f38e13c890481113fd613e80" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/template-home.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/template-home_2021-05-10-22-33-43.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php /* Template name: Home */ ?>
<?php $paged = (get_query_var('page')) ? get_query_var('page') : 1; ?>

<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<?php if ($paged == 1) get_template_part('partials/templates/home/destaques'); ?>

<?php get_template_part('partials/templates/home/listagem'); ?>

<?php if ($paged == 1) get_template_part('partials/templates/home/sugeridos-1'); ?>

<?php if ($paged == 1) get_template_part('partials/templates/home/sugeridos-2'); ?>

<?php get_template_part('partials/blocos/newsletter'); ?>

<?php get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php echo do_shortcode(get_field('geral-shortcode')); ?>

<?php get_footer(); ?>