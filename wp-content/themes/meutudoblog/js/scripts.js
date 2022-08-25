jQuery(function($) {
  /* Smooth Scroll */
  $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
    if(location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      if(!this.hash.startsWith('#modal-')) {
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if(target.length) {
          event.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000, function() {
            var $target = $(target);
            $target.focus();
            if($target.is(":focus")) {
              return false;
            } else {
              $target.attr('tabindex','-1');
              $target.focus();
            };
          });
        }
      }
    }
  });

  $('#pesquisa').find('.botao-alternar').click(function(e) {
    e.preventDefault();
    e.stopPropagation();
    let button = $(this);
    let parent = $(this).closest('#pesquisa');

    if(parent.hasClass('aberta')) {
      parent.removeClass('aberta');
      button.attr('title', 'Pesquisar');
    } else {
      parent.addClass('aberta');
      parent.find('input').focus();
      button.attr('title', 'Fechar');
    }
  });

  /* Menu Mobile */
  $('#menu').click(function() {
    let page = $('html, body');
    let backdrop = $('#backdrop');
    let body = $('body');
    let nav = $('nav.mobile');
    let hamburger = $(this);
    let compartilhar = $('section.bloco-compartilhar-lateral');
    
    if(body.hasClass('menu-mobile-active')) {
      if(nav.hasClass('pesquisa')) {
        nav.removeClass('pesquisa');
        nav.find('input[type=text]').val('');
      } else {
        hamburger.removeClass('is-active');
        body.removeClass('menu-mobile-active');
        backdrop.stop().fadeOut(300);
      }
    } else {
      page.animate({ scrollTop: 0 }, 50);
      backdrop.css('z-index', nav.css('z-index') != 'auto' ? parseInt(nav.css('z-index')) - 1 : 9998);
      backdrop.stop().fadeIn(300);
      hamburger.addClass('is-active');
      body.addClass('menu-mobile-active');
      compartilhar.removeClass('aberto');
    }
  });

  $('nav.mobile .botao-pesquisar').click(function (e) {
    let nav = $(this).closest('nav.mobile');

    if (nav.hasClass('pesquisa')) {
      nav.removeClass('pesquisa');
    } else {
      nav.addClass('pesquisa');
      nav.find('input[type=text]').focus();
    }
  });

  /* Backdrop */
  $('#backdrop').click(function(e) {
    let backdrop = $('#backdrop');
    let body = $('body');
    let nav = $('nav.mobile');
    let compartilhar = $('section.bloco-compartilhar-lateral');

    nav.removeClass('pesquisa');
    body.removeClass('menu-mobile-active');
    body.removeClass('share-buttons-active');
    compartilhar.removeClass('aberto');
    compartilhar.css('z-index', 9999);
    backdrop.stop().fadeOut(300);
  });

  /* Lity bind or menus */
  $(document).on('click', '.menu .lightbox a', lity);
  $(document).on('click', '.menu .lightbox a', function() {
    let body = $('body');
    let nav = $('section.menu-mobile');
    let hamburger = $('header.cabecalho-padrao .hamburger');

    nav.stop().fadeOut(300);
    hamburger.removeClass('is-active');
    body.removeClass('menu-mobile-active');
  });

  $('nav.mobile ul.menu > li.menu-item-has-children').click(function(e) {
    if ($(e.target).is('li')) {
      let item = $(this);
      let submenu = item.find('ul.sub-menu');
  
      if (item.hasClass('opened')) {
        item.removeClass('opened');
        submenu.stop().slideUp();
      } else {
        $('nav.mobile ul.menu > li.menu-item-has-children > ul.sub-menu').not(submenu).slideUp();
        item.addClass('opened');
        submenu.stop().slideDown();
      }
    }
  });

  /* Compartilhar */
  $('section.bloco-compartilhar-lateral .botao').click(function(e) {
    e.preventDefault();
    let backdrop = $('#backdrop');
    let body = $('body');
    let parent = $(this).closest('section.bloco-compartilhar-lateral');

    if (parent.hasClass('aberto')) {
      body.removeClass('share-buttons-active');
      parent.css('z-index', 9999);
      parent.removeClass('aberto');
      backdrop.stop().fadeOut(300);
    } else {
      body.addClass('share-buttons-active');
      parent.css('z-index', 999999);
      parent.addClass('aberto');
      backdrop.css('z-index', parent.css('z-index') != 'auto' ? parseInt(parent.css('z-index')) - 1 : 9998);
      backdrop.stop().fadeIn(300);
    }
  });
});