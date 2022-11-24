<?php
/*
Template Name: Ferramentas > Tabela (visualização)
*/
?>

<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<div class="theme-tables my-2 container">
    <div class="row">
        <div class="col-12">
            <?php if (get_field('page-mostrar-titulo')) { ?>
                <h1 class="titulo"><?php the_title(); ?></h1>
            <?php } ?>
        </div>
    </div>
    <div class="row mt-3 mb-5">
        <?php get_template_part('partials/author/cards'); ?>
    </div>
</div>

<?php if (get_field('page-newsletter-habilitado')) get_template_part('partials/blocos/newsletter'); ?>

<?php if (get_field('page-baixe-o-aplicativo-habilitado')) get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>