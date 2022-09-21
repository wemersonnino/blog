<section id="box-calculadora" class="calculadora">
    <header id="titulo-calculadora">
        <h4>Cálculo de Margem Consignável</h4>
    </header>
    <main id="input-calc" class="input-calc bg-silver">
        <div class="row">
            <div id="boxBeneficioSalario" class="col">
                <label for="beneficioSalario" class="form-label">Valor do benefício / salário <span class="align-content-around text-start"><a id="beneficioSalario-infor" class="float-end" href="#">i</a></span></label>
                <div class="input-group mb-3 form-floating">
                    <span class="input-group-text" id="basic-addon1">R$</span>
                    <input inputmode="numeric" type="text" min="1" max="5000000"
                           id="beneficioSalario" name="beneficioSalario" class="form-control"
                           placeholder="0,00" value="0,00"
                           aria-label="Valor do benefício / salário"
                    >
                </div>
            </div>
            <div class="col">
                <label for="parcelas" class="form-label text-start">Parcela de empréstimos do benefício <a id="parcelas-infor" class="float-end" href="#">i</a></span></label>
                <div class="input-group mb-3 form-floating">
                    <span class="input-group-text" id="basic-addon2">R$</span>
                    <input inputmode="numeric" min="1" max="5000000" type="text"
                           id="parcelas" class="form-control"
                           placeholder="0,00" value="0,00"
                           aria-label="Parcela de empréstimos do benefício">
                </div>

            </div>
        </div>
    </main>
    <footer id="footer-input-calc">
        <header>
            <h4>Resultado da margem consignável</h4>
        </header>
        <div class="w-100"></div><br>
        <main>
            <div class="row justify-content-around">
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12">
                    <h3>Margem permitida</h3>
                    <p>
                        Com o valor de seu benefício / salário atual, esta é a margem permitida para empréstimo consignado.
                    </p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                    <div class="row justify-content-start">
                        <div class="col">
                            <p class="fs-6">R$ <span id="resultBeneficioSalario">000,00</span></p>
                        </div>
                        <div class="col">
                            <p class="text-end"><a class="btn-link text-light" href="#">i</a></p>
                        </div>
                    </div>
                </div>
            </div><!--\Margem permitida-->
            <div class="row justify-content-around">
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12">
                    <h3>Margem disponível</h3>
                    <p>
                        Parte de sua margem permitida já está sendo utilizada com outro consignável, portanto a sua margem disponível é esta.
                    </p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0">
                    <div class="row justify-content-start">
                        <div class="col">
                            <p class="fs-6">R$ 000,00</p>
                        </div>
                        <div class="col">
                            <p class="text-end"><a class="btn-link text-light" href="#">i</a></p>
                        </div>
                    </div>
                </div>
            </div><!--\Margem disponível-->
        </main>
    </footer>
</section><!--\Calculadora Box-->
<script type="application/ld+json"><?php echo json_encode($schema); ?></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        jQuery(function($) {
            $('beneficioSalario').on('input',function(e) {
                e.preventDefault();
                //const parent = $(e.target).closest('.perguntas');
                //const pergunta = $(e.target).closest('.pergunta-list');
                console.log(e.target.value);

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
