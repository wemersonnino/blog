<?php /* Template name: Home */ ?>
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