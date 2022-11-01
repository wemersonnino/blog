import { useBlockProps } from '@wordpress/block-editor';
import './style.scss';

export default function Save(props){
	const blockProps = useBlockProps.save();

	return(
		<>
			<section id="box-simuladores" { ...useBlockProps() }>
				<header id="box-simuladores-topo">
					<div className="row justify-content-around">
						<div className="col-md-9 col-lg-9 col-xl-9 col-sm-12">
							<h2>
								<span>{props.attributes.titleSimulador}</span>
							</h2>
						</div>
						<div className="col-md-3 col-lg-3 col-xl-3 col-sm-12 text-end">
							<p><a className="btn-link text-light" href="#">i</a></p>
						</div>
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
									<button className="btnSimulador" name="btnSimulador" type="button">Simular</button>
								</td>
								<td>
									<p><a className="btn-link text-dark" href="#">i</a></p>
								</td>
							</tr>
							<tr>
								<th scope="row">Portabilidade consignado</th>
								<td>1,63% a.m.</td>
								<td>6 a 84 parcelas</td>
								<td>
									<button className="btnSimulador" name="btnSimulador" type="button">Simular</button>
								</td>
								<td>
									<p><a className="btn-link text-dark" href="#">i</a></p>
								</td>
							</tr>
							</tbody>
						</table>
						{/*//Table simuladores*/}
					</div>
				</main>
			</section>
		</>
	);
}
