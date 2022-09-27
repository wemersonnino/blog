<div class="w-100"></div>
<section id="box-calculadora" class="calculadora">
    <header id="titulo-calculadora" class="titulo_<?php _e($args['id']); ?>">
        <h2><?php _e($args['titulo_do_resultado']); ?></h2>
    </header>
    <main id="input-calc" class="input-calc bg-silver">
        <div class="row">
            <div id="boxBeneficioSalario" class="col calculadora_body_<?php _e($args['id']); ?>">
                <div id="boxBeneficioSalario" class="col">
                    <label for="beneficioSalario" class="form-label"><?php _e($args['valor_do_beneficio']); ?> <span class="align-content-around text-start"><a id="beneficioSalario-infor" class="float-end" href="#">i</a></span></label>
                    <div class="input-group mb-3 form-floating">
                        <span class="input-group-text" id="basic-addon1">R$</span>
                        <input type="text" id="beneficioSalario" name="beneficioSalario"
                               inputmode="numeric" min="1" max="5000000"
                               class="form-control calculadora_valor_do_beneficio_<?php _e($args['id']); ?>"
                               placeholder="0,00"
                               aria-label="<?php _e($args['valor_do_beneficio']); ?>"
                        >
                    </div>
                </div>
            </div><!--/input 1 -->
            <div id="boxParcelas" class="col">
                <label for="parcelas" class="form-label text-start"><?php _e($args['parcela_de_emprestimo']); ?> <a id="parcelas-infor" class="float-end" href="#">i</a></span></label>
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
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12">
                    <h3><?php _e($args['titulo_da_margem_permitida']); ?></h3>
                    <p>
                        <?php _e($args['descricao_da_margem_permitida']); ?>
                    </p>
                </div><!--/Text box ref input 01 -->
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                    <div class="row justify-content-start">
                        <div class="col">
                            <p class="fs-6">R$ <span id="resultBeneficioSalario" class="calculadora_margem_permitida_<?php _e($args['id']); ?>">000,00</span></p>
                        </div>
                        <div class="col">
                            <p class="text-end"><a class="btn-link text-light" href="#">i</a></p>
                        </div>
                    </div>
                </div><!--/Text box result ref input 01 -->
            </div><!--\Ref. Result and text All elements input 01-->
            <div class="row justify-content-around">
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12">
                    <h3><?php _e($args['titulo_da_margem_disponivel']); ?></h3>
                    <p>
                        <?php _e($args['descricao_da_margem_disponivel']); ?>
                    </p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                    <div class="row justify-content-start">
                        <div class="col">
                            <p class="fs-6 calculadora_margem_disponivel_<?php _e($args['id']); ?>">R$ 000,00</p>
                        </div>
                        <div class="col">
                            <p class="text-end"><a class="btn-link text-light" href="#">i</a></p>
                        </div>
                    </div>
                </div>
            </div><!--\Result Ref. All elements for input 02-->
        </main>
    </footer>
</section><!--\Calculadora Box-->
<div class="w-100"></div><br>
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
        /*padding: 10px;*/
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
        text-align: justify;

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
        #box-calculadora > 0.5rem auto;
        }

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
<script>
    /** Função de calculus */
    function calculadora_<?php _e($args['id']); ?>(){
        let valor_do_beneficio = jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').val();
        let parcela_de_emprestimo = jQuery('.calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>').val();

        /** Faz com que o usuário inicie digitando os centavos */
        switch(valor_do_beneficio.length){
            case 1:
                jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').val('0,0'+valor_do_beneficio);
                break;
            case 2:
                jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').val('0,'+valor_do_beneficio);
                break;
            case 5:
                if(Number.isInteger(parseFloat(valor_do_beneficio)) && parseFloat(valor_do_beneficio) > 0 && isFloat(parseFloat(valor_do_beneficio.replace(',','.'))) == false){
                    val = parseFloat(valor_do_beneficio)+',00';
                }else{
                    val1 = parseFloat(valor_do_beneficio.replace(',','.'));
                    val = val1.toFixed(2).replace('.',',');
                }
                jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').val(val);
                break;
        }
        switch(parcela_de_emprestimo.length){
            case 1:
                jQuery('.calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>').val('0,0'+parcela_de_emprestimo);
                break;
            case 5:
                if(Number.isInteger(parseFloat(parcela_de_emprestimo)) && parseFloat(parcela_de_emprestimo) > 0 && isFloat(parseFloat(parcela_de_emprestimo.replace(',','.'))) == false){
                    val = parseFloat(parcela_de_emprestimo)+',00';
                }else{
                    val1 = parseFloat(parcela_de_emprestimo.replace(',','.'));
                    val = val1.toFixed(2).replace('.',',');
                }
                jQuery('.calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>').val(val);
                break;
        }

        /** Matemarica da calculadora */
        if(valor_do_beneficio.length == 1){
            valor_do_beneficio = '0,0' + valor_do_beneficio;
        }else if(valor_do_beneficio.length == 2){
            valor_do_beneficio = '0,' + valor_do_beneficio;
        }
        let margem_permitida = parseFloat(valor_do_beneficio.replace('.','').replace(',','.')) * (<?php _e(str_replace(',', '.', $args['porcentagem_da_margem_permitida'])); ?>);
        let margem_disponivel = parseFloat(valor_do_beneficio.replace('.','').replace(',','.')) - parseFloat(parcela_de_emprestimo.replace(',','.'));
        if(isNaN(margem_disponivel)){
            margem_disponivel = 0;
        }
        if(isNaN(margem_permitida)){
            margem_permitida = 0;
        }
        jQuery('.calculadora_margem_permitida_<?php _e($args['id']); ?>').html("R$"+margem_permitida.toFixed(2).replace('.',','));
        jQuery('.calculadora_margem_disponivel_<?php _e($args['id']); ?>').html("R$"+margem_disponivel.toFixed(2).replace('.',','));
    }
    /** Evento de digitação */
    jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').keyup(function(){
        calculadora_<?php _e($args['id']); ?>();
    });
    jQuery('.calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>').keyup(function(){
        calculadora_<?php _e($args['id']); ?>();
    });
    function isFloat(n){
        return Number(n) === n && n % 1 !== 0;
    }

    /** On-the-fly mask change */
    const options = {
        onKeyPress: function (real, e, field, options) {
            var masks = ['0,00', '00,00', '000,00', '0.000,00', '0.000.000,00', '00.000.000,00', '000.000.000,00', '0.000.000.000,00'];
            var mask = (real.length > 7) ? masks[6] : masks[real.length];
            //var mask = masks[real.length];
            jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').mask(mask, options);
        },
        reverse: true
    };
    jQuery('.calculadora_valor_do_beneficio_<?php _e($args['id']); ?>').mask('0.000.000.000,00', options);
    jQuery('.calculadora_parcela_de_emprestimo_<?php _e($args['id']); ?>').mask('0.000.000.000,00', options);


</script>
