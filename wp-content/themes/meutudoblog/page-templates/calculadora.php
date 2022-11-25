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
    main#main-container {
        display: flex !important;
        flex-direction: column;
        flex-wrap: wrap;
        flex-grow: initial;
        width: 100%;
        max-width: max-content;
    }
    #titles .titulo {
        margin: 0 auto 0 1rem;
        padding-bottom: 1rem;
    }
</style>
<main id="main-container">
    <section class="container">
        <article class="row">
            <section id="content-post" class="col-md-8 col-lg-8 col-xl-8 col-sm-12">
                <article class="row">
                    <div class="ps-2">

                <div id="titles">
                    <h1 class="titulo text-capitalize fw-semibold lh-base mb-0 mb-md-3 mb-lg-3 mb-xl-3">
                        <?php echo the_title()?>
                    </h1><!--\title-->
                    <div class="w-100"></div>
                    <?php get_template_part('/partials/templates/page/conteudo-calculadoras'); ?><!--//main-->

                    <?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>




