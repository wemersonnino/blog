<?php
/**
 * Template Name: calculadoras-front
 * Template Post Type: page
 */
?>
<?php get_header(); ?>
<?php get_template_part('/partials/topos/padrao'); ?><!--//header-->
<style>
    #content-post-calculadora{
        padding: 0 2.5rem 0 0;
    }
</style>
<main id="main-container">
    <section class="container d-flex d-block">
        <article class="row justify-content-md-center">
            <section id="content-post" class="col col-md-8 col-sm-12">
                <article class="row">
                    <div class="ps-2">

                <div id="titles">
                    <h1 class="titulo text-capitalize fw-semibold lh-base mb-0 mb-md-3 mb-lg-3 mb-xl-3">
                        <?php echo the_title()?>
                    </h1><!--\title-->
                    <div class="w-100"></div>
                    <?php get_template_part('/partials/templates/page/conteudo-calculadoras'); ?><!--//main-->

                    <?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>

                    <?php if (get_field('/page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

                    <?php get_template_part('/partials/rodapes/padrao'); ?>

                    <?php get_footer(); ?>



