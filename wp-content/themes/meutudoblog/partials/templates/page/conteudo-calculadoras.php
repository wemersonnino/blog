<?php
if (have_posts() ):
while (have_posts() ): the_post();
?>
<div class="conteudo-wysiwyg">
    <article class="col-auto">
        <?php the_content(); ?>
    </article>
    <?php endwhile;
    else:?>
    <p>Sem postagem para exibir</p>
    <?php endif; ?>
</div>
</div>
</section><!--\content post-->
<aside class="col-md-2 col-lg-2 col-xl-2">
    <?php get_sidebar('calculadora_sidebar'); ?>
</aside>
</article>
</section>
</main>