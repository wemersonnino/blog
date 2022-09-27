<?php
 function diletec_calculadora_shortcode( $atts ) {
    // Attributes
    $default = array(
            'id' => null,
    );
    $filter = shortcode_atts( $default, $atts );
    $mrt_load_calculadora = new WP_Query( array(
        'post_type' => 'calculadora',
        'ID' => $filter['id'],
        'offset' => 1,
    ) );

    if ( $mrt_load_calculadora->have_posts() ) {
        while ( $mrt_load_calculadora->have_posts() ) { $mrt_load_calculadora->the_post();
            the_content();

            /** get post meta */
            $mrt_calculadora_meta = get_post_meta( $filter['id'] );
            // _e( $mrt_calculadora_meta['titulo_do_resultado'][0] );
            // _e( $mrt_calculadora_meta['titulo_da_margem_permitida'][0] );
            // _e( $mrt_calculadora_meta['descricao_da_margem_permitida'][0] );
            // _e( $mrt_calculadora_meta['titulo_da_margem_disponivel'][0] );
            // _e( $mrt_calculadora_meta['descricao_da_margem_disponivel'][0] );
            // _e( $mrt_calculadora_meta['valor_do_beneficio'][0] );
            // _e( $mrt_calculadora_meta['parcela_de_emprestimo'][0] );

            /** Html do projeto */
            $argsTemplates = array(
                'id' => $filter['id'],
                'titulo_do_resultado' => $mrt_calculadora_meta['titulo_do_resultado'][0],
                'titulo_da_margem_permitida' => $mrt_calculadora_meta['titulo_da_margem_permitida'][0],
                'descricao_da_margem_permitida' => $mrt_calculadora_meta['descricao_da_margem_permitida'][0],
                'titulo_da_margem_disponivel' => $mrt_calculadora_meta['titulo_da_margem_disponivel'][0],
                'descricao_da_margem_disponivel' => $mrt_calculadora_meta['descricao_da_margem_disponivel'][0],
                'valor_do_beneficio' => $mrt_calculadora_meta['valor_do_beneficio'][0],
                'parcela_de_emprestimo' => $mrt_calculadora_meta['parcela_de_emprestimo'][0],
                'porcentagem_da_margem_permitida' => $mrt_calculadora_meta['porcentagem_da_margem_permitida'][0],
            );
            load_template( plugin_dir_path( __FILE__ ) . 'templates/default.php', false, $argsTemplates );

        }
    } else {
      return 'Nothing found';
    }
    wp_reset_postdata();
}
add_shortcode( 'calculadora', 'diletec_calculadora_shortcode' );