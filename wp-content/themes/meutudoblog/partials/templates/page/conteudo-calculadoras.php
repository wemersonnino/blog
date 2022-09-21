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
<?php if (is_active_sidebar('calculadora-meutudo-sidebar')):?>
<aside class="col-md-3 col-lg-3 col-xl-3 h-100 w-100">
    <?php dynamic_sidebar('calculadora-meutudo-sidebar') ?>
</aside>
<?php endif; ?>
</article>
</section>
</main>