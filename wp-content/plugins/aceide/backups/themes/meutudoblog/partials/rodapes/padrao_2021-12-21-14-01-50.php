<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "3b02b8d09ade54f37356fb8b291a3fa13353eb884c" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/rodapes/padrao.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/rodapes/padrao_2021-12-21-14-01-50.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?><?php $bloco = new WP_Query(array(
  'post_type' => 'bloco',
  'name' => 'rodape-padrao',
  'posts_per_page' => 1
)); ?>
<?php if($bloco->have_posts()) : $bloco->the_post(); ?>

  <footer class="padrao">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-4 text-center text-lg-left">
          <a href="<?php echo esc_url(get_home_url()); ?>/">
            <img src="<?php echo get_template_directory_uri(); ?>/images/rodape-padrao-logo.png" width="231" height="73" alt="<?php echo get_bloginfo('name'); ?>" class="logo">
          </a>
          <ul class="sociais justify-content-center justify-content-lg-start">
            <?php if (get_field('redes-sociais')['facebook']) : ?>
              <li>
                <a href="<?php echo get_field('redes-sociais')['facebook']['url']; ?>" target="<?php echo get_field('redes-sociais')['facebook']['target']; ?>" title="<?php get_field('redes-sociais')['facebook']['title']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 42"><g><path d="M18.284 31.829h4.224a.656.656 0 0 0 .656-.656V21.66h2.194a.656.656 0 0 0 .653-.589l.37-3.591a.656.656 0 0 0-.653-.724h-2.561v-1.454c0-.3.1-.323.24-.323h2.271a.656.656 0 0 0 .656-.656v-3.487a.656.656 0 0 0-.654-.656l-3.132-.013a4.62 4.62 0 0 0-3.962 1.775 5.4 5.4 0 0 0-.956 3.145v1.673h-1.353a.656.656 0 0 0-.656.656v3.591a.656.656 0 0 0 .656.656h1.353v9.508a.656.656 0 0 0 .654.658zm-1.353-11.477v-2.278h1.353a.656.656 0 0 0 .656-.656v-2.324a4.162 4.162 0 0 1 .7-2.367 3.377 3.377 0 0 1 2.9-1.241l2.476.01v2.176h-1.615a1.51 1.51 0 0 0-1.553 1.635v2.111a.656.656 0 0 0 .656.656h2.494l-.235 2.278h-2.259a.656.656 0 0 0-.656.656v9.508h-2.911v-9.508a.656.656 0 0 0-.656-.656z" fill="currentColor"/><path d="M35.849 6.151A21 21 0 1 0 42 21a20.861 20.861 0 0 0-6.151-14.849zm-.931 28.767a19.689 19.689 0 1 1 5.766-13.922 19.559 19.559 0 0 1-5.766 13.922z" fill="currentColor"/></g></svg>
                </a>
              </li>
            <?php endif; ?>
            <?php if (get_field('redes-sociais')['instagram']) : ?>
              <li>
                <a href="<?php echo get_field('redes-sociais')['instagram']['url']; ?>" target="<?php echo get_field('redes-sociais')['instagram']['target']; ?>" title="<?php get_field('redes-sociais')['instagram']['title']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 42"><g><g><path d="M35.676 6.118a20.9 20.9 0 1 0 6.121 14.778 20.761 20.761 0 0 0-6.121-14.778zm-.923 28.632a19.593 19.593 0 1 1 5.738-13.854 19.463 19.463 0 0 1-5.738 13.854z" fill="currentColor"/></g><g><path d="M26.387 10.421H15.45a5.035 5.035 0 0 0-5.029 5.029v10.937a5.035 5.035 0 0 0 5.029 5.029h10.937a5.035 5.035 0 0 0 5.029-5.029V15.45a5.035 5.035 0 0 0-5.029-5.029zm3.705 15.966a3.709 3.709 0 0 1-3.705 3.705H15.45a3.709 3.709 0 0 1-3.7-3.705V15.45a3.709 3.709 0 0 1 3.7-3.7h10.937a3.709 3.709 0 0 1 3.705 3.7z" fill="currentColor"/><path d="M26.786 14.258a.806.806 0 1 0 .806.806.808.808 0 0 0-.806-.806z" fill="currentColor"/><path d="M20.919 15.773a5.146 5.146 0 1 0 5.146 5.146 5.152 5.152 0 0 0-5.146-5.146zm0 8.968a3.822 3.822 0 1 1 3.822-3.822 3.826 3.826 0 0 1-3.822 3.822z" fill="currentColor"/></g></g></svg>
                </a>
              </li>
            <?php endif; ?>
            <?php if (get_field('redes-sociais')['linkedin']) : ?>
              <li>
                <a href="<?php echo get_field('redes-sociais')['linkedin']['url']; ?>" target="<?php echo get_field('redes-sociais')['linkedin']['target']; ?>" title="<?php get_field('redes-sociais')['linkedin']['title']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 42"><g><path d="M35.849 6.151A21 21 0 1 0 42 21a20.861 20.861 0 0 0-6.151-14.849zm-.928 28.767a19.688 19.688 0 1 1 5.766-13.922 19.558 19.558 0 0 1-5.766 13.922z" fill="currentColor"/><path d="M26.716 30.601h4.067a.656.656 0 0 0 .656-.656V22.93a6.571 6.571 0 0 0-1.513-4.607 5.043 5.043 0 0 0-3.827-1.554 4.71 4.71 0 0 0-3.009.954v-.011a.656.656 0 0 0-.656-.656h-4.06a.657.657 0 0 0-.656.686c.051 1.117 0 12.089 0 12.2a.656.656 0 0 0 .656.659h4.065a.656.656 0 0 0 .656-.656v-6.832a2.2 2.2 0 0 1 .086-.746 1.6 1.6 0 0 1 1.478-1.078c.384 0 1.4 0 1.4 2.111v6.545a.656.656 0 0 0 .657.656zm-2.061-10.625a2.862 2.862 0 0 0-2.693 1.9 3.235 3.235 0 0 0-.183 1.241v6.176h-2.75c.009-2.121.034-8.651.01-10.92h2.74v1.076a.656.656 0 0 0 1.207.357 3.372 3.372 0 0 1 3.114-1.72c2.559 0 4.027 1.767 4.027 4.849v6.358h-2.753v-5.889a4.174 4.174 0 0 0-.592-2.37 2.444 2.444 0 0 0-2.126-1.058z" fill="currentColor"/><path d="M17.041 13.913a2.948 2.948 0 1 0-2.983 2.784h.024a2.773 2.773 0 0 0 2.956-2.77zm-2.959 1.471h-.027a1.446 1.446 0 0 1-1.589-1.458 1.475 1.475 0 0 1 1.643-1.458 1.456 1.456 0 0 1 1.618 1.465 1.476 1.476 0 0 1-1.645 1.451z" fill="currentColor"/><path d="M12.052 17.056a.656.656 0 0 0-.656.656v12.233a.656.656 0 0 0 .656.656h4.068a.656.656 0 0 0 .656-.656V17.712a.656.656 0 0 0-.656-.656zm3.412 12.233H12.71v-10.92h2.756z" fill="currentColor"/></g></svg>
                </a>
              </li>
            <?php endif; ?>
            <?php if (get_field('redes-sociais')['youtube']) : ?>
              <li>
                <a href="<?php echo get_field('redes-sociais')['youtube']['url']; ?>" target="<?php echo get_field('redes-sociais')['youtube']['target']; ?>" title="<?php get_field('redes-sociais')['youtube']['title']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.907 40.908"><g><path d="M34.913 5.991a20.456 20.456 0 1 0 5.991 14.463 20.319 20.319 0 0 0-5.991-14.463zm-.9 28.023a19.177 19.177 0 1 1 5.616-13.56 19.05 19.05 0 0 1-5.616 13.56z" fill="currentColor"/><path d="M31.464 15.917a5.511 5.511 0 0 0-1-2.576 3.432 3.432 0 0 0-2.387-1.141l-.116-.013h-.029c-2.937-.227-7.369-.23-7.414-.23h-.01c-.044 0-4.477 0-7.413.23h-.029l-.117.013a3.433 3.433 0 0 0-2.387 1.141 5.511 5.511 0 0 0-1 2.576v.011a36.349 36.349 0 0 0-.215 3.707v1.7a36.341 36.341 0 0 0 .215 3.708v.01a5.5 5.5 0 0 0 1 2.575 3.664 3.664 0 0 0 2.426 1.121c.1.013.184.024.242.035.02 0 .04.007.06.009 1.694.173 6.991.227 7.223.229.045 0 4.481-.011 7.417-.235h.032l.12-.013a3.441 3.441 0 0 0 2.38-1.142 5.506 5.506 0 0 0 1-2.574v-.011a36.392 36.392 0 0 0 .215-3.708v-1.7a36.4 36.4 0 0 0-.215-3.708zm-1.062 5.421c0 1.71-.193 3.447-.206 3.556a4.356 4.356 0 0 1-.671 1.872l-.007.008a2.185 2.185 0 0 1-1.564.727l-.133.015c-2.888.22-7.262.231-7.3.231-.054 0-5.4-.054-7.071-.22a4.909 4.909 0 0 0-.3-.043 2.429 2.429 0 0 1-1.641-.71l-.007-.008a4.252 4.252 0 0 1-.67-1.873 35.597 35.597 0 0 1-.206-3.555v-1.7c0-1.71.194-3.447.206-3.555a4.364 4.364 0 0 1 .671-1.875l.007-.007a2.179 2.179 0 0 1 1.567-.726l.129-.014c2.887-.223 7.257-.225 7.3-.225h.01c.044 0 4.414 0 7.3.225l.129.014a2.177 2.177 0 0 1 1.567.726l.007.008a4.26 4.26 0 0 1 .671 1.875c.013.111.206 1.847.206 3.555z" fill="currentColor"/><path d="M24.796 19.941l-6.58-3.948a.639.639 0 0 0-.968.548v7.9a.639.639 0 0 0 .968.548l6.58-3.948a.639.639 0 0 0 0-1.1zm-6.27 3.368V17.67l4.7 2.819z" fill="currentColor"/></g></svg>
                </a>
              </li>
            <?php endif; ?>
          </ul>
          <div class="d-none d-lg-block">
            <p class="titulo"><?php echo get_field('conquistas')['titulo']; ?></p>
            <?php if (get_field('conquistas')['imagem']) : ?>
              <img src="<?php echo get_field('conquistas')['imagem']['url']; ?>" width="<?php echo get_field('conquistas')['imagem']['width']; ?>" height="<?php echo get_field('conquistas')['imagem']['height']; ?>" alt="<?php echo get_field('conquistas')['titulo']; ?>" class="conquistas-imagem">
            <?php endif; ?>
          </div>
        </div>
        <div class="col-12 col-md-4 d-lg-none">
          <p class="titulo"><?php echo get_field('conquistas')['titulo']; ?></p>
          <?php if (get_field('conquistas')['imagem']) : ?>
            <img src="<?php echo get_field('conquistas')['imagem']['url']; ?>" width="<?php echo get_field('conquistas')['imagem']['width']; ?>" height="<?php echo get_field('conquistas')['imagem']['height']; ?>" alt="<?php echo get_field('conquistas')['titulo']; ?>" class="conquistas-imagem">
          <?php endif; ?>
        </div>
        <div class="col-12 col-md-5 col-lg-4">
          <p class="titulo"><?php echo get_field('contatos')['titulo']; ?></p>
          <ul class="contatos">
            <li class="email"><?php echo get_field('contatos')['email']; ?></li>
            <li class="telefone">
              <span class="numero">
                <img src="<?php echo get_template_directory_uri(); ?>/images/rodape-icone-whatsapp.svg" class="icone">
                <?php echo get_field('contatos')['telefone']; ?>
              </span>
            </li>
            <li class="telefone">
              <span class="rotulo"><?php echo get_field('contatos')['telefone-auxiliar-1']['rotulo']; ?></span>
              <span class="numero">
                <img src="<?php echo get_template_directory_uri(); ?>/images/rodape-icone-telefone.svg" class="icone">
                <?php echo get_field('contatos')['telefone-auxiliar-1']['telefone']; ?>
              </span>
            </li>
            <li class="telefone">
              <span class="rotulo"><?php echo get_field('contatos')['telefone-auxiliar-1']['rotulo']; ?></span>
              <span class="numero">
                <img src="<?php echo get_template_directory_uri(); ?>/images/rodape-icone-telefone.svg" class="icone">
                <?php echo get_field('contatos')['telefone-auxiliar-1']['telefone']; ?>
              </span>
            </li>
          </ul>
        </div>
        <div class="col-12 col-md-4 col-lg-3 offset-xl-1">
          <p class="titulo"><?php echo get_field('institucional')['titulo']; ?></p>
          <?php wp_nav_menu(array(
            'theme_location' => 'rodape',
            'menu_class' => 'menu institucional',
            'container' => false
          )); ?>
        </div>
      </div>

      <div class="separador"></div>

      <div class="row">
        <div class="col-12">
          <div class="termo">
            <?php echo get_field('termo')['texto']; ?>
          </div>
        </div>
      </div>

      <div class="separador"></div>

      <div class="row align-items-center">
        <div class="col-12 col-md-auto">
          <?php wp_nav_menu(array(
            'theme_location' => 'rodape-auxiliar',
            'menu_class' => 'menu auxiliar',
            'container' => false
          )); ?>
        </div>
        <div class="col"></div>
        <div class="col-12 col-md-auto text-center">
          <img src="<?php echo get_template_directory_uri(); ?>/images/rodape-padrao-logo-mcafee.png" alt="McAfee Secure" class="mcafee-logo">
        </div>
      </div>
    </div>
  </footer>

<?php endif; wp_reset_postdata(); ?>