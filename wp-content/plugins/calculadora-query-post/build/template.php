<div id="line"></div>
<?php
$categoria = get_page_by_title('Calculadoras');

$posts = get_pages([
		'child_of' => $categoria->ID,
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'post_type' => 'page',
		'post_status' => 'publish'
]);
?>

	<article id="boxPostsCalculadora">
		<?php if($categoria) : ?>
		<h2>Mais <?php echo $categoria->post_title ?></h2>
		<?php else: ?>
		<h2>Não existe categoria <?php echo $categoria->post_title ?>> presente</h2>
		<?php endif; ?>
		<div>
			<div class="container text-center">
				<div class="row">
					<div class="d-grid gap-2 w-100">
						<?php
						if(!empty($posts)) :
							foreach ($posts as $post):
								if (is_page($post->ID)) continue;
						?>
								<style>
									.btn-calc-<?php  echo $post->ID?> {
										display: flex;
										align-items: center;
										text-align: left;
										padding: 10px 20px !important;
										line-height: 20px;
										font-size: 15px;
										min-height: 65px;
										border: 0;
									}
									.btn-calc-<?php  echo $post->ID?> :hover {
										text-decoration: none;
									}
									@media only screen and (min-width: 35em) {
										/* Style adjustments for viewports that meet the condition */
										.btn-calc-<?php  echo $post->ID?>{
											width:20vw;
										}
									}
								</style>
						<a href="<?php the_permalink($post->ID); ?>" class="btn bg-dark rounded-pill text-white p-4 btn-calc-<?php  echo $post->ID?>" type="button">
							<svg width="18" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin-right: 7px;">
								<rect width="18" height="24" fill="url(#pattern0)"/>
								<defs>
									<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
										<use xlink:href="#image0_241_892" transform="translate(-0.0208333) scale(0.0416667 0.03125)"/>
									</pattern>
									<image id="image0_241_892" width="25" height="32" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAgCAYAAADnnNMGAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAwUlEQVRIie2Xyw3DIBAFhyh3t5BS3IFTijsIJaWEdGJKSAcvB0PkT1CM5b1YrOTLM+wAGg4gifh1kgYdV0PsiZME0AFPbOqeIANwM4IEJ6kB3kYAAK6Z3AOvnT3bOP9b6bi0GOh2AgBWJ2MBWfW7ZAY9djZvfs3N7eTQyu2kQoogntH3lrnzpTmwTeGp96X5Zsj0f2kO/L8nS+9L83EFp78nnmrXPAeqXQdBPNWueQ6c0a5gyAgJ0htC+vRsMH06fACFfSvTxbDprQAAAABJRU5ErkJggg=="/>
								</defs>
							</svg>
							<?php echo $post->post_title ?>
						</a>
						<?php
							endforeach;
						else:?>
						<p>Não existem posts para categoria <?php echo $categoria->post_title?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</article>
