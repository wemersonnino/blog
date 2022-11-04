<div class="author-posts row mt-5">
    <div class="col-12 mb-5">
        <div class="subtitle d-flex align-items-center font-weight-semibold">Artigos escritos</div>
    </div>
    <div class="col-12">
        
        <?php if (have_posts()) { ?>
            
            <div class="row">
                <?php while (have_posts()) { ?>
                    <?php the_post(); ?>
                    
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="post-content mb-3 px-3 px-lg-0">
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?= get_the_title(); ?>">
                                <?php if (has_post_thumbnail() && !empty(get_the_post_thumbnail_url())) { ?>
                                    <img src="<?php the_post_thumbnail_url('postagem-400x280'); ?>" alt="<?= get_the_title() ?>" class="w-100">
                                <?php } else { ?>
                                    <img src="https://dummyimage.com/400x280/eeeeee/cccccc" alt="<?= get_the_title() ?>" class="w-100">
                                <?php } ?>
                            </a>
                            <h2>
                                <a href="<?php the_permalink() ?>" rel="bookmark" title="<?= get_the_title(); ?>">
                                    <?= get_the_title(); ?>
                                </a>
                            </h2>
                            <p class="font-weight-medium mb-0">
                                Por <span class="color-1"><?= $args['author']->user_firstname . ' ' . $args['author']->user_lastname ?></span> 
                                <span class="mx-2">&bull;</span> 
                                <?= get_the_time('d/m/Y') ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
                
                <div class="col-12 mt-4 mb-5">
                    <?php
                    global $wp_query;
                    echo paginate_links([
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $wp_query->max_num_pages,
                        'type'  => 'list',
                        'prev_text' => ' ',
                        'next_text' => ' ',
                        'mid_size' => 2
                    ]);
                    ?>
                </div>
            </div>

        <?php } else { ?>
            
            <p><small><i>Nenhum artigo encontrado.</i></small></p>

        <?php } ?>

    </div>
</div>