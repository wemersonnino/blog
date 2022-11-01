<?php
/**
 * Template Name: calculadoras-front
 * Template Post Type: page
 */
?>
<?php get_header(); ?>
<?php get_template_part('/partials/topos/padrao'); ?><!--//header-->
<main id="main-container" class="container">
    <section class="container">
        <article class="row justify-content-between">
            <div class="w-100"></div>
            <section id="content-post" class="post-conteudo col-md-8 col-lg-8 col-xl-8 w-100 d-flex">
                <div id="titles" class="row align-items-center">
                    <h1 class="titulo text-capitalize fw-semibold lh-base mb-0 mb-md-3 mb-lg-3 mb-xl-3">
                        <?php echo the_title()?>
                    </h1><!--\title-->
                    <div class="w-100"></div>
                    <?php get_template_part('/partials/templates/page/conteudo-calculadoras'); ?><!--//main-->

                    <?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>

                    <?php if (get_field('/page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

                    <?php get_template_part('/partials/rodapes/padrao'); ?>

                    <?php get_footer(); ?>



