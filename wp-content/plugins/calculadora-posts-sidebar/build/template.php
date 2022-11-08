<div id="line"></div>
<?php

$blog_posts_calculadora = new WP_Query([
		'post_type'		=> 'post',
		'posts_per_page'	=> 3,
		'ignore_stick_posts'	=> true,
]);

if ($blog_posts_calculadora->have_posts()){
	while ($blog_posts_calculadora->have_posts()){
		$blog_posts_calculadora->the_post();

?>

<section class="container">
	<article class="row">
		<div class="wp-post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php
					if ( has_post_thumbnail() ){
						the_post_thumbnail('calculadora-query-post',['class' => 'mx-auto d-blocka img-fluid img-thumbnail']);
					}
				?>
			</a>
			<h5>
				<a class="btn btn-link" href="<?php the_permalink(); ?>">
					<?php the_title() ?>
				</a>
			</h5>
		</div>
	</article>
</section>
<?php
	}
	wp_reset_postdata();

}
?>
<div id="line"></div>
