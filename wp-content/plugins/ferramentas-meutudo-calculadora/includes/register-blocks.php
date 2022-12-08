<?php
function meutudo_register_blocks() {
	$blocks = [
		[
			'name' 		=> 'simulators',
			'options'	=> ['render_callback' => 'up_simulador_meutudo_render_cb']
		]
	];

	foreach($blocks as $block) {
		register_block_type(
			MEUTUDO_PLUGIN_DIR . 'build/blocks/' . $block['name'],
			$block['options'] ?? []
		);
	}
}
