<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<?php get_template_part('partials/templates/page/conteudo'); ?>

<?php if (get_field('page-newsletter-habilitado')) get_template_part('partials/blocos/newsletter'); ?>

<?php if (get_field('page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>