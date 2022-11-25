<?php
/*
Template Name: Ferramentas > Tabela (visualização)
*/

// Infos
$page = get_queried_object();
$pageId = (int) $page->ID;

// FAQs
$faq = [
    'habilitado' => get_field('habilitado', $pageId) ?? false,
    'perguntas' => get_field('perguntas', $pageId) ?? []
];

?>

<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<section class="tables-front post-conteudo">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-9">

                <!-- content -->
                <?php get_template_part('partials/tables/content'); ?>
                <!-- /content -->

                <!-- faq -->
                <?php if ($faq['habilitado']) { ?>
                    <div class="mt-4">
                        <?php get_template_part('partials/blocos/perguntas-frequentes', null, ['faq' => $faq]); ?>
                    </div>
                <?php } ?>
                <!-- /faq -->

            </div>
            <div class="col-12 col-lg-3">

                <!-- sidebar -->
                <?php get_template_part('partials/tables/sidebar'); ?>
                <!-- /sidebar -->
                
            </div>
        </div>
    </div>
</section>

<?php if (get_field('page-newsletter-habilitado', $pageId)) get_template_part('partials/blocos/newsletter'); ?>

<?php if (get_field('page-baixe-o-aplicativo-habilitado', $pageId)) get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>