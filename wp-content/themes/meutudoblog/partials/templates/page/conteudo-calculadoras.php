<style>
    .wp-block-social-link-anchor{
        border-radius: 29px;
        background-color: #283327;
        padding: 3px!important;
    }
    main#main-container {
        display: flex !important;
        flex-direction: column;
        flex-wrap: wrap;
        flex-grow: initial;
        width: 100%;
        max-width: max-content;
    }
</style>
<?php

global $wp_query;
$faq = get_field('postagens-perguntas-frequentes', $wp_query->post->ID);

?>
<?php if (have_posts()): ?>
    <?php while (have_posts() ): the_post(); ?> 
        <div class="conteudo-wysiwyg container">
            <article class="row">
                <?php the_content(); ?>
            </article>
            
            <?php if (isset($faq['habilitado']) && $faq['habilitado']) { ?>
                <div class="mt-4">
                    <?php get_template_part('partials/blocos/perguntas-frequentes', null, ['faq' => $faq]); ?>
                </div>
            <?php } ?>
            
    <?php endwhile; ?>
<?php else: ?>
    <p>Sem postagem para exibir</p>
<?php endif; ?>
</div>
</div>
</section><!--\content post-->

<?php if (is_active_sidebar('calculadora-meutudo-sidebar')):?>
    <aside class="col-md-4 col-lg-4 col-xl-4 col-sm-12 w-100">
        <?php dynamic_sidebar('calculadora-meutudo-sidebar') ?>
    </aside>
<?php endif; ?>

</article>
</section>
<footer class="row">

    <?php get_template_part('partials/templates/post/comentarios'); ?>

    <?php get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

    <?php get_template_part('partials/rodapes/padrao'); ?>

    <?php get_footer(); ?>
</footer>
</main>
<div class="w-100"></div>

