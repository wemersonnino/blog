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
						<a href="<?php the_permalink($categoria->post_title); ?>" class="btn bg-dark rounded-pill text-white p-4" type="button">
							<div class="row align-items-center">
								<div class="col-2">
									icon
								</div>
								<div class="col-10">
									<h5 id="title-post-calculadora-query"><?php echo $post->post_title ?></h5>
								</div>
							</div>
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
