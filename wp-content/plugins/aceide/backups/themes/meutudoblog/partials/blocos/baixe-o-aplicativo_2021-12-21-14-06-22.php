<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "3b02b8d09ade54f37356fb8b291a3fa13353eb884c" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/blocos/baixe-o-aplicativo.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/blocos/baixe-o-aplicativo_2021-12-21-14-06-22.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php $bloco = new WP_Query(array(
  'post_type' => 'bloco',
  'name' => 'bloco-baixe-o-aplicativo',
  'posts_per_page' => 1
)); ?>
<?php if($bloco->have_posts()) : $bloco->the_post(); ?>

  <section class="bloco-baixe-o-aplicativo">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
          <div class="row align-items-center">
            <div class="col-12 col-lg-auto">
              <?php if (get_field('logo')) : ?>
                <img src="<?php echo get_field('logo')['url']; ?>" alt="<?php echo bloginfo('name'); ?>" class="logo">
              <?php endif; ?>
            </div>
            <div class="col-12 col-md text-center">
              <p class="titulo">
                <?php the_field('titulo'); ?>
              </p>
            </div>
            <div class="col-12 col-md-auto">
              <div class="badges">
                <?php if (get_field('link-app-store')) : ?>
                  <a href="<?php echo get_field('link-app-store'); ?>" target="_blank" class="badge">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/baixe-o-aplicativo-badge-app-store.png" width="504" height="168" alt="<?php _e('Baixe na App Store', 'meutudoblog'); ?>">
                  </a>
                <?php endif; ?>
                <?php if (get_field('link-google-play')) : ?>
                  <a href="<?php echo get_field('link-google-play'); ?>" target="_blank" class="badge">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/baixe-o-aplicativo-badge-google-play.png" width="564" height="168" alt="<?php _e('Baixe no Google Play', 'meutudoblog'); ?>">
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php endif; wp_reset_postdata(); ?>