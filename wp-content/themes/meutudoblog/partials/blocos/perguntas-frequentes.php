<?php

$faq = $args['faq'] ?? get_field('postagens-perguntas-frequentes');

?>
<?php if ($faq['habilitado']) : ?>
    <?php $selecionadas = $faq['perguntas'] ? array_slice($faq['perguntas'], 0, 4) : null; ?>
    
    <?php $bloco = new WP_Query(array(
      'post_type' => 'bloco',
      'name' => 'bloco-perguntas-frequentes',
      'posts_per_page' => 1
    )); ?>
    <?php if($bloco->have_posts()) : $bloco->the_post(); ?>
        <?php $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        ); ?>
        <?php $padrao = get_field('perguntas-padrao') ? array_slice(get_field('perguntas-padrao'), 0, 4) : null; ?>
        <?php $padrao_amount = 4 - ($selecionadas ? count($selecionadas) : 0); ?>
    
        <?php if ($selecionadas) : ?>
            <?php $perguntas_selecionadas = new WP_Query(array(
                'post_type' => 'pergunta',
                'orderby' => 'post__in',
                'post__in' => $selecionadas,
                'posts_per_page' => 4
            )); ?>
        <?php endif; ?>
        <?php $perguntas_padrao = new WP_Query(array(
            'post_type' => 'pergunta',
            'orderby' => 'post__in',
            'post__in' => $padrao,
            'posts_per_page' => $padrao_amount
        )); ?>
        
        <?php if (($selecionadas ? $perguntas_selecionadas->post_count : 0) + $perguntas_padrao->post_count > 0) : ?>
    
            <section class="bloco-perguntas-frequentes">
              <!--<div class="container">-->
                <h2 class="titulo"><?php the_field('titulo'); ?></h2>
                
                <div class="perguntas">
                    <?php if($selecionadas) : ?>
                        <?php if($perguntas_selecionadas->have_posts()) : ?>
                            <?php while($perguntas_selecionadas->have_posts()) : $perguntas_selecionadas->the_post(); ?>
                                <?php get_template_part('partials/items/pergunta', 'list'); ?>
                                <?php array_push($schema['mainEntity'], array(
                                    '@type' => 'Question',
                                    'name' => get_the_title(),
                                    'acceptedAnswer' => array(
                                        '@type' => 'Answer',
                                        'text' => wp_strip_all_tags(get_the_content())
                                    )
                                )); ?>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if($padrao_amount > 0) : ?>
                        <?php if($perguntas_padrao->have_posts()) : ?>
                            <?php while($perguntas_padrao->have_posts()) : $perguntas_padrao->the_post(); ?>
                                <?php get_template_part('partials/items/pergunta', 'list'); ?>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <!--</div>-->
              </div>
            </section>
            <script type="application/ld+json"><?php echo json_encode($schema); ?></script>
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    jQuery(function($) {
                        $('section.bloco-perguntas-frequentes .perguntas .pergunta-list .pergunta').click(function(e) {
                            e.preventDefault();
                            const parent = $(e.target).closest('.perguntas');
                            const pergunta = $(e.target).closest('.pergunta-list');
                            
                            if(pergunta.hasClass('ativo')) {
                                pergunta.removeClass('ativo');
                            } else {   
                                parent.find('.pergunta-list').removeClass('ativo');
                                pergunta.addClass('ativo');
                            }
                        });
                    });
                });
            </script>
            
        <?php endif; ?>
        
    <?php endif; wp_reset_postdata(); ?>
<?php endif; ?>