<style>
    .wp-block-social-link-anchor{
        border-radius: 29px;
        background-color: #283327;
        padding: 3px!important;
    }
</style>
<?php

global $wp_query;
$faq = get_field('postagens-perguntas-frequentes', $wp_query->post->ID);

?>
<?php if (have_posts()): ?>
    <?php while (have_posts() ): the_post(); ?> 
        <div class="conteudo-wysiwyg justify-content-between">
            <article class="col-auto px-1">
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
    <aside class="col-md-4 col-lg-4 col-xl-4 col-sm-12 h-100 w-100">
        <?php dynamic_sidebar('calculadora-meutudo-sidebar') ?>
    </aside>
<?php endif; ?>

</article>
</section>
</main>
<?php get_template_part('partials/templates/post/comentarios'); ?>

<?php get_template_part('partials/blocos/newsletter'); ?>

<?php //get_template_part('partials/blocos/postagens-sugeridas'); ?>

<?php get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('partials/rodapes/padrao'); ?>

<?php get_template_part('partials/blocos/compartilhar-lateral'); ?>