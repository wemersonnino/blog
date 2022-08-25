<?php $bloco = new WP_Query(array(
  'post_type' => 'bloco',
  'name' => 'bloco-newsletter',
  'posts_per_page' => 1
)); ?>
<?php if($bloco->have_posts()) : $bloco->the_post(); ?>

  <section class="bloco-newsletter">
    <?php echo do_shortcode(get_field('shortcode')); ?>
  </section>

<?php endif; wp_reset_postdata(); ?>