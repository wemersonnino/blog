<?php
/*
Template Name: Ferramentas > Tabelas
*/

// Infos
$page = get_queried_object();
$paged = get_query_var('paged') ?? 1; 

// Get child pages
$posts = new WP_Query([
    'post_type' => 'page',
    'post_parent' => $page->ID,
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 9,
    'paged' => $paged
]);

?>

<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<section class="category-listagem">
    <div class="container">
        <?php if (get_field('page-mostrar-titulo')) { ?>
            <h1 class="titulo"><?php the_title(); ?></h1>
        <?php } ?>

        <?php if ($posts->have_posts()) { ?>
            <div class="posts">
                <div class="row">
                    <?php while ($posts->have_posts()) { $posts->the_post() ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <?php get_template_part('partials/items/post', 'card'); ?>
                        </div>
                    <?php } wp_reset_postdata() ?>
                </div>
            </div>

            <?php if ($posts->max_num_pages > 1) { ?>
                <div class="paginacao">
                    <?= paginate_links([
                        'current' => $paged,
                        'total' => $posts->max_num_pages,
                        'prev_text' => '',
                        'next_text' => ''
                    ]) ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php if (get_field('page-newsletter-habilitado')) get_template_part('partials/blocos/newsletter'); ?>

<?php if (get_field('page-baixe-o-aplicativo-habilitado')) get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>