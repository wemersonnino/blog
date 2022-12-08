<?php
function up_simulador_meutudo_render_cb($atts){
	$taxaConsignado             = esc_html($atts['taxaConsignado']);
	$pagamentoConsignado        = esc_html($atts['pagamentoConsignado']);
	$taxaPortabilidade          = esc_html($atts['taxaPortabilidade']);
	$pagamentoPortabilidade     = esc_html($atts['pagamentoPortabilidade']);
	$taxaSaqueAniversario       = esc_html($atts['taxaSaqueAniversario']);
	$pagamentoSaqueAniversario  = esc_html($atts['pagamentoSaqueAniversario']);
	ob_start();
	?>

	<section class="wp-block-create-block-simulators-meutudo mt-2 mb-4">
		<div class="w-100"></div><br>
		<div class="mt-table-simulador">
			<table>
				<thead>
				<tr>
					<th colspan="4"><span><h2>Confira as melhores soluções <strong>meutudo</strong> para você</h2></span></th>
				</tr>
				</thead>
				<tbody>
				<tr class="subtitle first">
					<td scope="col" colspan="1"><span>Produto</span></td>
					<td scope="col" colspan="1"><span>Taxa a partir de</span></td>
					<td scope="col" colspan="1"><span>Pagamento de</span></td>
					<td scope="col" colspan="1"></td>
				</tr>
				<tr class="odd">
					<td data-label="Produto" colspan="1"><span>Empréstimo consignado</span></td>
					<td data-label="Taxa a partir de" colspan="1"><span><?= $taxaConsignado?></span></td>
					<td data-label="Pagamento de" colspan="1"><span><?= $pagamentoConsignado?></span></td>
					<td data-label="" colspan="1">
                        <span>
                            <a href="https://web.meutudo.app/register/pre-register" class="btnSimulador" type="button">Simular</a>
                        </span>
					</td>
				</tr>
				<tr class="even">
					<td data-label="Produto" colspan="1"><span>Portabilidade consignado</span></td>
					<td data-label="Taxa a partir de" colspan="1"><span><?= $taxaPortabilidade?></span></td>
					<td data-label="Pagamento de" colspan="1"><span><?= $pagamentoPortabilidade?></span></td>
					<td data-label="" colspan="1">
                        <span>
                            <a href="https://web.meutudo.app/register/pre-register" class="btnSimulador" type="button">Simular</a>
                        </span>
					</td>
				</tr>
				<tr class="odd">
					<td data-label="Produto" colspan="1"><span>Antecipação Saque-aniversário</span></td>
					<td data-label="Taxa a partir de" colspan="1"><span><?= $taxaSaqueAniversario?></span></td>
					<td data-label="Pagamento de" colspan="1"><span><?= $pagamentoSaqueAniversario?></span></td>
					<td data-label="" colspan="1">
                        <span>
                            <a href="https://web.meutudo.app/register/pre-register" class="btnSimulador" type="button">Simular</a>
                        </span>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="w-100"></div><br>
	</section>

	<?php
	$temp_content = ob_get_contents();
	ob_end_clean();
	return $temp_content;
}
