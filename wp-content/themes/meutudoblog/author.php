<?php

// Get author
$author = isset($_GET['author_name']) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

?>
<?php get_header(); ?>

<?php get_template_part('partials/topos/padrao'); ?>

<!-- author content -->
<div class="theme-author container mt-2 mb-4">
    <?php get_template_part('partials/author/profile', null, ['author' => $author]); ?>
    
    <?php get_template_part('partials/author/posts', null, ['author' => $author]); ?>
</div>
<!-- /author content -->

<?php get_template_part('partials/blocos/newsletter'); ?>

<?php get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_footer(); ?>