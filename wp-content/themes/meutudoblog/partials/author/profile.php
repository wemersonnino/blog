<?php

// Get author
$author = $args['author'];
$authorPhoto = get_field('photo_profile', 'user_' . $author->ID) ?? null;
$authorIcon = get_field('icon', 'user_' . $author->ID) ?? null;

?>
<div class="profile row align-items-center">
    <?php if ($authorPhoto) { ?>
        <div class="col-12 col-lg-4 mb-3 mb-lg-0">
            <img src="<?= $authorPhoto ?>" alt="<?= $author->user_firstname . ' ' . $author->user_lastname ?>" class="w-100" />
        </div>
    <?php } ?>
    <div class="col-12 <?= ($authorPhoto ? 'col-lg-8' : 'col-lg-12') ?>">
        <h1 class="font-weight-bold mb-2">
            <?= $author->user_firstname . ' ' . $author->user_lastname ?>
            <?php if ($authorIcon) { ?>
                <img src="<?= $authorIcon ?>" alt="<?= $author->user_firstname . ' ' . $author->user_lastname ?>" />
            <?php } ?>
        </h1>
        <p class="font-weight-medium mb-0"><?= $author->user_description; ?></p>
        <div class="infos d-flex justify-content-between align-items-center mt-4">
            <span><?= count_user_posts($author->ID, 'post', true) ?> artigos escritos</span>
        </div>
    </div>
</div>