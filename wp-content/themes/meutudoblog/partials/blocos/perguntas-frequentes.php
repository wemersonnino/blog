<?php

// Infos
$faq = $args['faq'] ?? get_field('postagens-perguntas-frequentes');

// Busca bloco com infos
$bloco = new WP_Query([
    'post_type' => 'bloco',
    'name' => 'bloco-perguntas-frequentes',
    'posts_per_page' => 1
]);

// Busca perguntas
$selecionadas = $faq['perguntas'] ? $faq['perguntas'] : [];
$perguntas_selecionadas = !empty($selecionadas) ? new WP_Query([
    'post_type' => 'pergunta',
    'orderby' => 'post__in',
    'post__in' => $selecionadas
]) : null;

?>
<?php if ($faq['habilitado']) { ?>
    <?php if ($bloco->have_posts()) { ?>
        <?php 
            
        $bloco->the_post();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];
        
        ?>
        <?php if ($perguntas_selecionadas && $perguntas_selecionadas->post_count > 0) { ?>
            <section class="bloco-perguntas-frequentes">
                <h2 class="titulo"><?= get_field('titulo') ?></h2>
                <div class="perguntas">
                    <?php if ($perguntas_selecionadas->have_posts()) { ?>
                        <?php while ($perguntas_selecionadas->have_posts()) { ?>
                            <?php
                            
                            $perguntas_selecionadas->the_post();
                            get_template_part('partials/items/pergunta', 'list');
                            array_push($schema['mainEntity'], [
                                '@type' => 'Question',
                                'name' => wp_strip_all_tags(get_the_title()),
                                'acceptedAnswer' => [
                                    '@type' => 'Answer',
                                    'text' => wp_strip_all_tags(get_the_content())
                                ]
                            ]);

                            ?>
                        <?php } ?>
                        <?php wp_reset_postdata() ?>
                    <?php } ?>
                </div>
            </section>
            <script type="application/ld+json"><?= json_encode($schema) ?></script>
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
        <?php } ?>
    <?php } ?>
    <?php wp_reset_postdata() ?>
<?php } ?>