<?php $categorias = implode(', ', array_map(function($category) {
    return '<a href="' . esc_url(home_url('/categoria/' . $category->slug . '/')) . '" class="categoria">' . $category->name . '</a>';
}, get_the_terms(get_the_ID(), 'category'))); ?>
<?php

$posts = new WP_Query(array(
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 3,
    'category_name' => 'calculadoras',
));

?>

<main id="main-container">
    <section class="container">
        <article class="row align-content-start">
            <div class="w-100"></div>
            <section class="container-posts category-listagem">
                <article id="titles" class="row align-items-center">
                    <h1 class="titulo text-capitalize">
                        <?php echo single_cat_title() ?>
                    </h1>
                </article><!--\titulos-->
                <?php if($posts->have_posts()) : ?>
                <section id="post" class="posts">
                    <div class="row align-content-start">
                        <?php while($posts->have_posts()) : $posts->the_post(); ?>
                        <?php $categorias = array_map(function($object) { return $object->name; }, get_the_terms(get_the_ID(), 'category')); ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                           <?php get_template_part('partials/items/post', 'card'); ?>
                        </div><!--\Box Post Calc-->
                        <?php endwhile; wp_reset_postdata(); ?>
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