<?php
/*
 Template Name: calculadoras-front
 */
?>
<?php get_header(); ?>
<?php get_template_part('/partials/topos/padrao'); ?><!--//header-->
<main id="main-container 2">
    <section class="container">
        <article class="row justify-content-start">
            <div class="w-100"></div><br><br>
            <section id="content-post" class="post-conteudo col-md-8 col-lg-8 col-xl-8 w-100">
                <div class="row align-items-center">
                    <h1 class="title-nome nome">
                        <?php echo the_title()?>
                    </h1><!--\title-->
                    <div class="w-100"></div><br><br>
                    <?php get_template_part('/partials/templates/page/conteudo-calculadoras'); ?><!--//main-->

                    <?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>

                    <?php if (get_field('/page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

                    <?php get_template_part('/partials/rodapes/padrao'); ?>

                    <?php get_footer(); ?>



