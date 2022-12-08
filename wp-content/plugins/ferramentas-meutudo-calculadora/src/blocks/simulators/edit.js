/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const {
		taxaConsignado,pagamentoConsignado,
		taxaPortabilidade,pagamentoPortabilidade,
		taxaSaqueAniversario,pagamentoSaqueAniversario
	} = attributes;

	return (
		<>
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
		</>
	);
}
