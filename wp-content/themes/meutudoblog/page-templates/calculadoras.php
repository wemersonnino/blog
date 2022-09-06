<?php
/*
 Template Name: calculadoras
 */
?>
<?php get_header(); ?>
<?php get_template_part('/partials/topos/padrao'); ?><!--//header-->
<main id="main-container">
    <section class="container">
        <article class="row align-content-start">
            <section class="container-posts category-listagem">
                <article id="titles" class="row align-items-center">
                    <h1 class="titulo">
                        <?php the_title() ?>
                    </h1>
                </article><!--\titulos-->
<?php get_template_part('/partials/templates/page/conteudo-calculadoras'); ?><!--//main-->

<?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>

<?php if (get_field('/page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('/partials/rodapes/padrao'); ?>

<?php get_footer(); ?>

