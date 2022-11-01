<?php

// Get author infos
$authorId = (int) $post->post_author;
$authorPhoto = get_field('photo_posts', 'user_' . $authorId) ?? null;
$authorIcon = get_field('icon', 'user_' . $authorId) ?? null;

?>

<div class="theme-author">
    <div class="profile-post row align-items-center">
        <?php if ($authorPhoto) { ?>
            <div class="col-12 col-md-3 text-center">
                <a href="<?= get_author_posts_url($authorId) ?>" aria-label="Ver perfil de <?= get_the_author_meta('display_name') ?>">
                    <img src="<?= $authorPhoto ?>" class="w-100" alt="<?= get_the_author_meta('display_name') ?>" />
                </a>
            </div>
        <?php } ?>
        <div class="col-12 <?= ($authorPhoto ? 'col-md-9' : 'col-md-12') ?>">
            <span class="font-weight-bold mb-2">
                <?= get_the_author_meta('display_name') ?>
                <?php if ($authorIcon) { ?>
                    <img src="<?= $authorIcon ?>" alt="<?= get_the_author_meta('display_name') ?>" />
                <?php } ?>  
            </span>
            <p class="font-weight-medium mb-3">
                <?= get_the_author_meta('description') ?>
            </p>
            <a href="<?= get_author_posts_url($authorId) ?>" class="font-weight-bold" aria-label="Ver perfil de <?= get_the_author_meta('display_name') ?>">
                <?= count_user_posts($authorId) ?> artigos escritos
            </a>
        </div>
    </div>
</div>