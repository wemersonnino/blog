<?php

// Infos
$page = get_queried_object();
$pageId = (int) $page->ID;

// Get pages by IDs
$listPagesId = get_field('table_pages', $pageId) ?? [];
$listPages = !empty($listPagesId) ? new WP_Query([
    'post__in' => $listPagesId,
    'post_type' => 'page',
    'orderby' => 'date',
    'order' => 'DESC'
]) : null;

// Get posts by IDs
$listPostsId = get_field('table_posts', $pageId) ?? [];
$listPosts = !empty($listPostsId) ? new WP_Query([
    'post__in' => $listPostsId,
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC'
]) : null;

// Get default posts
if (empty($listPosts)) $listPosts = new WP_Query([
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 3
]);

// Get footer informations
$footerBlock = new WP_Query([
    'post_type' => 'bloco',
    'name' => 'rodape-padrao',
    'posts_per_page' => 1
]);

?>
<div class="sidebar">

    <!-- dynamic sidebar -->
    <ul class="dynamic-list">
        <?php dynamic_sidebar('sidebar_tables') ?>
    </ul>
    <!-- /dynamic sidebar -->

    <div class="fixed-list">
        
        <!-- list pages -->
        <?php if (!empty($listPages) && $listPages->have_posts()) { ?>
            <ul class="pages-list line position-relative p-0">
                <li class="title">Mais tabelas</li>
                <?php while ($listPages->have_posts()) { ?>
                    <?php $listPages->the_post() ?>
                    <li>
                        <a href="<?= get_the_permalink() ?>">
                            <h3 class="mb-0">
                                <?php if (has_post_thumbnail() && !empty(get_the_post_thumbnail_url())) { ?>
                                    <img src="<?php the_post_thumbnail_url('postagem-400x280'); ?>" alt="<?= get_the_title() ?>" class="imagem d-flex d-lg-none" width="100%">
                                <?php } else { ?>
                                    <img src="https://dummyimage.com/400x280/eeeeee/cccccc" alt="<?= get_the_title() ?>" class="imagem d-flex d-lg-none" width="100%">
                                <?php } ?>
                                <div class="d-flex flex-row align-items-center justify-content-start">
                                    <div class="icon"></div><div><?= get_the_title() ?></div>
                                </div>
                            </h3>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <!-- /list pages -->

        <!-- list posts -->
        <?php if (!empty($listPosts) && $listPosts->have_posts()) { ?>
            <ul class="posts-list line position-relative p-0">
                <?php while ($listPosts->have_posts()) { ?>
                    <?php $listPosts->the_post() ?>
                    <li class="px-2 px-lg-0">
                        <a href="<?= get_the_permalink() ?>" class="post-card">
                            <?php if (has_post_thumbnail() && !empty(get_the_post_thumbnail_url())) { ?>
                                <img src="<?php the_post_thumbnail_url('postagem-400x280'); ?>" alt="<?= get_the_title() ?>" class="imagem" width="400" height="280">
                            <?php } else { ?>
                                <img src="https://dummyimage.com/400x280/eeeeee/cccccc" alt="<?= get_the_title() ?>" class="imagem" width="400" height="280">
                            <?php } ?>
                            <h3><?= get_the_title() ?></h3>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <!-- /list posts -->

        <!-- list sociais -->
        <?php if ($footerBlock->have_posts()) { ?>
            <?php $footerBlock->the_post() ?>
            <ul class="social-list p-0">
                <?php if (get_field('redes-sociais')['facebook']) { ?>
                    <li>
                        <a href="<?= get_field('redes-sociais')['facebook']['url'] ?>" target="<?= get_field('redes-sociais')['facebook']['target'] ?>" title="Facebook" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.475 42.876"><g id="Grupo_79" data-name="Grupo 79" transform="translate(-93.292 -905.598)"><ellipse id="Elipse_2" data-name="Elipse 2" cx="22.737" cy="21.438" rx="22.737" ry="21.438" transform="translate(93.292 905.598)" /><path id="Caminho_19" data-name="Caminho 19" d="M91.928,707.036a3.893,3.893,0,0,0-2.833,1.247,4.4,4.4,0,0,0-1.173,3.01v2.369H85.778a.2.2,0,0,0-.195.208V717a.2.2,0,0,0,.195.208h2.144v6.418a.2.2,0,0,0,.195.208h2.945a.2.2,0,0,0,.195-.208v-6.418H93.42a.2.2,0,0,0,.189-.157l.736-3.129a.205.205,0,0,0-.189-.259h-2.9v-2.369a.738.738,0,0,1,.2-.505.651.651,0,0,1,.475-.208H94.18a.2.2,0,0,0,.195-.208v-3.129a.2.2,0,0,0-.195-.208Z" transform="translate(25.357 211.602)" fill="currentColor" /></g></svg>
                        </a>
                    </li>
                <?php } ?>
                <?php if (get_field('redes-sociais')['instagram']) { ?>
                    <li>
                        <a href="<?= get_field('redes-sociais')['instagram']['url'] ?>" target="<?= get_field('redes-sociais')['instagram']['target'] ?>" title="Instagram" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.176 42.876"><g id="Grupo_78" data-name="Grupo 78" transform="translate(-38.722 -905.598)"><ellipse id="Elipse_1" data-name="Elipse 1" cx="22.088" cy="21.438" rx="22.088" ry="21.438" transform="translate(38.722 905.598)" /><g id="Grupo_76" data-name="Grupo 76"><path id="Caminho_16" data-name="Caminho 16" d="M47.351,711.292a3.523,3.523,0,1,0,3.695,3.518A3.61,3.61,0,0,0,47.351,711.292Z" transform="translate(12.809 212.876)" fill="currentColor" /><path id="Caminho_17" data-name="Caminho 17" d="M42.961,706.64a56.688,56.688,0,0,1,11.893,0,4.6,4.6,0,0,1,4.152,3.912,47.1,47.1,0,0,1,0,11.488,4.6,4.6,0,0,1-4.152,3.912,56.671,56.671,0,0,1-11.893,0,4.6,4.6,0,0,1-4.152-3.912,47.1,47.1,0,0,1,0-11.488A4.6,4.6,0,0,1,42.961,706.64Zm11.631,3.16a1.084,1.084,0,1,0,1.137,1.082A1.111,1.111,0,0,0,54.591,709.8Zm-11.085,6.5a5.407,5.407,0,1,1,5.4,5.143A5.276,5.276,0,0,1,43.507,716.3Z" transform="translate(11.253 211.39)" fill="currentColor" fill-rule="evenodd" /></g></g></svg>
                        </a>
                    </li>
                <?php } ?>
                <?php if (get_field('redes-sociais')['linkedin']) { ?>
                    <li>
                        <a href="<?= get_field('redes-sociais')['linkedin']['url'] ?>" target="<?= get_field('redes-sociais')['linkedin']['target'] ?>" title="LinkdIn" aria-label="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.475 42.876"><g id="Grupo_81" data-name="Grupo 81" transform="translate(-202.432 -905.598)"><ellipse id="Elipse_4" data-name="Elipse 4" cx="22.737" cy="21.438" rx="22.737" ry="21.438" transform="translate(202.432 905.598)" /><g id="Grupo_77" data-name="Grupo 77"><path id="Caminho_20" data-name="Caminho 20" d="M169.591,706.328a1.96,1.96,0,1,0,1.84,1.955A1.9,1.9,0,0,0,169.591,706.328Z" transform="translate(49.948 211.39)" fill="currentColor" /><path id="Caminho_21" data-name="Caminho 21" d="M167.858,710.578a.112.112,0,0,0-.108.116v11.964a.112.112,0,0,0,.108.116h3.465a.112.112,0,0,0,.108-.116V710.694a.112.112,0,0,0-.108-.116Z" transform="translate(49.948 212.662)" fill="currentColor" /><path id="Caminho_22" data-name="Caminho 22" d="M172.192,710.578a.113.113,0,0,0-.109.116v11.964a.113.113,0,0,0,.109.116h3.464a.113.113,0,0,0,.109-.116v-6.442a1.776,1.776,0,0,1,.476-1.22,1.558,1.558,0,0,1,2.3,0,1.782,1.782,0,0,1,.476,1.22v6.442a.112.112,0,0,0,.108.116h3.465a.112.112,0,0,0,.108-.116v-7.933a3.683,3.683,0,0,0-3.92-3.778,5.9,5.9,0,0,0-1.877.5l-1.132.515v-1.271a.113.113,0,0,0-.109-.116Z" transform="translate(51.245 212.662)" fill="currentColor" /></g></g></svg>
                        </a>
                    </li>
                <?php } ?>
                <?php if (get_field('redes-sociais')['youtube']) { ?>
                    <li>
                        <a href="<?= get_field('redes-sociais')['youtube']['url'] ?>" target="<?= get_field('redes-sociais')['youtube']['target'] ?>" title="YouTube" aria-label="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42.876 42.876"><g id="Grupo_80" data-name="Grupo 80" transform="translate(-149.161 -905.598)"><circle id="Elipse_3" data-name="Elipse 3" cx="21.438" cy="21.438" r="21.438" transform="translate(149.161 905.598)" /><path id="Caminho_18" data-name="Caminho 18" d="M129.033,708.505a59.082,59.082,0,0,1,9.224,0l2.062.161a2.507,2.507,0,0,1,2.278,2.088,26.029,26.029,0,0,1,0,8.588,2.507,2.507,0,0,1-2.278,2.088l-2.062.161a59.082,59.082,0,0,1-9.224,0l-2.063-.161a2.507,2.507,0,0,1-2.278-2.088,26.028,26.028,0,0,1,0-8.588,2.507,2.507,0,0,1,2.278-2.088Zm2.77,8.817v-4.547a.276.276,0,0,1,.418-.236l3.789,2.274a.275.275,0,0,1,0,.473l-3.789,2.274A.276.276,0,0,1,131.8,717.322Z" transform="translate(36.955 211.988)" fill="currentColor" fill-rule="evenodd" /></g></svg>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <!-- /list sociais -->

    </div>
</div>