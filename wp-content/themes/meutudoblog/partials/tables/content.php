<h1 class="nome"><?= get_the_title() ?></h1>

<!--<?php if (has_post_thumbnail($post) && !empty(get_the_post_thumbnail_url($post))) { ?>
    <img src="<?php the_post_thumbnail_url('postagem-1200x675'); ?>" alt="<?= get_the_title($post) ?>" width="1200" height="675" class="imagem-destacada">
<?php } else { ?>
    <img src="https://dummyimage.com/1200x675/eeeeee/cccccc" alt="<?= get_the_title($post) ?>" width="1200" height="675" class="imagem-destacada">
<?php } ?>-->

<div class="conteudo-wysiwyg">
    <?php the_content() ?>
</div>