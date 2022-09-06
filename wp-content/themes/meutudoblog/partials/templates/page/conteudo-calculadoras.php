<?php
$term = get_queried_object();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts = new WP_Query(array(
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 3,
    'category_name' => 'calculadoras'
));
?>
<?php if($posts->have_posts()) : ?>
                <section id="post" class="posts">
                    <div class="row align-content-start">

                        <?php while($posts->have_posts()) : $posts->the_post(); ?>
                        <?php $categorias = array_map(function($object) { return $object->name; }, get_the_terms(get_the_ID(), 'category')); ?>

                        <div class="col-12 col-sm-6 col-lg-4">
                            <?php get_template_part('partials/items/post', 'card'); ?>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?><!--\Box Post Calc-->

                    </div>
                </section><!--\Posts-->
                <?php if($posts->max_num_pages > 1) : ?>
                    <div class="paginacao">
                        <?php echo paginate_links(array(
                            'current' => $paged,
                            'total' => $posts->max_num_pages,
                            'prev_text' => '',
                            'next_text' => ''
                        )); ?>
                    </div>
                <?php endif; ?>
<?php endif; ?>
            </section>
        </article>
    </section>
</main>