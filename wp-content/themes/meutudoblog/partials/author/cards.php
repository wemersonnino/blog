<?php

// Get users
$authors = get_users([
    #'fields' => ['ID', 'display_name'],
    'role__in'    => ['author', 'administrator'],
    'orderby' => 'display_name'
]);

?>
<?php foreach ($authors as $author) { ?>
    <?php

    // Only administrador "meutudo" show
    if (isset($author->roles[0]) && $author->roles[0] == 'administrator' && $author->user_login != 'MEU TUDO') 
        continue;

    // Images
    $authorPhoto = get_field('photo_profile', 'user_' . $author->ID) ?? null;
    $authorIcon = get_field('icon', 'user_' . $author->ID) ?? null;

    ?>
    <div class="col-12 col-md-6 col-lg-4 mb-5">
        <div class="card-author">
            <div class="infos row">
                <?php if ($authorPhoto) { ?>
                    <div class="photo col-5 p-0">
                        <img src="<?= $authorPhoto ?>" alt="<?= $author->display_name ?>" />
                    </div>
                <?php } ?>
                <div class="about <?= ($authorPhoto ? 'col-7' : 'col-12') ?>">
                    <?php if ($authorIcon) { ?>
                        <img src="<?= $authorIcon ?>" alt="<?= $author->display_name ?>" />
                    <?php } ?>
                    <h2 class="font-weight-bold"><?= $author->display_name ?></h2>
                    <p class="mb-2">Lorem Ipsum</p>
                </div>
            </div>
            <div class="links p-4 text-right">
                <a href="<?= get_author_posts_url($author->ID) ?>" class="font-weight-bold" aria-label="Ver perfil de <?= $author->display_name ?>">
                    Ver perfil &rsaquo;
                </a>
            </div>
        </div>
    </div>
<?php } ?>