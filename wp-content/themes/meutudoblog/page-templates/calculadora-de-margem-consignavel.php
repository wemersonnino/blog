<?php
/*
 Template Name: calculadora consignavel
 */
?>
<?php get_header(); ?>
<?php get_template_part('/partials/topos/padrao'); ?><!--//header-->
    <main id="main-container">
        <section class="container">
            <article class="row justify-content-start">
                <?php ?>
                <div class="w-100"></div><br><br>
                <section id="content-post" class="post-conteudo col-md-8 col-lg-8 col-xl-8">
                    <div id="post-<?php the_ID(); ?>" <?php post_class('row align-items-center'); ?>>
                        <h1 class="title-nome nome">
                            <?php the_title(); ?>
                        </h1><!--\title-->
                        <div class="w-100"></div><br><br>
                        <div class="conteudo-wysiwyg meta">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dolor est, fringilla in maximus sit amet, accumsan sit amet nisi.
                            </p>
                            <div class="w-100"></div><br><br>
                            <section id="box-calculadora" class="calculadora">
                                <header id="titulo-calculadora">
                                    <h4>Cálculo de Margem Consignável</h4>
                                </header>
                                <main id="input-calc" class="input-calc bg-silver">
                                    <div class="row">
                                        <div id="boxbeneficioSalatio" class="col">
                                            <label for="beneficioSalatio" class="form-label">Valor do benefício / salário <span class="align-content-around text-end"><a id="beneficioSalatio-infor" href="#">i</a></span></label>
                                            <input type="currency"  inputmode="numeric"
                                                   pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                                   data-type="currency"
                                                   id="beneficioSalatio" class="form-control"
                                                   placeholder="R$ 0.000,00" aria-label="Valor do benefício / salário"
                                                   value="R$ 0.000,00">
                                        </div>
                                        <div class="col">
                                            <label for="parcelas" class="form-label text-end">Parcela de empréstimos do benefício <a id="parcelas-infor" href="#">i</a></span></label>
                                            <input type="text" id="parcelas" class="form-control" placeholder="R$ 000,00" aria-label="Parcela de empréstimos do benefício">
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
                                                        <p class="fs-6">R$ 000,00</p>
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
                            <div class="w-100"></div><br>
                            <article class="row">
                                <h3>
                                    <strong>Aproveite as oportunidades da meutudo.</strong>
                                </h3>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                </p>
                            </article>
                            <div class="w-100"></div><br>
                            <section id="box-simuladores">
                                <header id="box-simuladores-topo">
                                    <div class="row justify-content-around">
                                        <div class="col-md-9 col-lg-9 col-xl-9 col-sm-12">
                                            <h2>
                                                <span>Confira as melhores soluções <strong>meutudo</strong> para você</span>
                                            </h2>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-xl-3 col-sm-12 text-end">
                                            <p><a class="btn-link text-light" href="#">i</a></p>
                                        </div>
                                    </div>
                                </header>
                                <main id="box-simuladores-main">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th scope="col">Produto</th>
                                                <th scope="col">Taxa a partir de</th>
                                                <th scope="col">Pagamento de</th>
                                                <th scope="col"></th>
                                                <th scope="col"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th scope="row">Empréstimo consignado</th>
                                                <td>1,62% a.m.</td>
                                                <td>6 a 84 parcelas</td>
                                                <td>
                                                    <button class="btnSimulador" name="btnSimulador" type="button">Simular</button>
                                                </td>
                                                <td>
                                                    <p><a class="btn-link text-dark" href="#">i</a></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Portabilidade consignado</th>
                                                <td>1,63% a.m.</td>
                                                <td>6 a 84 parcelas</td>
                                                <td>
                                                    <button class="btnSimulador" name="btnSimulador" type="button">Simular</button>
                                                </td>
                                                <td>
                                                    <p><a class="btn-link text-dark" href="#">i</a></p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table><!--\Table simuladores-->
                                    </div>
                                </main>
                            </section><!--\Section simuladores-->
                            <div class="w-100"></div><br>
                            <article>
                                <h5><strong>Como usar a calculadora de margem consignável?</strong></h5>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sapien nunc. Nam congue pellentesque diam elementum laoreet. Suspendisse quam urna, facilisis id convallis at, efficitur nec nibh. Fusce placerat lacus eu sapien ornare rutrum. Pellentesque non ipsum massa. Suspendisse potenti. Fusce ornare amet.
                                </p>
                            </article>
                            <div class="w-100"></div>
                            <article>
                                <h3>Video</h3>
                            </article>
                            <div class="w-100"></div><br>
                            <article class="conteudo-wysiwyg-questions">
                                <h5><strong>Afinal, o que é a margem consignável?</strong></h5>
                                <p>
                                    Afinal, o que é a margem consignável?
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dolor est, fringilla in maximus sit amet, accumsan sit amet nisi. Ut porttitor faucibus sapien, non cursus enim rhoncus a. Aliquam ornare mi non libero eleifend, aliquet rhoncus eros pharetra. Nullam consequat mi luctus, suscipit lorem at, maximus enim. Ut condimentum condimentum metus vel sollicitudin. Fusce accumsan, arcu vel congue tincidunt, lectus quam posuere enim, et bibendum nulla sapien sed ex. Sed eget nisi non magna rutrum consectetur vitae a libero. Aliquam consectetur aliquam tortor. Mauris tempor tincidunt aliquam. Sed consequat sapien mauris, et fermentum quam sollicitudin in. Mauris nulla ante, condimentum in placerat sed, dictum a nibh. Nam diam nisi, molestie quis tempor ut, ornare at ligula. Sed sit amet lectus non elit rhoncus fringilla. Sed nec orci sit amet enim finibus condimentum quis eu erat. Sed nec nunc eget sem iaculis aliquet. Etiam sollicitudin varius ipsum. Praesent placerat nunc et turpis.
                                </p>
                                <h5><strong>Como calcular margem consignável?</strong></h5>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sapien nunc. Nam congue pellentesque diam elementum laoreet. Suspendisse quam urna, facilisis id convallis at, efficitur nec nibh. Fusce placerat lacus eu sapien ornare rutrum. Pellentesque non ipsum massa. Suspendisse potenti. Fusce ornare amet.
                                </p>
                                <h5><strong>Como usar a calculadora de margem consignável da meutudo?</strong></h5>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sapien nunc. Nam congue pellentesque diam elementum laoreet. Suspendisse quam urna, facilisis id convallis at, efficitur nec nibh. Fusce placerat lacus eu sapien ornare rutrum. Pellentesque non ipsum massa. Suspendisse potenti. Fusce ornare amet.
                                </p>
                                <h5><strong>Tenho margem disponível, posso fazer empréstimo?</strong></h5>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sapien nunc. Nam congue pellentesque diam elementum laoreet. Suspendisse quam urna, facilisis id convallis at, efficitur nec nibh. Fusce placerat lacus eu sapien ornare rutrum. Pellentesque non ipsum massa. Suspendisse potenti. Fusce ornare amet.
                                </p>
                                <h5><strong>Não tenho margem disponível, o que fazer agora?</strong></h5>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sapien nunc. Nam congue pellentesque diam elementum laoreet. Suspendisse quam urna, facilisis id convallis at, efficitur nec nibh. Fusce placerat lacus eu sapien ornare rutrum. Pellentesque non ipsum massa. Suspendisse potenti. Fusce ornare amet.
                                </p>
                            </article><!--\Text box questions-->
                            <div class="w-100"></div><br>
                            <section id="box-perguntas-frequentes">

                            </section>
                        </div>
                    </div>
                </section><!--\content post-->
                <?php if (is_active_sidebar('calculadora_sidebar')): ?>
<!--                <aside class="col-md-2 col-lg-2 col-xl-2">-->
<!--                    Sidebar-->
<!--                </aside>-->
                    <?php dynamic_sidebar('calculadora_sidebar'); ?>
                <?php endif; ?>
            </article>
        </section>
    </main>
    <div class="w-100 clearfix"></div><br>


<?php if (get_field('/page-newsletter-habilitado')) get_template_part('partials/blocos/home-calculadoras'); ?>

<?php if (get_field('/page-baixe-o-aplicativo-habilitado'))get_template_part('partials/blocos/baixe-o-aplicativo'); ?>

<?php get_template_part('/partials/rodapes/padrao'); ?>

<?php get_footer(); ?>