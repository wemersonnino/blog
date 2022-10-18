<?php
/*
Template Name: Mapa do Site
*/

// Get acf
$blocks = get_field('blocks') ?? [];

?>

<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<section class="page-conteudo">
    <div class="container">
        <?php if (get_field('page-mostrar-titulo')) { ?>
            <h1 class="titulo"><?php the_title(); ?></h1>
        <?php } ?>
        <div class="conteudo-wysiwyg">
            <div class="row">
                <?php foreach ($blocks as $block) { ?>
                    <div class="col-12 col-lg-6 mb-4">
                        <h2 class="has-medium-font-size"><?= $block['title'] ?></h2>
                        <p>
                            <?php foreach ($block['links'] as $link) { ?>
                                <a href="<?= $link['url'] ?>" <?= (strpos($link['url'], 'mapa-do-site') !== false ? null : 'target="_blank" rel="noreferrer noopener"') ?>>
                                    <?= $link['label'] ?>
                                </a><br>
                            <?php } ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<?php if (get_field('page-newsletter-habilitado')) get_template_part('partials/blocos/newsletter'); ?>

<?php if (get_field('page-baixe-o-aplicativo-habilitado')) get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>