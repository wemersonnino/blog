<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "9433840641e6ce07f8bdcff4771a0c07401e69af4b" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/topos/padrao.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/topos/padrao_2021-10-07-13-51-53.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php global $wp; ?>
<?php $query_busca = get_query_var('busca'); ?>

<?php $bloco = new WP_Query(array(
  'post_type' => 'bloco',
  'name' => 'topo-padrao',
  'posts_per_page' => 1
)); ?>
<?php if($bloco->have_posts()) : $bloco->the_post(); ?>

  <header class="padrao">
    <div class="container">
      <div class="row align-items-center">
        <div class="col col-lg-auto">
          <a href="<?php echo esc_url(home_url()); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/topo-padrao-logo.png" alt="<?php echo get_bloginfo('name'); ?>" class="logo">
          </a>
        </div>
        <div class="col d-none d-lg-block"></div>
        <div class="col-auto ml-auto px-0 px-sm-3 ml-lg-0">
          <?php if (get_field('cta')) : ?>
            <a href="<?php echo get_field('cta')['url']; ?>" class="rounded-button" target="<?php echo get_field('cta')['target']; ?>"><?php echo get_field('cta')['title']; ?></a>
          <?php endif; ?>
        </div>
        <div class="col-auto d-lg-none">
          <div class="hamburger-holder"></div>
        </div>
      </div>
      <div class="row align-items-center d-none d-lg-flex">
        <div class="col">
          <nav>
            <?php wp_nav_menu(array(
              'theme_location' => 'principal',
              'menu_class' => 'menu',
              'container' => false
            )); ?>
          </nav>
        </div>
        <div class="col-auto ml-auto">
          <div id="pesquisa">
            <form action="<?php echo esc_url(home_url()); ?>" method="GET">
              <button class="botao-alternar" type="button" title="Pesquisar">
                <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.038 15.796"><path d="M.335 1.877A1.091 1.091 0 1 1 1.847.303l7.091 6.8a1.089 1.089 0 0 1 0 1.512l-7.091 6.8a1.091 1.091 0 0 1-1.512-1.564l6.274-6.048z" fill="currentColor"/><path d="M15.703 13.92a1.091 1.091 0 1 1-1.512 1.572L7.1 8.688a1.081 1.081 0 0 1 0-1.512L14.191.372a1.081 1.081 0 0 1 1.512.068 1.089 1.089 0 0 1 0 1.512L9.436 8.001z" fill="currentColor"/></svg>
              </button>
              <button class="botao-pesquisar" type="submit" title="Pesquisar">
                <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.556 19.556"><g><g><g><path d="M15.106 5.399a.722.722 0 0 0 0-1.022 4.938 4.938 0 0 0-4-1.412.723.723 0 0 0-.651.718v.073a.723.723 0 0 0 .791.647 3.5 3.5 0 0 1 2.837 1 .722.722 0 0 0 1.023-.004z" fill="currentColor"/></g></g><g><g><path d="M19.556 8.261a8.261 8.261 0 1 0-8.261 8.261 8.27 8.27 0 0 0 8.261-8.261zm-15.076 0a6.816 6.816 0 1 1 6.816 6.816A6.823 6.823 0 0 1 4.48 8.261z" fill="currentColor"/></g></g><g><g><path d="M1.233 19.345l5.25-5.25a.723.723 0 1 0-1.022-1.022l-5.25 5.25a.723.723 0 0 0 1.022 1.022z" fill="currentColor"/></g></g></g></svg>
              </button>
              <div class="sobreposicao">
                <input type="text" name="s" placeholder="<?php echo get_field('pesquisa')['rotulo']; ?>" value="<?php echo $query_busca; ?>" required="">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </header>

  <section class="menu-hamburger">
    <div class="container">
      <button class="hamburger hamburger--elastic" type="button" id="menu">
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
    </div>
  </section>

  <nav class="mobile">
    <div class="header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-auto">
            <button class="botao-pesquisar" type="submit" title="Pesquisar">
              <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.556 19.556"><g><g><g><path d="M15.106 5.399a.722.722 0 0 0 0-1.022 4.938 4.938 0 0 0-4-1.412.723.723 0 0 0-.651.718v.073a.723.723 0 0 0 .791.647 3.5 3.5 0 0 1 2.837 1 .722.722 0 0 0 1.023-.004z" fill="currentColor"/></g></g><g><g><path d="M19.556 8.261a8.261 8.261 0 1 0-8.261 8.261 8.27 8.27 0 0 0 8.261-8.261zm-15.076 0a6.816 6.816 0 1 1 6.816 6.816A6.823 6.823 0 0 1 4.48 8.261z" fill="currentColor"/></g></g><g><g><path d="M1.233 19.345l5.25-5.25a.723.723 0 1 0-1.022-1.022l-5.25 5.25a.723.723 0 0 0 1.022 1.022z" fill="currentColor"/></g></g></g></svg>
            </button>
          </div>
          <div class="col text-center">
            <?php if (get_field('cta')) : ?>
              <a href="<?php echo get_field('cta')['url']; ?>" class="rounded-button white" target="<?php echo get_field('cta')['target']; ?>"><?php echo get_field('cta')['title']; ?></a>
            <?php endif; ?>
          </div>
          <div class="col-auto">
            <div class="hamburger-holder"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="search">
      <div class="row align-items-center">
        <div class="col">
          <form action="<?php echo esc_url(home_url()); ?>" method="GET">
            <input type="text" name="s" placeholder="<?php echo get_field('pesquisa')['rotulo']; ?>" value="<?php echo $query_busca; ?>" required="">
            <button type="submit">
              <svg class="icone" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.556 19.556"><g><g><g><path d="M15.106 5.399a.722.722 0 0 0 0-1.022 4.938 4.938 0 0 0-4-1.412.723.723 0 0 0-.651.718v.073a.723.723 0 0 0 .791.647 3.5 3.5 0 0 1 2.837 1 .722.722 0 0 0 1.023-.004z" fill="currentColor"/></g></g><g><g><path d="M19.556 8.261a8.261 8.261 0 1 0-8.261 8.261 8.27 8.27 0 0 0 8.261-8.261zm-15.076 0a6.816 6.816 0 1 1 6.816 6.816A6.823 6.823 0 0 1 4.48 8.261z" fill="currentColor"/></g></g><g><g><path d="M1.233 19.345l5.25-5.25a.723.723 0 1 0-1.022-1.022l-5.25 5.25a.723.723 0 0 0 1.022 1.022z" fill="currentColor"/></g></g></g></svg>
            </button>
          </form>
        </div>
        <div class="col-auto">
          <div class="hamburger-holder"></div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <?php wp_nav_menu(array(
              'theme_location' => 'principal',
              'menu_class' => 'menu mobile',
              'container' => false
            )); ?>
          </div>
        </div>
      </div>
    </div>
  </nav>
  
  <?php if(function_exists('yoast_breadcrumb') && !(is_home() || is_front_page())) : echo custom_yoast_breadcrumb(get_yoast_breadcrumb_array()); endif; ?>
  
  <!--
  <?php yoast_breadcrumb('', '', true); ?>
  <?php print_r(get_yoast_breadcrumb_array()); ?>
  -->

<?php endif; wp_reset_postdata(); ?>