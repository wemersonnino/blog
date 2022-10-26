<?php

function diletec_calculadora_shortcode( $atts ) {
    // Attributes
    $default = array(
        'id' => null,
    );
    $filter = shortcode_atts( $default, $atts );
    $mrt_load_calculadora = new WP_Query([
        'post_type' => 'calculadora',
        'ID' => $filter['id']
    ]);

    if ( $mrt_load_calculadora->have_posts() ) {
        while ( $mrt_load_calculadora->have_posts() ) { $mrt_load_calculadora->the_post();
            //the_content();

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
            $args = array(
                'id' => $filter['id'],
                'titulo_do_resultado' => $mrt_calculadora_meta['titulo_do_resultado'][0],
                'titulo_da_margem_permitida' => $mrt_calculadora_meta['titulo_da_margem_permitida'][0],
                'descricao_da_margem_permitida' => $mrt_calculadora_meta['descricao_da_margem_permitida'][0],
                'titulo_da_margem_disponivel' => $mrt_calculadora_meta['titulo_da_margem_disponivel'][0],
                'descricao_da_margem_disponivel' => $mrt_calculadora_meta['descricao_da_margem_disponivel'][0],
                'valor_do_beneficio' => $mrt_calculadora_meta['valor_do_beneficio'][0],
                'parcela_de_emprestimo' => $mrt_calculadora_meta['parcela_de_emprestimo'][0],
                'porcentagem_da_margem_permitida' => $mrt_calculadora_meta['porcentagem_da_margem_permitida'][0],
                'titulo_da_dica_no_primeiro_campo'   => $mrt_calculadora_meta['titulo_da_dica_no_primeiro_campo'][0],
                'texto_para_compor_a_ajuda_no_primeiro_campo' => $mrt_calculadora_meta['texto_para_compor_a_ajuda_no_primeiro_campo'][0],
                'titulo_da_dica_no_segundo_campo' => $mrt_calculadora_meta['titulo_da_dica_no_segundo_campo'][0],
                'texto_para_compor_a_ajuda_no_campo_parcelas' => $mrt_calculadora_meta['texto_para_compor_a_ajuda_no_campo_parcelas'][0],
                'texto_dica_campo_resultado_salario_beneficio' => $mrt_calculadora_meta['texto_dica_campo_resultado_salario_beneficio'][0],
                'texto_para_compor_a_ajuda_no_campo_resultado_salario_beneficio' => $mrt_calculadora_meta['texto_para_compor_a_ajuda_no_campo_resultado_salario_beneficio'][0],
                'titulo_da_dica_no_segundo_campo_parcelas' => $mrt_calculadora_meta['titulo_da_dica_no_segundo_campo_parcelas'][0],
                'texto_que_compoem_a_dica_para_parcelas' => $mrt_calculadora_meta['texto_que_compoem_a_dica_para_parcelas'][0],
            );
            ob_start();
            ?>
            <div class="w-100"></div>
            <section id="box-calculadora" class="calculadora">
                <header id="titulo-calculadora" class="titulo_<?php _e($args['id']); ?>">
                    <h2><?php _e($args['titulo_do_resultado']); ?></h2>
                </header>
                <main id="input-calc" class="input-calc bg-silver container">
                    <div class="row">
                        <div id="boxBeneficioSalario" class="col-md-6 col-lg-6 col-xl-6 col-sm-12 calculadora_body_<?php _e($args['id']); ?>">
                            <div id="boxBeneficioSalario" class="col">
                                <label for="beneficioSalario" class="form-label text-start">
                                    <?php _e($args['valor_do_beneficio']); ?>
                                    <span class="align-content-around text-start">
                                    <button type="button" class="btn btn-link float-end parcelas-infor"
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-bs-container="body"
                                            data-bs-trigger="hover focus"
                                            data-bs-placement="auto"
                                            data-bs-sanitize="true"
                                            data-bs-html="false"
                                            title="<?php _e($args['titulo_da_dica_no_primeiro_campo']); ?>"
                                            data-bs-content="<?php _e($args['texto_para_compor_a_ajuda_no_primeiro_campo']); ?>"
                                            role="button"
                                    >
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.4715 2.96412C13.3096 1.85273 11.8376 1.09073 10.2345 0.770854C8.63133 0.450974 6.96628 0.587011 5.44194 1.16241C5.32363 1.23436 5.23495 1.3437 5.19109 1.47171C5.14724 1.59972 5.15093 1.73844 5.20153 1.86412C5.25214 1.9898 5.34651 2.09462 5.46849 2.16064C5.59047 2.22666 5.73248 2.24976 5.87019 2.226C7.481 1.631 9.25939 1.60994 10.8849 2.16658C12.5104 2.72323 13.8761 3.82099 14.736 5.26205C15.5958 6.70311 15.8932 8.39269 15.5746 10.0263C15.256 11.66 14.3423 13.1303 12.9982 14.1723C11.6541 15.2142 9.96797 15.7594 8.24364 15.7095C6.51931 15.6595 4.87018 15.0178 3.59343 13.9C2.31668 12.7821 1.49626 11.2616 1.28001 9.6125C1.06377 7.96336 1.46592 6.29403 2.414 4.90531C2.4761 4.844 2.52337 4.77022 2.55242 4.68925C2.58146 4.60828 2.59158 4.52213 2.58203 4.43695C2.57248 4.35178 2.54351 4.26969 2.49718 4.19657C2.45086 4.12344 2.38834 4.06109 2.31408 4.01397C2.23983 3.96685 2.15568 3.93613 2.06766 3.92402C1.97965 3.9119 1.88994 3.91868 1.80496 3.94387C1.71998 3.96907 1.64183 4.01205 1.57611 4.06975C1.51039 4.12745 1.45872 4.19843 1.42479 4.27762C0.285895 5.96326 -0.164693 7.99563 0.159981 9.98254C0.484654 11.9694 1.56158 13.7701 3.18293 15.0369C4.80429 16.3038 6.85517 16.9471 8.93981 16.8428C11.0244 16.7384 12.9951 15.8938 14.4715 14.4718C15.2557 13.7162 15.8778 12.8192 16.3022 11.832C16.7266 10.8447 16.9451 9.78656 16.9451 8.71796C16.9451 7.64936 16.7266 6.59122 16.3022 5.60397C15.8778 4.61672 15.2557 3.71969 14.4715 2.96412Z" fill="black"/>
                                            <path d="M8.37937 5.45161C8.74581 5.45161 9.04287 5.16537 9.04287 4.81229C9.04287 4.45921 8.74581 4.17297 8.37937 4.17297C8.01294 4.17297 7.71588 4.45921 7.71588 4.81229C7.71588 5.16537 8.01294 5.45161 8.37937 5.45161Z" fill="black"/>
                                            <path d="M6.99813 12.2633C6.83816 12.2633 6.68474 12.3245 6.57162 12.4335C6.45851 12.5425 6.39496 12.6903 6.39496 12.8445C6.39496 12.9986 6.45851 13.1464 6.57162 13.2554C6.68474 13.3644 6.83816 13.4257 6.99813 13.4257H9.94162C10.1016 13.4257 10.255 13.3644 10.3681 13.2554C10.4813 13.1464 10.5448 12.9986 10.5448 12.8445C10.5448 12.6903 10.4813 12.5425 10.3681 12.4335C10.255 12.3245 10.1016 12.2633 9.94162 12.2633H9.10321V7.26497C9.10321 7.11083 9.03966 6.963 8.92655 6.85401C8.81343 6.74501 8.66001 6.68378 8.50004 6.68378H7.11274C6.95276 6.68378 6.79934 6.74501 6.68623 6.85401C6.57311 6.963 6.50956 7.11083 6.50956 7.26497C6.50956 7.41912 6.57311 7.56695 6.68623 7.67594C6.79934 7.78494 6.95276 7.84617 7.11274 7.84617H7.89686V12.2633H6.99813Z" fill="black"/>
                                        </svg>
                                    </button>
                                </span>
                                </label>
                                <div class="input-group mb-3 form-floating">
                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                    <input type="text" id="beneficioSalario" name="beneficioSalario"
                                           inputmode="numeric" min="1" max="5000000"
                                           class="form-control calculadora_valor_do_beneficio_<?php _e($args['id']); ?>"
                                           placeholder="0,00"
                                           aria-label="<?php _e($args['valor_do_beneficio']); ?>"
                                           role="button"
                                    >
                                </div>
                            </div>
                        </div><!--/input 1 -->
                        <div id="boxParcelas" class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                            <label for="parcelas" class="form-label text-start">
                                <?php _e($args['parcela_de_emprestimo']); ?>
                                <span class="align-content-around text-start">
                                    <button type="button" class="btn btn-link float-end parcelas-infor"
                                            tabindex="1"
                                            data-bs-toggle="popover"
                                            data-bs-container="body"
                                            data-bs-trigger="hover focus"
                                            data-bs-placement="auto"
                                            data-bs-sanitize="true"
                                            data-bs-html="false"
                                            title="<?php _e($args['texto_para_compor_a_ajuda_no_campo_parcelas']); ?>"
                                            data-bs-content="<?php _e($args['texto_para_compor_a_ajuda_no_campo_resultado_salario_beneficio']); ?>"
                                            role="button"
                                    >
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.4715 2.96412C13.3096 1.85273 11.8376 1.09073 10.2345 0.770854C8.63133 0.450974 6.96628 0.587011 5.44194 1.16241C5.32363 1.23436 5.23495 1.3437 5.19109 1.47171C5.14724 1.59972 5.15093 1.73844 5.20153 1.86412C5.25214 1.9898 5.34651 2.09462 5.46849 2.16064C5.59047 2.22666 5.73248 2.24976 5.87019 2.226C7.481 1.631 9.25939 1.60994 10.8849 2.16658C12.5104 2.72323 13.8761 3.82099 14.736 5.26205C15.5958 6.70311 15.8932 8.39269 15.5746 10.0263C15.256 11.66 14.3423 13.1303 12.9982 14.1723C11.6541 15.2142 9.96797 15.7594 8.24364 15.7095C6.51931 15.6595 4.87018 15.0178 3.59343 13.9C2.31668 12.7821 1.49626 11.2616 1.28001 9.6125C1.06377 7.96336 1.46592 6.29403 2.414 4.90531C2.4761 4.844 2.52337 4.77022 2.55242 4.68925C2.58146 4.60828 2.59158 4.52213 2.58203 4.43695C2.57248 4.35178 2.54351 4.26969 2.49718 4.19657C2.45086 4.12344 2.38834 4.06109 2.31408 4.01397C2.23983 3.96685 2.15568 3.93613 2.06766 3.92402C1.97965 3.9119 1.88994 3.91868 1.80496 3.94387C1.71998 3.96907 1.64183 4.01205 1.57611 4.06975C1.51039 4.12745 1.45872 4.19843 1.42479 4.27762C0.285895 5.96326 -0.164693 7.99563 0.159981 9.98254C0.484654 11.9694 1.56158 13.7701 3.18293 15.0369C4.80429 16.3038 6.85517 16.9471 8.93981 16.8428C11.0244 16.7384 12.9951 15.8938 14.4715 14.4718C15.2557 13.7162 15.8778 12.8192 16.3022 11.832C16.7266 10.8447 16.9451 9.78656 16.9451 8.71796C16.9451 7.64936 16.7266 6.59122 16.3022 5.60397C15.8778 4.61672 15.2557 3.71969 14.4715 2.96412Z" fill="black"/>
                                            <path d="M8.37937 5.45161C8.74581 5.45161 9.04287 5.16537 9.04287 4.81229C9.04287 4.45921 8.74581 4.17297 8.37937 4.17297C8.01294 4.17297 7.71588 4.45921 7.71588 4.81229C7.71588 5.16537 8.01294 5.45161 8.37937 5.45161Z" fill="black"/>
                                            <path d="M6.99813 12.2633C6.83816 12.2633 6.68474 12.3245 6.57162 12.4335C6.45851 12.5425 6.39496 12.6903 6.39496 12.8445C6.39496 12.9986 6.45851 13.1464 6.57162 13.2554C6.68474 13.3644 6.83816 13.4257 6.99813 13.4257H9.94162C10.1016 13.4257 10.255 13.3644 10.3681 13.2554C10.4813 13.1464 10.5448 12.9986 10.5448 12.8445C10.5448 12.6903 10.4813 12.5425 10.3681 12.4335C10.255 12.3245 10.1016 12.2633 9.94162 12.2633H9.10321V7.26497C9.10321 7.11083 9.03966 6.963 8.92655 6.85401C8.81343 6.74501 8.66001 6.68378 8.50004 6.68378H7.11274C6.95276 6.68378 6.79934 6.74501 6.68623 6.85401C6.57311 6.963 6.50956 7.11083 6.50956 7.26497C6.50956 7.41912 6.57311 7.56695 6.68623 7.67594C6.79934 7.78494 6.95276 7.84617 7.11274 7.84617H7.89686V12.2633H6.99813Z" fill="black"/>
                                        </svg>
                                    </button>
                                </span>
                            </label>
                            <div class="input-group mb-3 form-floating">
                                <span class="input-group-text" id="basic-addon2">R$</span>
                                <input inputmode="numeric" min="1" max="5000000" type="text"
                                       id="parcelas" class="form-control calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>"
                                       placeholder="0,00"
                                       aria-label="Parcela de empréstimos do benefício">
                            </div>
                        </div><!--/input 2 -->
                    </div>
                </main>
                <footer id="footer-input-calc" class="calculadora_footer_<?php _e($args['id']); ?>">
                    <header class="titulo_do_resultado_<?php _e($args['id']); ?>">
                        <h4><?php _e($args['titulo_do_resultado']); ?></h4>
                    </header>
                    <div class="w-100"></div><br>
                    <main>
                        <div class="row justify-content-around">
                            <div class="col-md-5 col-lg-5 col-xl-5 col-sm-12">
                                <h3><?php _e($args['titulo_da_margem_permitida']); ?></h3>
                                <p>
                                    <?php _e($args['descricao_da_margem_permitida']); ?>
                                </p>
                            </div><!--/Text box ref input 01 -->
                            <div class="col-md-5 col-lg-5 col-xl-5 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                                <div class="row justify-content-start">
                                    <div class="col">
                                        <p class="fs-6">
                                            <strong>R$ <span id="resultBeneficioSalario" class="calculadora_margem_permitida_<?php _e($args['id']); ?>">000,00</span></strong>
                                        </p>
                                    </div>
                                    <div class="col">
                                        <p class="text-end">
                                            <button type="button" class="btn btn-link float-end parcelas-infor"
                                                    tabindex="3"
                                                    data-bs-toggle="popover"
                                                    data-bs-container="body"
                                                    data-bs-trigger="hover focus"
                                                    data-bs-placement="auto"
                                                    data-bs-sanitize="true"
                                                    data-bs-html="false"
                                                    title="<?php _e($args['titulo_da_dica_no_segundo_campo']); ?>"
                                                    data-bs-content="<?php _e($args['texto_dica_campo_resultado_salario_beneficio']); ?>"
                                                    role="button"
                                            >
                                                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.5428 3.83591C18.0753 2.39766 16.2158 1.41154 14.1908 0.997575C12.1658 0.583614 10.0626 0.759661 8.13711 1.50429C7.98767 1.5974 7.87566 1.7389 7.82026 1.90457C7.76486 2.07023 7.76952 2.24975 7.83344 2.4124C7.89736 2.57504 8.01657 2.71069 8.17065 2.79612C8.32474 2.88155 8.50411 2.91145 8.67807 2.8807C10.7128 2.11071 12.9592 2.08345 15.0124 2.80381C17.0657 3.52418 18.7908 4.94482 19.8769 6.80971C20.963 8.67461 21.3387 10.8611 20.9363 12.9753C20.5338 15.0894 19.3797 16.9921 17.6819 18.3406C15.9841 19.689 13.8542 20.3945 11.6761 20.3299C9.49801 20.2653 7.4149 19.4349 5.80216 17.9882C4.18941 16.5416 3.1531 14.5739 2.87995 12.4397C2.6068 10.3055 3.11478 8.14521 4.31235 6.34805C4.39079 6.26871 4.4505 6.17323 4.48719 6.06844C4.52388 5.96366 4.53666 5.85217 4.5246 5.74194C4.51253 5.63171 4.47594 5.52548 4.41743 5.43085C4.35891 5.33622 4.27993 5.25553 4.18614 5.19455C4.09234 5.13357 3.98605 5.09382 3.87487 5.07814C3.76369 5.06245 3.65038 5.07123 3.54304 5.10383C3.43569 5.13644 3.33698 5.19206 3.25396 5.26673C3.17095 5.3414 3.10568 5.43326 3.06283 5.53574C1.62422 7.71716 1.05505 10.3473 1.46517 12.9186C1.87528 15.4899 3.23561 17.8201 5.28364 19.4596C7.33167 21.0991 9.92225 21.9316 12.5555 21.7965C15.1887 21.6615 17.6779 20.5684 19.5428 18.7282C20.5334 17.7504 21.3192 16.5896 21.8554 15.3119C22.3915 14.0343 22.6674 12.665 22.6674 11.2821C22.6674 9.89917 22.3915 8.52981 21.8554 7.2522C21.3192 5.97458 20.5334 4.81372 19.5428 3.83591V3.83591Z" fill="white"/>
                                                    <path d="M11.8476 7.05503C12.3105 7.05503 12.6857 6.68461 12.6857 6.22768C12.6857 5.77075 12.3105 5.40033 11.8476 5.40033C11.3847 5.40033 11.0095 5.77075 11.0095 6.22768C11.0095 6.68461 11.3847 7.05503 11.8476 7.05503Z" fill="white"/>
                                                    <path d="M10.1028 15.87C9.90078 15.87 9.70698 15.9493 9.5641 16.0903C9.42121 16.2314 9.34094 16.4227 9.34094 16.6222C9.34094 16.8217 9.42121 17.013 9.5641 17.154C9.70698 17.2951 9.90078 17.3743 10.1028 17.3743H13.8209C14.023 17.3743 14.2168 17.2951 14.3597 17.154C14.5026 17.013 14.5828 16.8217 14.5828 16.6222C14.5828 16.4227 14.5026 16.2314 14.3597 16.0903C14.2168 15.9493 14.023 15.87 13.8209 15.87H12.7619V9.40167C12.7619 9.20219 12.6816 9.01089 12.5387 8.86983C12.3959 8.72878 12.2021 8.64954 12 8.64954H10.2476C10.0455 8.64954 9.85175 8.72878 9.70886 8.86983C9.56598 9.01089 9.4857 9.20219 9.4857 9.40167C9.4857 9.60115 9.56598 9.79246 9.70886 9.93351C9.85175 10.0746 10.0455 10.1538 10.2476 10.1538H11.2381V15.87H10.1028Z" fill="white"/>
                                                </svg>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div><!--/Text box result ref input 01 -->
                        </div><!--\Ref. Result and text All elements input 01-->
                        <div class="row justify-content-around">
                            <div class="col-md-5 col-lg-5 col-xl-5 col-sm-12">
                                <h3><?php _e($args['titulo_da_margem_disponivel']); ?></h3>
                                <p>
                                    <?php _e($args['descricao_da_margem_disponivel']); ?>
                                </p>
                            </div>
                            <div class="col-md-5 col-lg-5 col-xl-5 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                                <div class="row justify-content-start">
                                    <div class="col">
                                        <strong>R$ <span class="fs-6 calculadora_margem_disponivel_<?php _e($args['id']); ?>">000,00</span></strong>
                                    </div>
                                    <div class="col">
                                        <p class="text-end">
                                            <button id="ultimoCampo" type="button" class="btn btn-link float-end parcelas-infor"
                                                    tabindex="4"
                                                    data-bs-toggle="popover"
                                                    data-bs-container="body"
                                                    data-bs-trigger="hover focus"
                                                    data-bs-placement="auto"
                                                    data-bs-sanitize="true"
                                                    data-bs-html="false"
                                                    title="<?php _e($args['titulo_da_dica_no_segundo_campo_parcelas']); ?>"
                                                    data-bs-content="<?php _e($args['texto_que_compoem_a_dica_para_parcelas']); ?>"
                                                    role="button"
                                            >
                                                <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.5428 3.83591C18.0753 2.39766 16.2158 1.41154 14.1908 0.997575C12.1658 0.583614 10.0626 0.759661 8.13711 1.50429C7.98767 1.5974 7.87566 1.7389 7.82026 1.90457C7.76486 2.07023 7.76952 2.24975 7.83344 2.4124C7.89736 2.57504 8.01657 2.71069 8.17065 2.79612C8.32474 2.88155 8.50411 2.91145 8.67807 2.8807C10.7128 2.11071 12.9592 2.08345 15.0124 2.80381C17.0657 3.52418 18.7908 4.94482 19.8769 6.80971C20.963 8.67461 21.3387 10.8611 20.9363 12.9753C20.5338 15.0894 19.3797 16.9921 17.6819 18.3406C15.9841 19.689 13.8542 20.3945 11.6761 20.3299C9.49801 20.2653 7.4149 19.4349 5.80216 17.9882C4.18941 16.5416 3.1531 14.5739 2.87995 12.4397C2.6068 10.3055 3.11478 8.14521 4.31235 6.34805C4.39079 6.26871 4.4505 6.17323 4.48719 6.06844C4.52388 5.96366 4.53666 5.85217 4.5246 5.74194C4.51253 5.63171 4.47594 5.52548 4.41743 5.43085C4.35891 5.33622 4.27993 5.25553 4.18614 5.19455C4.09234 5.13357 3.98605 5.09382 3.87487 5.07814C3.76369 5.06245 3.65038 5.07123 3.54304 5.10383C3.43569 5.13644 3.33698 5.19206 3.25396 5.26673C3.17095 5.3414 3.10568 5.43326 3.06283 5.53574C1.62422 7.71716 1.05505 10.3473 1.46517 12.9186C1.87528 15.4899 3.23561 17.8201 5.28364 19.4596C7.33167 21.0991 9.92225 21.9316 12.5555 21.7965C15.1887 21.6615 17.6779 20.5684 19.5428 18.7282C20.5334 17.7504 21.3192 16.5896 21.8554 15.3119C22.3915 14.0343 22.6674 12.665 22.6674 11.2821C22.6674 9.89917 22.3915 8.52981 21.8554 7.2522C21.3192 5.97458 20.5334 4.81372 19.5428 3.83591V3.83591Z" fill="white"/>
                                                    <path d="M11.8476 7.05503C12.3105 7.05503 12.6857 6.68461 12.6857 6.22768C12.6857 5.77075 12.3105 5.40033 11.8476 5.40033C11.3847 5.40033 11.0095 5.77075 11.0095 6.22768C11.0095 6.68461 11.3847 7.05503 11.8476 7.05503Z" fill="white"/>
                                                    <path d="M10.1028 15.87C9.90078 15.87 9.70698 15.9493 9.5641 16.0903C9.42121 16.2314 9.34094 16.4227 9.34094 16.6222C9.34094 16.8217 9.42121 17.013 9.5641 17.154C9.70698 17.2951 9.90078 17.3743 10.1028 17.3743H13.8209C14.023 17.3743 14.2168 17.2951 14.3597 17.154C14.5026 17.013 14.5828 16.8217 14.5828 16.6222C14.5828 16.4227 14.5026 16.2314 14.3597 16.0903C14.2168 15.9493 14.023 15.87 13.8209 15.87H12.7619V9.40167C12.7619 9.20219 12.6816 9.01089 12.5387 8.86983C12.3959 8.72878 12.2021 8.64954 12 8.64954H10.2476C10.0455 8.64954 9.85175 8.72878 9.70886 8.86983C9.56598 9.01089 9.4857 9.20219 9.4857 9.40167C9.4857 9.60115 9.56598 9.79246 9.70886 9.93351C9.85175 10.0746 10.0455 10.1538 10.2476 10.1538H11.2381V15.87H10.1028Z" fill="white"/>
                                                </svg>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div><!--\Result Ref. All elements for input 02-->
                    </main>
                </footer>
            </section><!--\Calculadora Box-->
            <style>
                .titulo_<?php _e($args['id']); ?>{
                    /*background: black;*/
                    /*color: white;*/
                    /*border-radius: 30px 0px 0px 0px;*/
                }

                .titulo_do_resultado_<?php _e($args['id']); ?>{

                }
                .titulo_do_resultado_<?php _e($args['id']); ?> h2{
                    /*padding: 10px;*/
                }
                .calculadora_body_<?php _e($args['id']); ?>{
                    /*background: #f5f5f582;*/
                    padding: 0;
                }
                .calculadora_margem_permitida_<?php _e($args['id']); ?>{
                    line-height: 4em;
                }
                .calculadora_margem_disponivel_<?php _e($args['id']); ?>{
                    line-height: 4em;
                }
                .calculadora_footer_<?php _e($args['id']); ?>{
                    background: #e33089;
                    /*color: #fff;*/
                    /*padding: 10px;*/
                }
                .money{
                    float: left;
                    height: 40px;
                    line-height: 40px;
                    font-weight: 500;
                }
                /*input[type="text"]{*/
                /*    float: right;*/
                /*    max-width: 86%;*/
                /*    height: 40px;*/
                /*    border: 0px;*/
                /*    border: 1px solid #ccc;*/
                /*    border-radius: 0px 5px 5px 0px;*/
                /*}*/

                /*label {*/
                /*    !*background-color: #bdb6b6;*!*/
                /*    display: inline-block;*/
                /*    max-width: 100%;*/
                /*    margin-bottom: 5px;*/
                /*    font-weight: 400;*/
                /*    border-radius: 5px;*/
                /*    padding-right: 0px !important;*/
                /*}*/
                /* ==========================================================================
                   Wemerson Pereira custom styles
                   ========================================================================== */
                :root{
                    /*fonts*/
                    --Montserrat: 'Montserrat', sans-serif;

                    /*color*/
                    --color-black: #000000;
                    --color-pink: #D22688;
                    --color-white: #FFFFFF;
                    --color-black-50: #00000026;
                    --color-silver-50: #F5F5F5;

                    /*background-color*/
                    --bg_black: #000000;
                    --bg-silver:#F5F5F5;
                    --bg-white: #FFFFFF;
                    --bg-pink: #D22688;

                    /*Sizes*/
                    --font-8:  0.525em;
                    --font-12: 0.750em;
                    --font-14: 0.875em;
                    --font-15: 0.938em;
                    --font-18: 1.125em;
                    --font-20: 1.250em;
                    --font-22: 1.375em;
                    --font-24: 1.500em;
                    --font-40: 2.500em;
                    --font-55: 3.438em;
                    --font-64: 4.000em;
                }

                #box-calculadora #titulo-calculadora{
                    width: auto;
                    max-width: 100%;
                    height: auto;
                    max-height: 88px;
                    background: var(--color-black);
                    border-radius: 32px 4px 0px 0px;
                    color: var(--color-white);
                    padding: 1.5rem;
                    margin: 0 auto;
                    font-family: var(--Montserrat), sans-serif;
                    font-size: var(--font-20);
                    font-weight: 700;
                    line-height: 24px;
                    letter-spacing: 0em;
                    text-align: left;
                }
                #box-calculadora #titulo-calculadora h2{
                    font-size: var(--font-20);
                    font-weight: 600;
                    line-height: 1;
                }
                #box-calculadora > main{
                    max-width: 100%;
                    max-height: 100%;
                    background: var(--bg-silver);
                    padding: 1.5rem;
                }
                #boxbeneficioSalatio{
                    padding: 1rem;
                }
                #box-calculadora > main .form-label {
                    width: 100%;
                    font-family: var(--Montserrat), sans-serif;
                    font-size: var(--font-14);
                    font-weight: 400;
                    line-height: 17px;
                    letter-spacing: 0em;
                    text-align: justify;
                    margin: 0 auto 1.5rem auto;

                }
                #box-calculadora > main .form-label > span #beneficioSalatio-infor {
                    margin-right: 2rem;
                    float: right;
                }
                #box-calculadora > main #boxBeneficioSalario input#beneficioSalaRio,#parcelas{
                    padding: 0 1rem;
                    font-weight: 400;
                }
                #box-calculadora > main #boxBeneficioSalario label[for=beneficioSalario] {
                    margin:0 auto 1.5rem auto;
                }
                section#box-calculadora #footer-input-calc{
                    max-width: 100%;
                    max-height: 100%;
                    padding: 1.5rem;
                    background-color: #D22688;
                    border-radius: 0px 0px 32px 4px;
                    color: var(--color-silver-50);
                }
                section#box-calculadora #footer-input-calc header h4{
                    font-family: var(--Montserrat), sans-serif;
                    font-size: var(--font-20);
                    font-weight: 700;
                    line-height: 24px;
                    letter-spacing: 0em;
                    text-align: left;
                }
                section#box-calculadora #footer-input-calc main h3{
                    font-family: var(--Montserrat);
                    font-size: var(--font-20);
                    font-weight: 600;
                    line-height: 24px;
                    letter-spacing: 0em;
                    text-align: left;
                }
                section#box-calculadora #footer-input-calc main div p{
                    font-family: var(--Montserrat);
                    font-size: var(--font-14);
                    font-weight: 400;
                    line-height: 17px;
                    letter-spacing: 0em;
                    text-align: left;

                }
                section#box-simuladores #box-simuladores-topo{
                    width: 100%;
                    height: auto;
                    background-color: var(--bg_black);
                    border-radius: 16px 4px 0px 0px;
                    padding: 1rem;
                    color: var(--color-white);
                }
                section#box-simuladores #box-simuladores-topo h2{
                    font-family: var(--Montserrat);
                    font-size: var(--font-20);
                    font-weight: 600;
                    line-height: 0.938em;
                    letter-spacing: 0em;
                    text-align: left;
                    color: var(--color-white);
                }
                section#box-simuladores main#box-simuladores-main table thead{
                    background-color: var(--bg-white);
                }
                section#box-simuladores main#box-simuladores-main table thead tr th{
                    font-family: var(--Montserrat);
                    font-size: var(--font-14);
                    font-weight: 400;
                    line-height: 17px;
                    letter-spacing: 0em;
                    text-align: left;
                    padding: 1.2rem;
                }
                section#box-simuladores main#box-simuladores-main table tbody tr th,td{
                    font-family: var(--Montserrat);
                    font-size: var(--font-14);
                    font-weight: 600;
                    line-height: 17px;
                    letter-spacing: 0em;
                    text-align: left;
                    padding: 1.2rem;
                }
                section#box-simuladores main#box-simuladores-main table tbody tr td{
                    font-family: var(--Montserrat);
                    font-size: var(--font-14);
                    font-weight: 600;
                    line-height: 17px;
                    letter-spacing: 0em;
                    text-align: left;
                    padding: 1.2rem;
                    line-height: 2;
                    letter-spacing: 0em;
                }
                section#box-simuladores main#box-simuladores-main table tbody tr td p a{
                    color: var(--color-black);
                    font-size: var(--font-18);
                    font-weight: 300;
                }
                section#box-simuladores main#box-simuladores-main table tbody tr td button{
                    font-family: var(--Montserrat);
                    font-size: var(--font-18);
                    font-weight: 700;
                    letter-spacing: 0em;
                    text-align: center;
                    position: relative;
                    /* MeuRosa */
                    background-color: var(--bg-pink);
                    border-radius: 5em;
                    padding: 1rem;
                    color: var(--color-white);
                    line-height: 0;
                }
                .conteudo-wysiwyg-questions{
                    max-width: 100%;
                    padding: 1rem;
                    margin: 0 auto;
                    max-height: 100%;
                }
                .conteudo-wysiwyg-questions p{
                    font-family: var(--Montserrat);
                    font-size: var(--font-18);
                    text-align: justify;
                    font-weight: 400;
                    line-height: 31px;
                    letter-spacing: 0em;
                }
                @media only screen and (min-width: 35em) {
                    /* Style adjustments for viewports that meet the condition */
                    #beneficioSalario{
                        padding: 0 1rem 0 1rem;
                    }
                    #box-calculadora > main #boxBeneficioSalario label[for=beneficioSalario] {
                        margin:0.4rem auto;
                    }
                    #boxParcelas label[for=parcelas]{
                        margin: 0 auto 0.38rem auto;
                    }
                }

            </style>

            <!-- JavaScript Bundle with Popper -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
            <script>
                const calculateBox = document.querySelector('#input-calc');
                const beneficioSalario = document.querySelector('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>');
                const parcelasEmprest = document.querySelector(".calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>");
                const resultBeneficioSalario = document.querySelector('#resultBeneficioSalario');
                const resultMargem = document.querySelector('.calculadora_margem_disponivel_<?php _e($args['id']); ?>');
                const percentValue = <?php _e($args['porcentagem_da_margem_permitida']); ?>

                /**evento do campo 1 onde sera input com salario ou valor do beneficio */
                beneficioSalario.addEventListener('input',(evt)=>{
                    let event = evt.target.value;

                    //formata os valores de input em formato moeda
                    let tratamento = event.replace(/\D/g, "");
                    tratamento = (tratamento / 100).toFixed(2) + "";
                    tratamento = tratamento.replace(".", ",");
                    tratamento = tratamento.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    evt.target.value = tratamento;

                    tratarSoma(tratamento);
                },false);
                beneficioSalario.addEventListener('input',(evt)=>{
                    let event = evt.target.value;
                    event = event.replace(',',"");
                    //console.log(event);
                    if (event === ""){
                        resultBeneficioSalario.innerHTML = "R$ 0,00";
                    }
                },false);

                /**evento do campo 2 onde sera input com o valor das parcelas */
                parcelasEmprest.addEventListener('input',(evt)=>{
                    let event = evt.target.value;

                    //formata os valores de input em formato moeda
                    let tratamento = event.replace(/\D/g, "");
                    tratamento = (tratamento / 100).toFixed(2) + "";
                    tratamento = tratamento.replace(".", ",");
                    tratamento = tratamento.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    evt.target.value = tratamento;
                    //console.log(`montante1 parcel: ${tratamento}`);

                },false);
                parcelasEmprest.addEventListener('input',(e)=>{
                    let event = e.target.value;
                    //formata os valores de input em formato moeda
                    let tratamento = event.replace(/\D/g, "");
                    tratamento = (tratamento / 100).toFixed(2) + "";
                    tratamento = tratamento.replace(".", ",");
                    tratamento = tratamento.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    e.target.value = tratamento;
                },false);
                parcelasEmprest.addEventListener('input',(evt)=>{
                    let event = evt.target.value;
                    event = event.replace(',',"");
                    event = event.replace('.',"");
                    event = (event / 100);
                    if (event === ""){
                        resultBeneficioSalario.innerHTML = "R$ 0,00";
                    }
                    else if (event != ""){
                        tratarSub(resultBeneficioSalario.textContent,parcelasEmprest.value);
                    }
                    else if(event === 0){
                        /** aqui se o usuario não tiver nenhuma parcela o valor sera igual ao margem permitida */
                        resultMargem.innerHTML = resultBeneficioSalario.textContent;
                    }
                },false);


                /**Functions que realizam tratamentos das saidas de front e para a matemática da calculadora */

                /**Function que faz o tratamento e calculo das entradas no campo 1 (ou input 1) */
                const tratarSoma = (e) =>{
                    if (e.indexOf('.')){
                        e = e.replace(".","");
                        e = e.replace(",","");
                    }
                    let montante = e * (percentValue / 100);
                    //console.log(`Porcentagem: ${percentValue / 100}`);
                    //console.log(`result: ${e}`);
                    //console.log(`montante: ${montante}`);
                    tratamentoResultado(montante);
                    //console.log(`montante1: ${tratamentoResultado(montante)}`);
                };
                /**Function que realizam tratamentos das entradas, saidas e matematica do input de parcelas */
                const tratarSub = (e,x) =>{
                    x = parcelasEmprest.value;
                    if (e.indexOf('.')){
                        e = e.replace(".","");
                        e = e.replace(",","");
                    }
                    if (x.indexOf('.')){
                        x = x.replace(".","");
                        x = x.replace(",","");
                    }
                    let montante = (e - x);
                    //console.log(`result parcel: ${e}`);
                    //console.log(`montante parcel: ${x}`);
                    tratamentoResultadoSub(montante);
                    //console.log(`montante1 parcel: ${tratamentoResultadoSub(montante)}`);
                };

                const tratamentoResultado = (e) => {
                    let valorTarget = (e.valueOf() / 100).toFixed(2)+"";
                    valorTarget = valorTarget.replace(".", ",");
                    valorTarget = valorTarget.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    e = valorTarget;
                    //console.log(`show value tratado: ${e}`);
                    resultBeneficioSalario.innerHTML = e;
                    return e;
                };
                const tratamentoResultadoSub = (e) => {
                    let valorTarget = (e.valueOf() / 100).toFixed(2)+"";
                    valorTarget = valorTarget.replace(".", ",");
                    valorTarget = valorTarget.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    e = valorTarget;
                    //console.log(`show value tratado: ${e}`);
                    resultMargem.innerHTML = e;
                    return e;
                };

                /**Function enable popovers */
                jQuery(function () {
                    $('[data-bs-toggle="popover"]').popover()
                });
                jQuery(function () {
                    $('.parcelas-infor').popover({
                        container: 'body'
                    })
                });

            </script>

            <?php
            //load_template( plugin_dir_path( __FILE__ ) . 'templates/default.php', false, $argsTemplates );
            $temp_content = ob_get_contents();
            ob_end_clean();
            return $temp_content;
        }
    } else {
        return 'Nothing found calculadora';
    }
    wp_reset_postdata();
}
add_shortcode( 'calculadora', 'diletec_calculadora_shortcode' );