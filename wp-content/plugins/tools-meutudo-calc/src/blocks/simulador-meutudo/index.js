import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps,RichText,InspectorControls } from '@wordpress/block-editor';
import {PanelBody, ToggleControl} from "@wordpress/components";
import icons from '../../icons';
import './main.scss';

registerBlockType('tools-meutudo-calc/simulador-meutudo',{
    icon: icons.primary,

    edit({ attributes, setAttributes }){
        const blockProps = useBlockProps();
        const {
            taxaConsignado,pagamentoConsignado,
            taxaPortabilidade,pagamentoPortabilidade,
            taxaSaqueAniversario,pagamentoSaqueAniversario
        } = attributes;

        return(
            <section {...blockProps}>
                <div className="w-100"></div>
                <br/>
                    <section id="box-simuladores">
                        <header id="box-simuladores-topo">
                            <div className="row justify-content-around">
                                <div className="col-md-9 col-lg-9 col-xl-9 col-sm-12">
                                    <h2>
                                        <span>Confira as melhores soluções <strong>meutudo</strong> para você</span>
                                    </h2>
                                </div>
                                <div className="col-md-3 col-lg-3 col-xl-3 col-sm-12 text-end">
                                </div>
                            </div>
                        </header>
                        <main id="box-simuladores-main">
                            <table className="table table-striped">
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
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('1,62% a.m.','tools-meutudo')}
                                            onChange={taxaConsignado => setAttributes({ taxaConsignado })}
                                        />
                                    </td>
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('6 a 84 parcelas','tools-meutudo')}
                                            onChange={pagamentoConsignado => setAttributes({ pagamentoConsignado })}
                                        />
                                    </td>
                                    <td>
                                        <a href="https://web.meutudo.app/register/pre-register" className="btnSimulador" type="button">Simular</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Portabilidade consignado</th>
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('1,75% a.m.','tools-meutudo')}
                                            onChange={taxaPortabilidade => setAttributes({ taxaPortabilidade })}
                                        />
                                    </td>
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('6 a 84 parcelas','tools-meutudo')}
                                            onChange={pagamentoPortabilidade => setAttributes({ pagamentoPortabilidade })}
                                        />
                                    </td>
                                    <td>
                                        <a href="https://web.meutudo.app/register/pre-register" className="btnSimulador" type="button">Simular</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Antecipação Saque-aniversário</th>
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('1,80% a.m.','tools-meutudo')}
                                            onChange={taxaSaqueAniversario => setAttributes({ taxaSaqueAniversario })}
                                        />
                                    </td>
                                    <td>
                                        <RichText
                                            tagName={"p"}
                                            placeholder={__('6 a 84 parcelas','tools-meutudo')}
                                            onChange={pagamentoSaqueAniversario => setAttributes({ pagamentoSaqueAniversario })}
                                        />
                                    </td>
                                    <td>
                                        <a href="https://web.meutudo.app/register/pre-register" className="btnSimulador" type="button">Simular</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </main>
                    </section>
                    <div className="w-100"></div>
                <br/>
            </section>
        )
    }
});