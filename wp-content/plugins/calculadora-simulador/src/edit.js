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
import { useBlockProps } from '@wordpress/block-editor';

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
export default function Edit(props) {

	const uptdateSimulador = () =>{
		props.setAttributes({titleSimulador: event.target.value})
	}
	return (
		<section id="box-simuladores" { ...useBlockProps() }>
			<header id="box-simuladores-topo">
				<div className="row justify-content-around">
					<div className="col-md-9 col-lg-9 col-xl-9 col-sm-12">
						<h2>
							<span>Confira as melhores soluções <strong>meutudo</strong> para você</span>
						</h2>
					</div>
					{/*<div className="col-md-3 col-lg-3 col-xl-3 col-sm-12 text-end">*/}
					{/*	<p><a className="btn-link text-light" href="#">i</a></p>*/}
					{/*</div>*/}
				</div>
			</header>
			<main id="box-simuladores-main">
				<div className="table-responsive">
					<table className="table table-striped table-sm">
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
								<a href={'https://web.meutudo.app/register/pre-register'} className="btnSimulador" name="btnSimulador" type="button">Simular</a>
							</td>
							<td>
								{/*<p><a className="btn-link text-dark" href="#">i</a></p>*/}
							</td>
						</tr>
						<tr>
							<th scope="row">Portabilidade consignado</th>
							<td>1,63% a.m.</td>
							<td>6 a 84 parcelas</td>
							<td>
								<a className="btnSimulador" name="btnSimulador" type="button">Simular</a>
							</td>
							<td>
								{/*<p><a className="btn-link text-dark" href="#">i</a></p>*/}
							</td>
						</tr>
						</tbody>
					</table>
					{/*//Table simuladores*/}
				</div>
			</main>
		</section>
	);
}
