// jQuery(document).ready(function ($) {
//   MasterPopups.on('afterOpen', function ($, popup_instance, popup_id, options) {
//     //Get cookie value( "true" string or null)
//     var conversion = MasterPopups.get_cookie_event('onConversion', options);
//     console.log(conversion);
//
//     //true or false
//     console.log(popup_instance.already_converted());
//   });
// });



window.MasterPopups = (function ($, window, document, undefined) {
  var app = {
    callbacks: [],
    popups: [],
    queue_popups: [],
    opened_popups: [],
    working: 0,
    last_open_event: 'click',
    is_scrolling_down: false,
    recaptcha_error: "<p><strong>Google reCAPTCHA Error</strong><br><br>Invalid Site key or the Site key used is from another reCAPTCHA version.</p>"
  };

  app.msg = function(id, msg, msg2){
    if( id == 1 ){//popup id
      if( msg2 ){
        console.log(msg, msg2);
      } else {
        console.log(msg);
      }
    }
  };


  //Document Ready
  $(function (event) {
    // var v = "v6 ";
    // console.log(v);
    // $('.post-title').text(v+$('.post-title').text());

    var popups_z_index = parseInt(MPP_PUBLIC_JS.popups_z_index, 10);
    var sticky_z_index = parseInt(MPP_PUBLIC_JS.sticky_z_index, 10);

    app.debug = MPP_PUBLIC_JS.debug_mode === 'on';
    app.z_index = {
      overlay: popups_z_index - 1,
      popup: popups_z_index,
      sticky: sticky_z_index,
    };
    app.enable_enqueue_popups = MPP_PUBLIC_JS.enable_enqueue_popups;


    $.each(MPP_POPUP_OPTIONS, function (id) {
      app.popups.unshift(id);
    });
    $.each(app.popups, function (index, id) {
      var options = MPP_POPUP_OPTIONS[id];
      var display = false;

      var has_cookie_not_show_popup = app.has_cookie_not_show_popup(id);

      //On Click
      var onClick = options.triggers.open.onClick;
      var selectors = [];
      selectors.push('.mpp-trigger-popup-' + id);
      selectors.push('a[href*="mpp-trigger-popup-' + id + '"]');
      if (onClick.customClass) {
        $('.' + onClick.customClass).css('cursor', 'pointer');
        selectors.push('.' + onClick.customClass);
        selectors.push('a[href*="' + onClick.customClass + '"]');
      }
      selectors = selectors.join(',');
      var onClickEvent = onClick.event == 'hover' ? 'mouseover' : 'click';
      $(selectors).on(onClickEvent, function (event) {
        if (onClick.preventDefault) {
          event.preventDefault();
        }
        options.open.event = 'click';
        app.open_popup_by_id(id, options);
      });

      if (has_cookie_not_show_popup) {
        return;
      }

      //Load counter
      var cookieCounter = app.create_cookie_loads_counter(id);
      //Mostrar el popup siempre que el usuario ha visitado previamente x páginas
      if (cookieCounter.loadCounter >= 1 && cookieCounter.pageViews < cookieCounter.loadCounter) {
        return;
      }

      //Si el popup es usado como bloqueador de contenido
      if (app.is_content_locker_enabled(options)) {
        if (!app.show_popup_content_locker_whole_page(options)) {
          if (app.is_content_locker_unlocked(options)) {
            app.unlock_page_content(options);
          } else {
            //Inline
            $('.mpp-inline-' + id).MasterPopups(options);
          }
        }
      } else {
        //Si el popup ya a generado conversión entonces no hacer nada
        if (!app.show_popup_on_conversion(options)) {
          return;
        }
        //Inline
        $('.mpp-inline-' + id).MasterPopups(options);
      }

      //CookiePlus Addon support
      if (typeof CookiePlus !== 'undefined') {
        if (!CookiePlus.should_display_popup(id, options)) {
          return;
        }
      }

      if (options.inline.disableTriggers && $('.mpp-inline-' + id).length) {
        return;
      }

      //On Load
      display = false;
      if (app.show_popup_content_locker_whole_page(options)) {
        options.triggers.open.onLoad.enabled = true;
        options.triggers.open.onLoad.delay = 10;
      }
      var onLoad = options.triggers.open.onLoad;
      if (onLoad.enabled && !MPP_PUBLIC_JS.is_admin) {
        //$(window).on('load', function (event) {Fix firefox
          display = true;

          if (app.show_popup_content_locker_whole_page(options)) {
            if (app.is_content_locker_unlocked(options)) {
              display = false;
            }
          } else {
            if (app.get_cookie_event('onLoad', options) || !app.show_popup_on_conversion(options)) {
              display = false;
            }
          }
          if (display) {
            setTimeout(function () {
              options.open.event = 'onLoad';
              app.open_popup_by_id(id, options);
            }, app.parse_number(onLoad.delay));
          }
        //});
      }

      //On Exit
      display = false;
      var onExit = options.triggers.open.onExit;
      if (onExit.enabled && !MPP_PUBLIC_JS.is_admin) {
        $(document).on('mouseleave', function (event) {
          display = true;
          //Internet expolorer y Edge muestran valores mayores a 0 en algunos casos
          if ((app.isIE() && event.clientY > 50) || (!app.isIE() && event.clientY > 0)) {
            display = false;
          }
          if (app.get_cookie_event('onExit', options) || !app.show_popup_on_conversion(options)) {
            display = false;
          }

          if (display) {
            options.open.event = 'onExit';
            app.open_popup_by_id(id, options);
          }
        });
      }

      //On User Inactivity
      display = false;
      var onInactivity = options.triggers.open.onInactivity;
      if (onInactivity.enabled && !MPP_PUBLIC_JS.is_admin) {
        setTimeout(function () {
          $(document).idle({
            idle: app.parse_number(onInactivity.period),
            //keepTracking: ! cookies.onInactivity.enabled,// Aveces falla al cargar la página
            onIdle: function () {
              display = true;
              if (app.get_cookie_event('onInactivity', options) || !app.show_popup_on_conversion(options)) {
                display = false;
              }

              if (display) {
                options.open.event = 'onInactivity';
                app.open_popup_by_id(id, options);
              }
            },
          });
        }, 1500);
      }

      //On Scroll
      display = false;
      var onScroll = options.triggers.open.onScroll;
      if (onScroll.enabled && !MPP_PUBLIC_JS.is_admin) {
        $(window).scroll(function () {
          display = true;
          var instance = app.get_instance(id);
          if (onScroll.displayed || (instance && instance.is_closing)) {
            display = false;
          }
          if (app.get_cookie_event('onScroll', options) || !app.show_popup_on_conversion(options)) {
            display = false;
          }

          if (onScroll.amount != '0px' && onScroll.amount != '0%') {
            if (display && app.is_scrolling_down && app.in_scroll_top(onScroll.amount)) {
              options.open.event = 'onScroll';
              app.open_popup_by_id(id, options);
              onScroll.displayed = true;
            }
          }

          if (onScroll.afterPost) {
            if (display && app.in_scroll_element($('.mpp-after-post-content'), 'top', 200)) {
              options.open.event = 'onScroll';
              app.open_popup_by_id(id, options);
              onScroll.displayed = true;
            }
          }

          if (onScroll.selector && $(onScroll.selector).length) {
            if (display && app.in_scroll_element($(onScroll.selector), 'top', 200)) {
              options.open.event = 'onScroll';
              app.open_popup_by_id(id, options);
              onScroll.displayed = true;
            }
          }
        });
      }
    });
  });//document ready

  //Close with ESC Key
  $(document).on('keydown', function (event) {
    if (event.which == 27) {
      $('.mpp-is-open').each(function (index, popup) {
        var instance = app.get_instance($(popup));
        if (instance.options.triggers.close.onEscKeydown && !instance.is_inline()) {
          instance.close();
        }
      });
    }
  });

  //Close On Scroll Down/Up
  var currentScrollTop = 0, lastScrollTop = 0;
  $(window).scroll(function (event) {
    currentScrollTop = $(this).scrollTop();
    app.is_scrolling_down = currentScrollTop > lastScrollTop;
    lastScrollTop = currentScrollTop;
    $('.mpp-is-open').each(function (index, popup) {
      var instance = app.get_instance($(popup));
      var onScrollDown = instance.options.triggers.close.onScroll;
      var onScrollUp = instance.options.triggers.close.onScrollUp;
      if (app.is_scrolling_down && onScrollDown.enabled && app.in_scroll_top(onScrollDown.amount)) {
        instance.close();
      }
      if (!app.is_scrolling_down && onScrollUp.enabled && app.in_scroll_top(onScrollUp.amount, '<=')) {
        instance.close();
        if (MPP_POPUP_OPTIONS[instance.options.id]) {
          //Permite mostrar otra vez el popup. Sólo cuando se cierra automáticamente en scroll top.
          MPP_POPUP_OPTIONS[instance.options.id].triggers.open.onScroll.displayed = false;
        }
      }
    });
  });

  function MasterPopups(element, options) {
    var _ = this;
    _.$body = $('body');
    _.popup = element;
    _.$popup = $(_.popup);
    _.popup_id = 0;
    _.$container = _.$popup.closest('.mpp-container');
    _.$wrap = _.$popup.find('.mpp-wrap').first();
    _.$wrap_content = _.$wrap.find('.mpp-content').first();
    _.$desktop_content = _.$wrap.find('.mpp-content-desktop').first();
    _.$mobile_content = _.$wrap.find('.mpp-content-mobile').first();
    _.$wp_editor_content = _.$wrap.find('.mpp-content-wp-editor').first();
    _.$device_contents = _.$popup.find('.mpp-content-desktop, .mpp-content-mobile');
    _.$elements = _.$popup.find('.mpp-element');
    _.$overlay = _.$container.find('.mpp-overlay');
    _.$sticky = _.$container.find('.mpp-sticky');

    _.is_open = false;
    _.is_opening = false;
    _.is_closing = false;
    _.showPopup = true;
    _.closeInterval = undefined;
    _.metadata = {};

    _.defaults = {
      id: 0,
      position: 'middle-center',
      fullScreen: false,
      mobileDesign: false,
      ratioSmallDevices: 1,

      list: {
        service: '',
      },

      afterConversion: {
        message: '',
      },

      wpEditor: {
        enabled: false,
        autoHeight: false,
        padding: '20px 36px',
      },

      sound: {
        enabled: false,
        delay: -10,
        src: '',
      },

      preloader: {
        show: true,
        duration: 1000,
      },

      open: {
        event: 'click',
        delay: 0,
        duration: 800,
        animation: 'mpp-zoomIn',
        disablePageScroll: false,
        loadCounter: 0,
      },

      close: {
        delay: 0,
        duration: 700,
        animation: 'mpp-zoomOut',
      },

      overlay: {
        show: true,
        durationIn: 300,
        durationOut: 250,
      },

      notificationBar: {
        fixed: true,
        pushPageDown: true,
      },

      sticky: {
        enabled: false,
        initial: false,
        vertical: false,
      },

      inline: {
        shouldClose: false,
        disableTriggers: false
      },

      desktop: {
        device: 'desktop',
        browserWidth: 1000,
        browserHeight: 580,
        width: 800,
        widthUnit: 'px',
        height: 400,
        heightUnit: 'px',
      },

      mobile: {
        device: 'mobile',
        browserWidth: 600,
        browserHeight: 580,
        width: 500,
        widthUnit: 'px',
        height: 300,
        heightUnit: 'px',
        resizeOpeningKeyborad: true
      },

      callbacks: {
        beforeOpen: function ($, popup_instance, popup_id, options) {
        },
        afterOpen: function ($, popup_instance, popup_id, options) {
        },
        beforeClose: function ($, popup_instance, popup_id, options) {
        },
        afterClose: function ($, popup_instance, popup_id, options) {
        },
        onSubmit: function ($, popup_instance, popup_id, options, success) {
        },
        resize: function ($, popup_instance, popup_id, options) {
        },
      },

      triggers: {
        open: {
          onLoad: {
            enabled: false,
            delay: 1000,
          },
          onExit: {
            enabled: false,
          },
          onInactivity: {
            enabled: false,
            period: 60000,//1 minute
          },
          onScroll: {
            enabled: false,
            amount: '0%',
            afterPost: false,
            selector: '',
            displayed: false,
          },
        },
        close: {
          onClickOverlay: true,
          onEscKeydown: true,
          automatically: {
            enabled: false,
            delay: 10000,
          },
          onScroll: {
            enabled: false,
            amount: '10%',
          },
          onScrollUp: {
            enabled: false,
            amount: '10%',
          },
        }
      },
      cookies: {
        loadCounter: {
          name: ''
        },
        onLoad: {
          enabled: false,
        },
        onExit: {
          enabled: false,
        },
        onInactivity: {
          enabled: false,
        },
        onScroll: {
          enabled: false,
        },
        onConversion: {
          enabled: false,
        },
      },
      custom_cookies: {},
      contentLocker: {
        cookies: {
          unlockWithPassword: '',
          unlockWithForm: '',
          duration: 365,
        },//mpp_unlock
        enabled: false,
        type: '',//shortcode, page_content, whole_page
        unlock: '',//password, form
      },
    };

    if (_.has_popup()) {
      _.metadata = _.$popup.data('popup') || {};
    }
    _.options = $.extend(true, {}, _.defaults, options, _.metadata);
    _.options.id = _.options.id || _.$popup.data('popup-id');
    _.popup_id = _.options.id;
    _.options.open_delay = app.parse_number(_.options.open.delay) + _.duration_preloader_and_overlay();

    //Set data attribute
    _.set_options_to_data(_.options);

    //Create some elements
    _.init();

    //Register all events
    _.events();

    //Finally open popup
    _.open();

    return this;
  }

  MasterPopups.prototype = {
    has_popup: function () {
      var _ = this;
      return _.$popup.length > 0;
    },

    set_options_to_data: function (options) {
      var _ = this;
      if (_.has_popup()) {
        _.$popup.data('popup', options);
      }
    },

    init: function () {
      var _ = this;

      _.init_elements();
      _.build_link_powered_by();

      //Preloader
      if (_.has_overlay() && _.options.preloader.show) {
        _.build_preloader(_.$overlay);
      }
    },

    init_elements: function () {
      var _ = this;
      _.$elements.each(function (index, element) {
        var actions = $(this).data('actions');
        if (actions.onclick && actions.onclick.action != 'default') {
          $(element).css('cursor', 'pointer');
        }
        if ($(element).data('type') === 'countdown') {
          app.init_countdown($(element));
        }
        if ($(element).data('type') === 'field_recaptcha') {
          app.init_recaptcha($(element));
        }
      });
    },

    build_preloader: function ($target) {
      var _ = this;
      $target.append('<div class="mpp-preloader"></div>');
      if (_.is_support_css_property('animation')) {
        $target.find('.mpp-preloader').addClass('mpp-preloader-animation').html('<div class="mpp-preloader-spinner1"></div><div class="mpp-preloader-spinner2"></div>');
      } else {
        $target.find('.mpp-preloader').addClass('mpp-preloader-image');
      }
    },

    build_link_powered_by: function () {
      var _ = this;
      if (_.$popup.find('.cookieplus-wrap-link-powered-by').length) {
        _.$popup.find('.mpp-wrap-link-powered-by').remove();
      }
    },

    events: function () {
      var _ = this;
      _.$popup.on('mpp_changed_device', _.on_changed_device);
      _.on_keypress_elements();
      _.on_click_elements();
      _.close_popup_events();
      _.video_events();
      _.form_events();
      _.sticky_events();
      _.countdown_events();

      $(window).on("resize", function () {
        if (_.is_open) {
          _.set_dynamic_styles('onResize');
          _.call_function('resize', _.options.callbacks.resize);
        }
      });

      $(window).scroll(function () {
        if (_.is_open) {
          _.notification_bar_styles();
        }
      });

      //Working in popup. To avoid automatic closing
      _.$popup.find('.mpp-input, .mpp-select, .mpp-textarea').on('focus', function (event) {
        app.working = _.popup_id;
      });
      _.$popup.on('mouseenter', function (event) {
        app.working = _.popup_id;
      });
      $(document).on('click', function (event) {
        if ($(event.target).closest('.mpp-container').length === 0) {
          app.working = 0;
        }
      });
    },

    on_changed_device: function (event, _, current_device, old_device) {
      _.restore_video_poster_and_stop_videos(old_device);
    },

    sticky_events: function () {
      var _ = this;
      if (!_.options.sticky.enabled) {
        return;
      }
      _.$sticky.on('click', '.mpp-sticky-control', function (event) {
        _.options.open.event = 'click';
        _.open();
        _.$sticky.fadeOut(150);
      });
    },

    countdown_events: function () {
      var _ = this;
      if (typeof $.fn.MasterPopupsCountdown !== 'function') {
        return;
      }

      MasterPopupsCountdown.on('finish', function (countdownInstance, endDate) {
        setTimeout(function () {
          $popup = countdownInstance.$el.closest('.mpp-box');
          if (!$popup.length) {
            return;
          }
          if ($popup.find('.mpp-countdown-message').length) {
            $popup.find('.mpp-element-countdown').fadeOut(400, function () {
              $(this).addClass('mpp-hide');//Para evitar que por alguna animación aparezca otra vez
              $popup.find('.mpp-countdown-message').fadeIn();
            });
          }
        }, 2500);
      });
    },

    on_keypress_elements: function (event) {
      var _ = this;
      _.$popup.find('.mpp-element-content[tabindex]').on('keypress', function (e) {
        if (e.which === 13) {
          var type = $(this).closest('.mpp-element').data('type');
          if (['custom_field_input_checkbox', 'custom_field_input_checkbox_gdpr'].indexOf(type) > -1) {
            $(this).find('label').trigger('click');
          } else {
            $(this).trigger('click');
          }
        }
      });
    },

    on_click_elements: function () {
      var _ = this;
      _.$popup.on('click', '.mpp-element', function (event) {
        var actions = $(this).data('actions');
        if (actions.onclick) {
          switch (actions.onclick.action) {
            case 'close-popup':
              //Lo quité porque evita que redireccione a una URL en caso el botón tenga un link
              //event.preventDefault();
              _.close(app.last_open_event);
              break;

            case 'open-popup':
            case 'open-popup-and-not-close':
              event.preventDefault();
              if (actions.onclick.action == 'open-popup') {
                _.close(app.last_open_event);
              }
              var popup_id = actions.onclick.popup_id;
              if (MPP_POPUP_OPTIONS[popup_id]) {
                MPP_POPUP_OPTIONS[popup_id].open.event = 'click';
                app.open_popup_by_id(popup_id);
              }
              break;

            case 'redirect-to-url':
              //Lo quité porque evita que los checkboxs no funcionen
              //event.preventDefault();
              if (actions.onclick.url_close == 'on') {
                _.close(app.last_open_event);
              }
              if (actions.onclick.url && actions.onclick.url != '#' && actions.onclick.url != 'http://') {
                app.redirect(actions.onclick.url, actions.onclick.target, 500);
              }
              break;
          }

          if (actions.onclick.cookie_name) {
            app.set_custom_cookies_on_event(_.popup_id, 'click', actions.onclick.cookie_name);
          }
        }
      });
    },

    close_popup_events: function () {
      var _ = this;
      _.$container.on('click', '.mpp-element-close-icon, .mpp-close-popup', function (event) {
        //event.preventDefault();//ya no colocar para que siga funcionando los links(href) junto con la clase mpp-close-popup
        _.close();
      });

      if (_.has_overlay() && _.options.triggers.close.onClickOverlay) {
        _.$overlay.addClass('mpp-overlay-close-popup');
        _.$overlay.on('click', function (event) {
          _.close();
        });
      }
    },

    video_events: function () {
      var _ = this;
      var $elements = _.$popup.find('.mpp-element-video');
      $elements.on('click', '.mpp-video-poster .mpp-play-icon', function (event) {
        $(this).parent('.mpp-video-poster').css('display', 'none');
        var $wrap_video = $(this).closest('.mpp-element').find('.mpp-wrap-video');
        var $video;
        if ($wrap_video.data('video-type') == 'html5') {
          $video = $wrap_video.find('video').first();
          var player = videojs($video.attr('id'));
          player.play();
        } else {
          $video = $wrap_video.find('iframe').first();
          $video.attr('src', $video.data('src'));
        }
      });
    },

    form_events: function () {
      var _ = this;
      _.valid_characters_events();
      _.$popup.find('.mpp-element-field_submit').on('click', function (event) {
        _.$popup.removeClass('mpp-form-sent-ok');
        var $form = _.get_device_content($(this).data('device'));
        _.process_form($form);
      });

      _.$popup.find('.mpp-input, .mpp-select, .mpp-textarea').on('focus', function (event) {
        $(this).removeClass('mpp-error');
        $(this).closest('.mpp-element').removeClass('mpp-has-error').find('.mpp-error-warning').remove();
      });
      _.find_rechaptcha_wrap(_.$popup).on('mouseover', function (event) {
        $(this).removeClass('mpp-error');
        $(this).closest('.mpp-element').removeClass('mpp-has-error').find('.mpp-error-warning').remove();
      });
      _.$popup.find('.mpp-element-custom_field_input_checkbox label, .mpp-element-custom_field_input_checkbox_gdpr label').on('click touchstart', function (event) {
        $(this).find('.mpp-checkbox').removeClass('mpp-error');
        $(this).closest('.mpp-element').removeClass('mpp-has-error').find('.mpp-error-warning').remove();
      });
      _.$popup.on('click', '.mpp-back-to-form', function (event) {
        _.remove_processing_form();
      });

    },

    valid_characters_events: function () {
      var _ = this;
      var valid_elements = '' +
        '.mpp-element-field_first_name,' +
        '.mpp-element-field_last_name,' +
        '.mpp-element-custom_field_input_text,' +
        '.mpp-element-field_phone';
      _.$popup.find('.mpp-input').on('keydown', function (e) {
        var $input = $(this);
        var valid_characters = $input.data('valid-characters');
        if (!$input.closest(valid_elements).length || valid_characters == 'all' || app.is_control_keypress(e)) {
          return;
        }
        if (valid_characters == 'not-numbers' && app.is_number_keypress(e)) {
          e.preventDefault();
        } else if (valid_characters == 'only-numbers' && !app.is_number_keypress(e)) {
          e.preventDefault();
        } else if (valid_characters == 'numbers-and-plus' && !app.is_number_keypress(e, '.')) {
          e.preventDefault();
        } else if (valid_characters == 'numbers-and-dash' && !app.is_number_keypress(e, '-')) {
          e.preventDefault();
        }
      });
      _.$popup.find('.mpp-input').on('keyup', function (e) {
        var $input = $(this);
        var valid_characters = $input.data('valid-characters');
        if (!$input.closest(valid_elements).length || valid_characters == 'all') {
          return;
        }
        switch (valid_characters) {
          case 'not-numbers':
            this.value = this.value.replace(/[\d]+/, '');
            break;
          case 'only-numbers':
            this.value = this.value.replace(/[^\d]+/, '');
            break;
          case 'numbers-and-plus':
            this.value = this.value.replace(/[^\d.]+/, '');
            break;
          case 'numbers-and-dash':
            this.value = this.value.replace(/[^\d-]+/, '');
            break;
        }
      });
    },

    get_last_open_event: function (event) {
      return (event && typeof event == 'string' ) ? event : app.last_open_event;
    },

    show_popup_content: function () {
      var _ = this;
      _.$popup.find('.mpp-content').css('opacity', '1');
    },

    hide_popup_content: function () {
      var _ = this;
      _.$popup.find('.mpp-content').css('opacity', '0');
    },

    show_popup: function () {
      var _ = this;
      setTimeout(function () {
        _.$popup.fadeIn(120);
      }, 80);//Evita que se muestre antes de tiempo
    },

    hide_popup: function () {
      var _ = this;
      _.$popup.hide();
    },

    open: function (event) {
      var _ = this;

      event = event || _.options.open.event;
      app.last_open_event = event;

      if (_.is_open || _.is_opening) {
        return;
      }

      _.call_function('beforeOpen', _.options.callbacks.beforeOpen);

      if (_.options.sticky.enabled && _.options.sticky.initial && event != 'click') {
        _.open_sticky_control();
        return;
      }

      if (_.enqueue_this_popup(event) && app.enable_enqueue_popups == 'on') {
        return;
      }

      setTimeout(function () {
        _.before_open_popup(event);
        if (!_.showPopup) {
          return;
        }

        setTimeout(function () {
          _.hide_preloader();
          _.show_popup();

          if (_.already_converted() && app.show_popup_on_conversion(_.options)) {
            _.show_conversion_content();
            _.after_open_popup(event);
          } else {
            //Animate all elements
            _.animate_elements();

            _.$wrap.animateCSS_MasterPopup(_.options.open.animation, {
              infinite: true,
              infiniteClass: '',
              duration: app.parse_number(_.options.open.duration),
            });
            setTimeout(function () {
              _.after_open_popup(event);
            }, app.parse_number(_.options.open.duration) + 100);
          }
        }, _.duration_preloader_and_overlay(event));

      }, app.parse_number(_.options.open.delay));

      return false;
    },

    enqueue_this_popup: function (event) {
      var _ = this;
      if (event == 'onLoad' || event == 'onScroll') {
        if (_.exist_open_popups()) {
          var index = app.queue_popups.indexOf(_.options.id);
          if (index >= 0) {
            app.queue_popups.splice(index, 1);//Remove popup by index
          }
          app.queue_popups.push(_.options.id);//Add popup
          return true;
        }
      }
      //if (_.options.open.event == 'onLoad' || _.options.open.event == 'onScroll') {
      return false;
    },

    exist_open_popups: function () {
      var _ = this;
      if (!_.is_inline()) {
        return $('.mpp-popup').not('.mpp-inline').is('.mpp-is-opening, .mpp-is-open');
      }
      return false;
    },

    set_opening: function (status) {
      var _ = this;
      _.is_opening = status;
      if (status) {
        _.$popup.addClass('mpp-is-opening');
      } else {
        _.$popup.removeClass('mpp-is-opening');
      }
    },

    start_before_open_popup: function (event) {
      //para obtener la posición correcta del popup
      this.$popup.css('opacity', 0);
      this.$popup.show();
    },

    end_before_open_popup: function (event) {
      this.$popup.css('opacity', 1);
      this.$popup.hide();
    },

    before_open_popup: function (event) {
      var _ = this;
      //_.call_function('beforeOpen', _.options.callbacks.beforeOpen);
      if (!_.showPopup) {
        return;
      }

      _.set_opening(true);
      _.start_before_open_popup();
      _.set_initial_styles();
      _.set_dynamic_styles('onOpen');
      _.open_overlay(event);
      _.lazy_load_content();

      _.play_sound_effect();
      _.end_before_open_popup();
    },

    after_open_popup: function (event) {
      var _ = this;
      _.set_opening(false);
      _.is_open = true;
      _.$wrap.removeClass(_.options.open.animation + ' mpp-animated');
      _.$popup.addClass('mpp-is-open');
      _.play_autoplay_videos();
      _.show_hide_link_powered_by('show');
      _.close_automatically_delay(event);
      _.update_impressions();

      if (app.opened_popups.indexOf(_.popup_id) == -1) {
        app.opened_popups.push(_.popup_id);
      }

      app.set_custom_cookies_on_event(_.popup_id, 'afterOpen', null);
      _.call_function('afterOpen', _.options.callbacks.afterOpen);
    },

    open_overlay: function (event) {
      var _ = this;
      if (!_.has_overlay()) {
        return;
      }
      _.$overlay.fadeIn(_.overlay_duration_in(event));
      if (_.options.preloader.show) {
        _.$overlay.find('.mpp-preloader').fadeIn(200);
      }
    },

    overlay_duration_in: function (event) {
      var _ = this;
      var open_event = _.get_last_open_event(event);
      var duration = app.parse_number(_.options.overlay.durationIn);
      if (open_event == 'onExit') {
        duration = 50;
      }
      return duration;
    },

    hide_preloader: function () {
      var _ = this;
      if (_.has_overlay()) {
        _.$overlay.find('.mpp-preloader').fadeOut(250);
      }
    },

    show_hide_link_powered_by: function (action) {
      var _ = this;
      if (_.$popup.find('.mpp-wrap-link-powered-by').length) {
        if (action == 'show') {
          _.$popup.find('.mpp-wrap-link-powered-by').fadeIn(500);
        } else {
          _.$popup.find('.mpp-wrap-link-powered-by').fadeOut(100);
        }
      }
    },

    duration_preloader_and_overlay: function (event) {
      var _ = this;
      if (!_.has_overlay()) {
        return 0;
      }
      var preloader_duration = 0;
      if (_.options.preloader.show) {
        preloader_duration = app.parse_number(_.options.preloader.duration);
      }
      return preloader_duration + _.overlay_duration_in(event);
    },

    lazy_load_content: function (event) {
      var _ = this;
      _.load_iframe_url();
    },

    load_iframe_url: function (event) {
      var _ = this;
      var $elements = _.$popup.find('.mpp-element-iframe');
      $elements.each(function (index, el) {
        var iframe_url = $(el).find('.mpp-iframe-wrap').data('src');
        if (iframe_url) {
          $(el).find('.mpp-iframe-wrap > iframe').attr('src', iframe_url);
          setTimeout(function(){
            $(el).find('.mpp-iframe-wrap > .mpp-icon-spinner').hide();
          }, 4500);
        }
      });
    },

    play_autoplay_videos: function () {
      var _ = this;
      var $content = _.get_device_content();
      $content.find('.mpp-element-video').each(function (index, element) {
        if ($(element).find('.mpp-wrap-video').data('autoplay') == 'on') {
          $(element).find('.mpp-video-poster .mpp-play-icon').trigger('click');
        }
      });
    },

    play_sound_effect: function () {
      var _ = this;
      if (_.is_inline() || !_.options.sound.enabled || !_.options.sound.src) {
        return;
      }
      var $audio = _.$container.find('.mpp-sound-effect');
      var src = MPP_PUBLIC_JS.plugin_url + 'assets/audio/' + _.options.sound.src;
      if (!$audio.length) {
        _.$container.append('<audio class="mpp-sound-effect" preload="auto" muted="muted" style="display:none !important;"><source src="' + src + '" type="audio/mpeg"></source></audio>');
        $audio = _.$container.find('.mpp-sound-effect');
      }
      setTimeout(function () {
        $audio[0].volume = 0.5;
        $audio[0].muted = false;
        $audio[0].play();
      }, _.duration_preloader_and_overlay() + app.parse_number(_.options.sound.delay) + 200);
    },

    animations_to_not_hide: function () {
      return ['mpp-flash', 'mpp-pulse', 'mpp-rubberBand', 'mpp-shake', 'mpp-swing', 'mpp-tada', 'mpp-wobble'];
    },

    animate_elements: function () {
      var _ = this;
      _.$elements.each(function (index, element) {
        var animation = $(element).data('animation');
        if (animation.enable == 'on') {
          if (_.animations_to_not_hide().indexOf(animation.effect) < 0) {
            $(element).hide();
          }
          setTimeout(function () {
            $(element).show();
          }, app.parse_number(animation.delay) + 50);
          $(element).animateCSS_MasterPopup(animation.effect, {
            delay: app.parse_number(animation.delay),
            duration: app.parse_number(animation.duration),
          });
        }
      });
    },

    set_initial_styles: function () {
      var _ = this;
      //_.show_popup_content();
      _.update_z_index();
      _.disable_page_scroll(true);

      if (_.options.sticky.enabled) {
        _.$sticky.fadeOut(150);
      }
      if (_.options.wpEditor.enabled) {
        _.$popup.addClass('mpp-has-wp-editor');
        _.$wrap_content.css({
          'padding': _.options.wpEditor.padding,
        });
      }
    },

    set_dynamic_styles: function (event) {
      var _ = this;
      _.display_content_for_device();
      _.resize_popup(event);
      _.resize_elements(event);
      _.reposition_close_icon_wp_editor();
      _.notification_bar_styles();
    },

    notification_bar_styles: function () {
      var _ = this;
      if (_.is_notification_bar()) {
        if (!_.options.notificationBar.fixed) {
          _.$popup.css('position', 'absolute');
          if (this.options.position == 'bottom-bar') {
            _.fix_top_position_for_bottom_bar();
          }
        }

        setTimeout(function () {
          if (_.is_opening) {
            app.on('afterOpen', function () {
              if (!_.is_closing) {
                _.push_page_down_for_top_bar();
              }
            });
          } else {
            if (!_.is_closing) {
              _.push_page_down_for_top_bar();
            }
          }
        }, 100);//esperar por funcionalidad de header fixed del tema actual en scroll
      }
    },

    update_z_index: function () {
      var _ = this;
      if (_.is_inline()) {
        return;
      }
      _.$overlay.css('z-index', app.z_index.overlay);
      app.z_index.overlay++;
      _.$popup.css('z-index', app.z_index.popup);
      app.z_index.popup++;
      _.$sticky.css('z-index', app.z_index.sticky);
      app.z_index.sticky++;
    },

    display_content_for_device: function () {
      var _ = this;
      if (_.get_active_device() == 'mobile') {
        if (_.$mobile_content.css('display') == 'none') {
          _.$mobile_content.show();
          _.$desktop_content.hide();
          _.$popup.trigger('mpp_changed_device', [_, 'mobile', 'desktop']);
        }
      } else {
        if (_.$desktop_content.css('display') == 'none') {
          _.$desktop_content.show();
          _.$mobile_content.hide();
          _.$popup.trigger('mpp_changed_device', [_, 'desktop', 'mobile']);
        }
      }
    },

    get_ratio: function () {
      var _ = this;
      var op = _.get_device_options();
      var ws = _.window_size();
      var viewport_width = Math.max(280, ws.width - _.get_spacing() - _.get_side_spacing());
      var viewport_height = Math.max(280, ws.height - _.get_spacing());

      var ref_width = viewport_width / _.get_number_value(op.width + op.widthUnit, 'horizontal');
      var ref_height = viewport_height / _.get_number_value(op.height + op.heightUnit, 'vertical');

      var ws = _.window_size();

      var ratio = Math.min(ref_width, ref_height);
      if( ref_height < ref_width ){
        //Funcionalidad para evitar redimensionar el popup al abrir el teclado en dispositivos móviles
        if( !_.options.mobile.resizeOpeningKeyborad && _.options.position == 'middle-center' ){
          ratio = ref_width;
          _.$popup.addClass('not-resize-opening-keyboard');
          var popup_height = _.value_by_ratio(ratio, op.height);
          _.$popup.css('top', ws.scrollTop + (popup_height/2) - 40);
        }
      } else {
        _.$popup.removeClass('not-resize-opening-keyboard');
        if(_.options.position == 'middle-center' && !_.options.wpEditor.enabled ){
          _.$popup.css('top', '50%');
        }
      }

      if (_.is_notification_bar() || _.options.fullScreen) {
        ratio = ws.width / op.browserWidth;
      }

      if (!(_.is_notification_bar() && _.options.fullScreen)) {
        ratio = ratio > 1 ? 1 : ratio;
      }

      return ratio;
    },

    resize_popup: function (event) {
      var _ = this;
      var op = _.get_device_options();
      var ratio = _.get_ratio();

      if (_.options.fullScreen && !_.is_notification_bar()) {
        _.$popup.css({
          'width': '100%',
          'height': '100%',
        });
      } else {
        if (_.in_mobile_reference()) {
          ratio = ratio * parseFloat(_.options.ratioSmallDevices);
        }
        if (_.is_notification_bar()) {
          _.$popup.css('width', '100%');
        } else {
          _.$popup.css('width', _.value_by_ratio(ratio, op.width + op.widthUnit));
        }


        //Popup Height
        if (_.options.wpEditor.enabled) {
          if (_.options.wpEditor.autoHeight) {
            _.$popup.css('height', 'auto');
          } else {
            _.$popup.css('height', op.height + op.heightUnit);
          }
        } else {
          _.$popup.css('height', _.value_by_ratio(ratio, op.height + op.heightUnit));
        }
      }

      //For Overflow hidden feature
      if (_.is_notification_bar()) {
        _.$device_contents.css({
          'width': _.value_by_ratio(ratio, op.browserWidth + 'px'),
          'height': _.value_by_ratio(ratio, op.height + 'px'),
        });
      } else if (_.options.fullScreen) {
        _.$device_contents.css({
          'width': _.value_by_ratio(ratio, op.browserWidth + 'px'),
          'height': _.value_by_ratio(ratio, op.browserHeight + 'px'),
        });
      } else {
        _.$device_contents.css({
          'width': _.value_by_ratio(ratio, op.width + 'px'),
          'height': _.value_by_ratio(ratio, op.height + 'px'),
        });
      }
      //End overflow hidden feature

      if (_.options.wpEditor.enabled) {
        _.fix_content_overflow_wp_editor();
        _.resize_wp_editor();
      }
    },

    resize_elements: function (event) {
      var _ = this;
      var ratio = _.get_ratio();
      var ws = _.window_size();
      var $device_content = _.get_device_content();
      var content_size = _.get_sizes($device_content);
      _.$elements.each(function (index, element) {
        var type = $(element).data('type');
        var position = $(element).data('position');
        var size = $(element).data('size');
        var top = position.top;
        var left = position.left;

        $(element).css({
          'top': _.value_by_ratio(ratio, top),
          'left': _.value_by_ratio(ratio, left),
          'width': _.value_by_ratio(ratio, size.width),
          'height': _.value_by_ratio(ratio, size.height),
        });

        if (type == 'shortcode') {
          return;
        }

        var $content = $(element).find('.mpp-element-content');
        var font = $content.data('font');
        var padding = $content.data('padding');
        var border = $content.data('border');

        var $target = $content;
        if ($.inArray(type, _.form_elements()) > -1) {
          $target = $content.find('input');
          if (type == 'field_message') {
            $target = $content.find('textarea');
          } else if (type == 'custom_field_dropdown') {
            $target = $content.find('select');
          }
        }

        var styles = {
          'font-size': _.value_by_ratio(ratio, font['font-size']),
          'padding-top': _.value_by_ratio(ratio, padding.top),
          'padding-right': _.value_by_ratio(ratio, padding.right),
          'padding-bottom': _.value_by_ratio(ratio, padding.bottom),
          'padding-left': _.value_by_ratio(ratio, padding.left),
          'border-top-width': _.value_by_ratio(ratio, border['top-width']),
          'border-right-width': _.value_by_ratio(ratio, border['right-width']),
          'border-bottom-width': _.value_by_ratio(ratio, border['bottom-width']),
          'border-left-width': _.value_by_ratio(ratio, border['left-width']),
          'border-radius': _.value_by_ratio(ratio, border.radius),
        };
        $.each(styles, function (property, value) {
          $target._css(property, value, 'important');
        });

        if (type == 'custom_field_dropdown') {
          var n = app.number_data(font['font-size']);
          var font_size = (app.parse_number(n.value) * 0.8) + n.unit;
          $(element).find('.mpp-icon-dropdown').css({
            'font-size': _.value_by_ratio(ratio, font_size),
          });
        }

        if (type == 'video' || type == 'iframe') {
          if (size['full-screen'] == 'on') {
            $(element)._css('width', ws.width + 'px', 'important');
            $(element)._css('height', ws.height + 'px', 'important');
            $(element)._css('top', (ws.scrollTop - content_size.offset.top) + 'px', 'important');
            $(element)._css('left', (ws.scrollLeft - content_size.offset.left) + 'px', 'important');
          }
        }
        if (size['full-width'] == 'on') {
          $(element)._css('width', ws.width + 'px', 'important');
          $(element)._css('left', (ws.scrollLeft - content_size.offset.left) + 'px', 'important');
        }
        if (type == 'countdown') {
          var countdown = $(element).data('countdown-timer');
          $target = $(element).find('.mpp-countdown');
          $target._css('font-size', _.value_by_ratio(ratio, countdown['label-font-size']));
          $target.find('.mpp-count')._css('font-size', _.value_by_ratio(ratio, font['font-size']));
          $target.find('.mpp-count-digit')._css('width', _.value_by_ratio(ratio, countdown.width));
          $target.find('.mpp-count-digit')._css('height', _.value_by_ratio(ratio, countdown.height));
          $target.find('.mpp-count-digit .mpp-count.mpp-top')._css('line-height', _.value_by_ratio(ratio, countdown.height));
        }
        if (type == 'field_recaptcha') {
          $target = $(element).find('.mpp-recaptcha-wrap');
          $target._css('transform', 'scale('+_.value_by_ratio(ratio, 1)+')');
        }
      });

      //Close icon -> Force top right of the page
      if (_.$elements.filter('.mpp-on-top-right-page').length) {
        _.force_top_right_page_close_icon(ratio);
      }
    },

    fix_content_overflow_wp_editor: function () {
      var _ = this;
      if (_.options.wpEditor.enabled && _.options.wpEditor.autoHeight) {
        var ws = _.window_size();
        var $wp_editor = _.$wrap.find('.mpp-content-wp-editor');
        $wp_editor.css({ 'overflow': 'visible', 'height': 'auto' });//Para obtener la altura real del contenido
        //_.$wrap_content.css('height', 'auto');//Si se agrega directo desaparece el scroll en contenidos largos.
        if ($wp_editor.height() < ws.height) {
          _.$wrap_content.css('height', 'auto');//Esto evita el problema de overflow en firefox al redimensionar
        } else {
          _.$wrap_content.css('height', '100%');//este es valor original
        }
        $wp_editor.css({ 'overflow': 'auto', 'height': '100%' });//Regresamos a su valor original
      }
    },

    resize_wp_editor: function () {
      var _ = this;
      if (_.is_inline()) {
        return;
      }
      var ws = _.window_size();
      var ps = _.popup_size();
      var verticalSpacing = 30;//Espacio superior
      var middleCenterIsFixed = true;
      if (_.options.position == 'middle-center') {
        verticalSpacing = 40;
      }
      if (_.options.position != 'middle-center' || (_.options.position == 'middle-center' && middleCenterIsFixed)) {
        if (ps.height + 1 > ws.height) {
          _.$popup.css('height', ws.height - verticalSpacing);
          ps = _.popup_size();
        }
      }

      var offsetTop = Math.max(0, ((ws.height - ps.height) / 2));
      var offsetLeft = Math.max(0, ((ws.width - ps.width) / 2));

      switch (_.options.position) {
        case 'top-left':
        case 'top-center':
        case 'top-right':
        case 'top-bar':
          if (_.options.position == 'top-center') {
            _.$popup.css('left', offsetLeft + ws.scrollLeft);
          }
          break;

        case 'middle-center':
          if (middleCenterIsFixed) {
            _.$popup.css({
              'top': offsetTop,
              'left': offsetLeft + ws.scrollLeft,
            });
          } else {
            if (ps.height + 1 > ws.height) {
              offsetTop += 30;
            }
            _.$popup.css({
              'position': 'absolute',
              'top': offsetTop + ws.scrollTop,
              'left': offsetLeft + ws.scrollLeft,
            });
          }
          break;

        case 'middle-left':
        case 'middle-right':
          _.$popup.css('top', offsetTop);
          break;

        case 'bottom-left':
        case 'bottom-center':
        case 'bottom-right':
        case 'bottom-bar':
          if (_.options.position == 'bottom-center') {
            _.$popup.css('left', offsetLeft + ws.scrollLeft);
          }
          break;
      }
    },

    reposition_close_icon_wp_editor: function () {//for wp-editor
      var _ = this;
      var $close_icon = _.$popup.find('.mpp-close-icon');
      if (!$close_icon.length) {
        return;
      }
      var ps = _.popup_size();
      $close_icon.css({
        'left': ps.width - $close_icon.width() - 10,
        'top': 10,
      });
    },

    force_top_right_page_close_icon: function (ratio) {
      var _ = this;
      var ws = _.window_size();
      var ps = _.popup_size();
      var offset = parseInt(30 * ratio);
      var $close_icon = _.$elements.filter('.mpp-on-top-right-page');
      var icon_width = $close_icon.width();
      var top = -ps.position.top + offset;//ws.scrollTop - _.$popup.offset().top + offset;
      var left = ws.width - _.$popup.offset().left - offset - icon_width;
      _.$elements.filter('.mpp-on-top-right-page').css({
        'top': top,
        'left': left,
      });
      if (_.options.fullScreen || _.is_notification_bar()) {
        var $device_content = _.get_device_content();
        var right = -((ws.width - $device_content.width()) / 2) + offset;
        top = ps.height >= 80 ? offset : parseInt(18 * ratio);
        _.$elements.filter('.mpp-on-top-right-page').css({
          'top': top,
          'left': 'auto',
          'right': right,
        });
        if (_.options.fullScreen) {
          top = -((ws.height - $device_content.height()) / 2) + offset;
          _.$elements.filter('.mpp-on-top-right-page').css('top', top);
        }
      }
    },

    fix_top_position_for_bottom_bar: function () {
      var _ = this;
      var ws = _.window_size();
      _.$popup.css('top', ws.documentHeight);
      _.$popup.css('bottom', 'auto');
      var height_updated = false;
      $(window).scroll(function () {
        if (!height_updated && app.in_scroll_top('5%')) {
          height_updated = true;
          _.$popup.hide();
          ws = _.window_size();
          _.$popup.fadeIn(400);
          _.$popup.css('top', ws.documentHeight);
        }
      });
    },

    push_page_down_for_top_bar: function () {
      var _ = this;
      if (!_.is_open || !_.options.notificationBar.pushPageDown || _.options.position == 'bottom-bar') return;

      var $fixed_header = $(_.options.notificationBar.fixedHeaderSelector);
      var $container_page = $(_.options.notificationBar.containerPageSelector);
      $container_page = $container_page.length ? $container_page : _.$body;
      var popup_height = _.$popup.outerHeight();

      if ($('#wpadminbar').length) {
        _.$popup.css('margin-top', $('#wpadminbar').outerHeight());
      }

      var header_margin_top = 0;
      var container_margin_top = 0;
      if ($fixed_header.length) {
        if ($fixed_header.data('margin-top') === undefined) {
          $fixed_header.attr('data-margin-top', $fixed_header.css('margin-top'));
        }
        if ($fixed_header.css('position') == 'fixed' && _.options.notificationBar.fixed) {
          header_margin_top = popup_height;
        } else {
          header_margin_top = $fixed_header.data('margin-top');
        }
        $fixed_header.css({ 'margin-top': header_margin_top });
      }

      if ($container_page.length) {
        if ($container_page.data('margin-top') === undefined) {
          $container_page.attr('data-margin-top', $container_page.css('margin-top'));
        }
        container_margin_top = popup_height;
        if ($fixed_header.length && _.options.notificationBar.fixed) {
          if ($fixed_header.css('position') == 'fixed') {
            container_margin_top += $fixed_header.outerHeight();
          } else {
            container_margin_top += parseInt($container_page.data('margin-top'));
          }
        }
        $container_page.css({ 'margin-top': container_margin_top });
      }
    },

    restore_page_down_for_top_bar: function () {
      var _ = this;

      if (!_.options.notificationBar.pushPageDown || _.options.position == 'bottom-bar') return;

      var $fixed_header = $(_.options.notificationBar.fixedHeaderSelector);
      var $container_page = $(_.options.notificationBar.containerPageSelector);
      $container_page = $container_page.length ? $container_page : _.$body;
      var header_margin_top = '0px';
      var container_margin_top = '0px';
      var duration = 750;
      if ($fixed_header.length) {
        header_margin_top = $fixed_header.data('margin-top') !== undefined ? $fixed_header.data('margin-top') : 0;
        $fixed_header.stop().animate({ 'margin-top': header_margin_top }, duration, function () {
        });
      }
      if ($container_page.length) {
        container_margin_top = $container_page.data('margin-top') !== undefined ? $container_page.data('margin-top') : 0;
        $container_page.stop().animate({ 'margin-top': container_margin_top }, duration, function () {
        });
      }
    },

    close_automatically_delay: function (event) {
      var _ = this;
      if (_.options.triggers.close.automatically.enabled) {
        _.closeInterval = setInterval(function () {
          if (app.working != _.popup_id && _.is_open) {
            _.close(event);
          }
        }, app.parse_number(_.options.triggers.close.automatically.delay));
      }
    },

    close: function (event, show_sticky) {
      var _ = this;
      if (!app.is_content_locker_enabled(_.options)) {
        if (_.is_inline() && !_.options.inline.shouldClose) {
          return;
        }
      }

      show_sticky = show_sticky !== undefined ? show_sticky : true;

      if (!_.is_open || _.is_closing) {
        return;
      }
      _.is_closing = true;
      _.before_close_popup(show_sticky);

      //Animate close
      _.$wrap.animateCSS_MasterPopup(_.options.close.animation, {
        infinite: true,
        infiniteClass: '',
        duration: _.options.close.duration,
      });

      setTimeout(function () {
        _.after_close_popup(event, show_sticky);
      }, app.parse_number(_.options.close.duration));
    },

    before_close_popup: function (show_sticky) {
      var _ = this;
      if (_.is_notification_bar()) {
        _.restore_page_down_for_top_bar();
      }
      _.show_hide_link_powered_by('hide');
      _.disable_page_scroll(false);
      _.call_function('beforeClose', _.options.callbacks.beforeClose);
    },

    after_close_popup: function (event, show_sticky) {
      var _ = this;
      _.is_closing = false;
      _.is_open = false;
      clearInterval(_.closeInterval);
      _.close_overlay();
      _.hide_popup();
      _.$wrap.removeClass(_.options.close.animation + ' mpp-animated');
      _.$popup.removeClass('mpp-is-open');
      _.$popup.find('.mpp-error-warning').remove();
      _.restore_video_poster_and_stop_videos();
      _.restore_iframe_url();
      _.remove_processing_form();

      //Set cookies
      _.set_cookies_after_close( event );

      if (app.enable_enqueue_popups == 'on') {
        _.open_enqueue_popups();
      }

      _.call_function('afterClose', _.options.callbacks.afterClose);

      if (_.options.sticky.enabled && show_sticky) {
        _.open_sticky_control();
      }
    },

    close_overlay: function () {
      var _ = this;
      if (!_.has_overlay()) {
        return;
      }
      _.$overlay.fadeOut(app.parse_number(_.options.overlay.durationOut));
    },

    restore_iframe_url: function (event) {
      var _ = this;
      var $elements = _.$popup.find('.mpp-element-iframe');
      $elements.each(function (index, el) {
        var $iframe = $(el).find('.mpp-iframe-wrap > iframe');
        $iframe.attr('src', 'about:blank');
        $(el).find('.mpp-iframe-wrap > .mpp-icon-spinner').show();
      });
    },

    restore_video_poster_and_stop_videos: function (device) {
      var _ = this;
      var $elements = _.$popup.find('.mpp-element-video');
      if (device) {
        $elements = _.get_device_content(device).find('.mpp-element-video');
      }
      $elements.each(function (index, element) {
        $(element).find('.mpp-video-poster').css('display', 'block');
        var $wrap_video = $(element).find('.mpp-wrap-video');
        var $video;
        if ($wrap_video.data('video-type') == 'html5') {
          $video = $wrap_video.find('video').first();
          var player = videojs($video.attr('id'));
          player.pause();
          player.currentTime(0);
        } else {
          $video = $wrap_video.find('iframe').first();
          $video.attr('src', 'about:blank');
        }
      });
    },

    disable_page_scroll: function (disable) {
      var _ = this;
      if (!_.is_inline() && _.options.open.disablePageScroll) {
        $('html').toggleClass('mpp-disable-page-scroll', disable);
      }
    },

    open_sticky_control: function () {
      var _ = this;
      if (!_.options.sticky.enabled || app.has_cookie_not_show_popup(_.popup_id)) {
        return;
      }

      var animate_class = 'mpp-slideInUp';
      switch (_.options.position) {
        case 'top-left':
        case 'top-center':
        case 'top-right':
        case 'top-bar':
          animate_class = 'mpp-slideInDown';
          break;

        case 'middle-left':
          animate_class = 'mpp-slideInLeft';
          if (_.options.sticky.vertical) {
            animate_class = 'mpp-slideInDown';
          }
          break;

        case 'middle-right':
          animate_class = 'mpp-slideInRight';
          if (_.options.sticky.vertical) {
            animate_class = 'mpp-slideInUp';
          }
          break;

        case 'bottom-left':
        case 'bottom-center':
        case 'bottom-right':
        case 'bottom-bar':
          animate_class = 'mpp-slideInUp';
          break;
      }
      _.$sticky.fadeIn(300).find('.mpp-sticky-control').animateCSS_MasterPopup(animate_class, {
        infinite: false,
        infiniteClass: '',
        duration: 1000,
      });
      _.$sticky.fadeIn(300).css({ 'z-index': app.z_index.sticky });
    },

    set_cookies_after_close: function (event) {
      var _ = this;
      var cookie = _.options.cookies[_.get_last_open_event(event)];
      if (cookie && cookie.enabled) {
        app.cookie.set(cookie.name, true, cookie.duration == 'days' ? cookie.days : 0);
      }
      app.set_custom_cookies_on_event(_.popup_id, 'afterClose', null);
    },

    already_converted: function () {
      var _ = this;
      return !!app.get_cookie_event('onConversion', _.options);
    },

    open_enqueue_popups: function () {
      if (app.queue_popups.length > 0) {
        app.open_popup_by_id(app.queue_popups[0]);
        app.queue_popups.shift();//Delete first
      }
    },

    update_impressions: function (restore) {
      var _ = this;
      restore = restore || false;
      if (MPP_PUBLIC_JS.is_admin) {
        return;
      }
      var data = {};
      data.action = 'mpp_update_impressions';
      data.popup_id = _.options.id;
      data.restore = restore;
      setTimeout(function () {
        _.ajax({
          data: data,
          success: function (response) {
          },
        }, 'update_impressions', true);
      }, 7000);
    },

    update_submits: function () {
      var _ = this;
      var data = {};
      data.action = 'mpp_update_submits';
      data.popup_id = _.options.id;
      _.ajax({
        data: data,
        success: function (response) {
        },
      }, 'update_submits', true);
    },

    build_conversion_content: function () {
      var _ = this;
      var html =
        '<div class="mpp-conversion">' +
        '<div class="mpp-conversion-content">' +
        _.options.afterConversion.message +
        '</div>' +
        '<div class="mpp-conversion-footer">' +
        '<span class="mpp-close-popup">' + MPP_PUBLIC_JS.strings.close_popup + '</span>' +
        '</div>' +
        '</div>';
      if (!_.$wrap.find('.mpp-conversion').length) {
        _.$wrap.append(html);
      }
      return _.$wrap.find('.mpp-conversion');
    },

    show_conversion_content: function () {
      var _ = this;
      var $conversion_content = _.build_conversion_content();
      _.hide_popup_content();
      if (_.is_inline() && !_.options.inline.shouldClose) {
        $conversion_content.find('.mpp-close-popup').remove();
      }
      $conversion_content.fadeIn(400);
    },

    process_form: function ($form) {
      var _ = this;
      var $processing_form = _.build_processing_form();
      var $content = $processing_form.find('.mpp-processing-form-content');
      var fake_delay = 1800;

      $processing_form.fadeIn(200, function (e) {
        _.hide_popup_content();
        //Form Type
        if (_.$popup.data('form-type') == 'none' && !app.is_content_locker_enabled_with_password(_.options)) {
          setTimeout(function () {
            $content.html('Please define the "Form Type". Go your popup options and in "Form Type" choose Subscription Form or Contact Form.');
            _.remove_preloader_processing_form();
          }, fake_delay);
        }
        //Validate form
        else if (!_.validate_form($form)) {
          setTimeout(function () {
            _.remove_processing_form();
          }, fake_delay);
        } else {
          if (_.has_recaptcha('invisible') || _.has_recaptcha('v3')) {
            if (_.has_recaptcha('invisible')) {
              //Ejecuta el recaptcha invisible y luego ejecuta una función que dentro llama a _.submit_form($form);
              //Ver app.init_recaptcha();
              app.google_recaptcha.execute(_.get_recaptcha_index($form));
            } else {
              //Recaptcha version 3, necesario setear el token para obtenerlo antes de enviar el formulario
              app.google_recaptcha.readyExcecute(function (token) {
                _.set_recaptcha_token($form, token);
                _.submit_form($form);
              });
            }
          } else {
            _.submit_form($form);
          }
        }
      });
    },

    validate_form: function ($form) {
      var _ = this;
      var value = '';
      var is_valid_value = true;
      var is_valid_form = true;
      var $target, value, type, message;
      var minlength = 1;
      var message = '';

      $form.find('.mpp-form-element').each(function (index, el) {
        is_valid_value = true;
        type = $(el).data('type');
        if ($(el).data('required') == 'off' && type != 'field_recaptcha') {
          return true;
        }
        value = '';
        var validation_message = $(el).data('validation-message');
        message = _.get_validation_message_by_element($(el));


        if (type == 'custom_field_input_checkbox' || type == 'custom_field_input_checkbox_gdpr') {
          $target = $(el).find('input.mpp-checkbox');
        } else if (type == 'custom_field_dropdown') {
          $target = $(el).find('select.mpp-select');
        } else if (type == 'field_message') {
          $target = $(el).find('textarea.mpp-textarea');
        } else if (type == 'field_recaptcha') {
          $target = _.find_rechaptcha_wrap($(el));
        } else {
          $target = $(el).find('input.mpp-input');
        }
        value = $target.val();

        //Remove all errors
        $(el).removeClass('mpp-has-error').find('.mpp-error-warning').remove();
        $target.removeClass('mpp-error');


        //validate
        if (type == 'custom_field_input_checkbox' || type == 'custom_field_input_checkbox_gdpr') {
          if (!$target.is(':checked')) {
            is_valid_value = false;
            is_valid_form = false;
            message = validation_message || MPP_PUBLIC_JS.strings.validation.checkbox;
          }
        } else if (type == 'field_email') {
          if (!_.validator.is_email(value)) {
            is_valid_value = false;
            is_valid_form = false;
            message = validation_message || MPP_PUBLIC_JS.strings.validation.email;
          }
        } else if (type == 'custom_field_dropdown') {
          if (!_.validator.min_length(value, 1)) {
            is_valid_value = false;
            is_valid_form = false;
            message = validation_message || MPP_PUBLIC_JS.strings.validation.dropdown;
          }
        } else if (type == 'field_recaptcha') {
          //Solo validar la version 2 con checkbox del recaptcha
          if (_.has_recaptcha('v2')) {
            if (!_.get_recaptcha_token($target)) {
              is_valid_value = false;
              is_valid_form = false;
            }
          }
        } else {
          var $input = $(el).find('.mpp-input');
          if ($input.length && $input.data('min-characters')) {
            minlength = parseInt($input.data('min-characters'));
          }
          minlength = minlength >= 1 ? minlength : 1;
          if (!_.validator.min_length(value, minlength)) {
            is_valid_value = false;
            is_valid_form = false;
            var minstring = MPP_PUBLIC_JS.strings.validation.min_length;
            message = message + ' (' + minstring + ' ' + minlength + ')';
            message = validation_message || message;
          }
        }

        //Regex validation
        var regex = $(el).data('regex-validation');
        if (regex && !app.is_content_locker_enabled_with_password(_.options)) {
          try {
            pattern = app.regex_parser(regex);
            is_valid_value = pattern.test(value);
            if (!is_valid_value) {
              is_valid_form = false;
              message = validation_message || message;
            }
          } catch (e) {
            //isValidRegex = false;
          }
        }

        if (!is_valid_value) {
          $target.addClass('mpp-error');
          $(el).addClass('mpp-has-error').append('<span class="mpp-error-warning " title="' + message + '"></span>');
          setTimeout(function () {
            $(el).find('.mpp-error-warning').addClass('mpp-animated mpp-animated-2x mpp-flash');
          }, 2000);
        }
      });
      return is_valid_form;
    },

    submit_form: function ($form) {
      var _ = this;
      var data = _.get_form_data($form.data('device'));

      _.ajax({
        data: data,
        beforeSend: function () {
        },
        success: function (response) {
          if (!response) {
            return;
          }

          if (response.error) {
            _.show_message_after_submit(response.message);
          } else {
            if (response.success) {
              var cookie = _.options.cookies.onConversion;
              if (cookie.enabled) {
                app.cookie.set(cookie.name, true, cookie.duration == 'days' ? cookie.days : 0);
              }
              app.set_custom_cookies_on_event(_.popup_id, 'onConversion', null);
              app.unlock_content_locker_and_set_cookies(_.options);

              if (response.actions.close_popup) {
                setTimeout(function () {
                  _.close('', false);
                  app.open_popup_by_id(response.actions.open_popup_id);
                }, app.parse_number(response.actions.close_popup_delay));
              }
              if (response.actions.download_file) {
                setTimeout(function () {
                  //download(response.actions.file);//Dejó de funcionar
                  app.downloadURI(response.actions.file, "");
                }, 1000);
              }
              if (response.actions.redirect) {
                setTimeout(function () {
                  if (_.$body.hasClass('wp-admin')) {
                    alert('MasterPopups say: Redirection is disabled in Admin');
                  } else {
                    app.redirect(response.actions.redirect_to, response.actions.redirect_target, 1000);
                  }
                }, 1500);
              }
              if (response.actions.advanced_redirection) {
                setTimeout(function () {
                  if (_.$body.hasClass('wp-admin')) {
                    alert('MasterPopups say: Redirection is disabled in Admin');
                  } else {
                    app.redirect(response.actions.advanced_redirection, response.actions.redirect_target, 1000);
                  }
                }, 1500);
              }
              _.update_submits();
              _.$popup.addClass('mpp-form-sent-ok');
              _.show_message_after_submit(response.actions.message);
              _.call_function('submit', _.options.callbacks.onSubmit, true);
            } else {
              var message = response.actions.message;
              if (response.actions.error) {
                message += '<div style="padding-top: 8px;"><strong>ERROR: </strong><em>' + response.actions.error + '</em></div>';
              }
              _.show_message_after_submit(message);
              _.call_function('submit', _.options.callbacks.onSubmit, false);
            }
          }
        },
        complete: function (jqXHR, textStatus) {
          _.reset_recaptcha($form);
        },
      }, 'process_form');
    },

    get_form_data: function (device) {
      var _ = this;
      var device = device || _.get_active_device();
      var $form = _.get_device_content(device);
      var $form_elements = $form.find('.mpp-form-element:not(.mpp-element-field_submit)');

      var data = $form_elements.find('input[name],select[name],textarea[name]').serializeMyObject();
      data.action = 'mpp_process_ajax_form';
      data.sub_action = 'mpp_' + _.$popup.data('form-type');
      data.popup_id = _.options.id;
      data.current_device = $form_elements.eq(0).data('device');
      data.popup_elements = [];
      $form_elements.each(function (index, el) {
        data.popup_elements.push($(el).data('index'));
      });

      if (app.is_content_locker_enabled(_.options)) {
        data.contentLocker = true;
        if (app.is_content_locker_enabled_with_password(_.options)) {
          data.sub_action = 'mpp_check_password_content_locker';
          var $input_password = $form_elements.find('input[type="password"]');
          data.password = $input_password.val();
          data.validation_message = _.get_validation_message_by_element($input_password.closest('.mpp-element'));
        }
      }

      if (_.has_recaptcha()) {
        data.recaptcha_token = _.get_recaptcha_token($form);
        data.recaptcha_version = _.get_recaptcha_version($form);
      }

      return data;
    },

    show_message_after_submit: function (message) {
      var _ = this;
      _.remove_preloader_processing_form();
      var $processing_form = _.$wrap.find('.mpp-processing-form');
      var $content = $processing_form.find('.mpp-processing-form-content');
      $content.html(message);
    },

    build_processing_form: function () {
      var _ = this;
      var html =
        '<div class="mpp-processing-form">' +
        '<div class="mpp-processing-form-content">' +
        '</div>' +
        '<div class="mpp-processing-form-footer">' + _.get_buttons_processing_form() + '</div>' +
        '</div>';
      _.$wrap.append(html);
      var $processing_form = _.$wrap.find('.mpp-processing-form');
      _.build_preloader($processing_form);
      if (_.is_inline() && !_.options.inline.shouldClose) {
        $processing_form.find('.mpp-close-popup').remove();
      }
      _.$wrap.find('.mpp-preloader').fadeIn(200);
      return $processing_form;
    },

    get_buttons_processing_form: function () {
      var _ = this;
      var buttons = '<span class="mpp-back-to-form">' + MPP_PUBLIC_JS.strings.back_to_form + '</span>';
      if (!app.is_content_locker_enabled(_.options)) {
        buttons += '<span class="mpp-close-popup">' + MPP_PUBLIC_JS.strings.close_popup + '</span>';
      }
      return buttons;
    },

    remove_processing_form: function () {
      var _ = this;
      _.show_popup_content();
      _.$wrap.find('.mpp-processing-form').fadeOut(300, function (e) {
        $(this).remove();
      });
    },

    remove_preloader_processing_form: function () {
      var _ = this;
      var $processing_form = _.$wrap.find('.mpp-processing-form');
      $processing_form.find('.mpp-preloader').remove();
      $processing_form.find('.mpp-processing-form-footer').fadeIn(200);
    },

    get_validation_message_by_element: function ($element) {
      var validation_message = $element.data('validation-message');
      var message = validation_message || MPP_PUBLIC_JS.strings.validation.general;
      return message;
    },


    call_function: function (event, callback, extra_arg) {
      var _ = this;
      if ($.isFunction(callback)) {
        callback.call(_, jQuery, _, _.options.id, _.options, extra_arg);
      }
      app.call_events(event, jQuery, _, _.options.id, _.options, extra_arg);
    },

    viewport: function () {
      var e = window, a = 'inner';
      if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
      }
      return { width: e[a + 'Width'], height: e[a + 'Height'] };
    },

    window_size: function () {
      var _ = this;
      var size = {
        height: $(window).height(),
        documentWidth: $(document).width(),
        documentHeight: $(document).height(),
        scrollTop: $(window).scrollTop(),
        scrollLeft: $(window).scrollLeft(),
        viewport: {
          width: _.viewport().width,
          height: _.viewport().height,
        },
      };
      if (_.is_inline()) {
        size.width = _.$container.parent().innerWidth();
      } else {
        size.width = $(window).width();
      }
      return size;
    },

    popup_size: function () {
      var _ = this;
      return _.get_sizes(_.$popup);
    },

    get_sizes: function ($target) {
      return !$target.length ? {} : {
        width: $target.width(),
        height: $target.height(),
        innerWidth: $target.innerWidth(),
        innerHeight: $target.innerHeight(),
        outerWidth: $target.outerWidth(true),
        outerHeight: $target.outerHeight(true),
        position: {
          top: $target.position().top,
          left: $target.position().left
        },
        offset: {
          left: $target.offset().left,
          top: $target.offset().top,
        }
      };
    },

    get_device_options: function () {
      var _ = this;
      return _.get_active_device() == 'mobile' ? _.options.mobile : _.options.desktop;
    },

    get_active_device: function () {
      var _ = this;
      if (_.options.mobileDesign && _.in_mobile_reference()) {
        return 'mobile';
      }
      return 'desktop';
    },

    get_device_content: function (device) {
      var _ = this;
      device = device || _.get_active_device();
      return device == 'desktop' ? _.$desktop_content : _.$mobile_content;
    },

    in_mobile_reference: function () {
      return this.window_size().width <= app.parse_number(this.options.mobile.browserWidth);
    },

    is_notification_bar: function () {
      return this.options.position == 'top-bar' || this.options.position == 'bottom-bar';
    },

    is_inline: function () {
      return this.$popup.hasClass('mpp-inline');
    },

    get_spacing: function () {
      var _ = this;
      var op = _.get_device_options();
      if (_.is_inline() || (op.width == 100 && op.widthUnit == '%')) {
        return 0;
      }
      if (_.window_size().width <= _.options.mobile.browserWidth) {
        return 10;
      }
      return 20;
    },

    get_side_spacing: function () {
      var _ = this;
      if (_.is_inline()) {
        return 0;
      }
      if (_.options.position.indexOf('left') > -1 || _.options.position.indexOf('right') > -1) {
        return (12 / 100) * _.window_size().width;
      }
      return 0;
    },

    is_support_css_property: function (propertyName) {
      var elm = document.createElement('div');
      propertyName = propertyName.toLowerCase();
      if (elm.style[propertyName] !== undefined) {
        return true;
      }
      var propertyNameCapital = propertyName.charAt(0).toUpperCase() + propertyName.substr(1),
        domPrefixes = 'Webkit Moz ms O'.split(' ');

      for (var i = 0; i < domPrefixes.length; i++) {
        if (elm.style[domPrefixes[i] + propertyNameCapital] !== undefined) {
          return true;
        }
      }
      return false;
    },

    form_elements: function () {
      return ['field_first_name', 'field_last_name', 'field_email', 'field_phone', 'field_message', 'custom_field_input_text', 'custom_field_dropdown'];
    },

    has_overlay: function () {
      var _ = this;
      return _.options.overlay.show && _.$overlay.length == 1;
    },

    find_rechaptcha_wrap: function ($target) {
      if ($target) {
        var $recaptcha_wrap = $target;
        if (!$target.hasClass('mpp-recaptcha-wrap')) {
          $recaptcha_wrap = $target.find('.mpp-recaptcha-wrap').first();
        }
        return $recaptcha_wrap;
      }
      return this.get_device_content().find('.mpp-recaptcha-wrap').first();
    },

    get_recaptcha_token: function ($target) {
      return this.find_rechaptcha_wrap($target).find('[name="g-recaptcha-response"]').val();
    },

    set_recaptcha_token: function ($target, token) {
      //la versión 3 de recaptcha no actualiza el token por lo tanto debemos hacerlo manualmente
      this.find_rechaptcha_wrap($target).find('[name="g-recaptcha-response"]').val(token);
    },

    get_recaptcha_version: function ($target) {
      return this.find_rechaptcha_wrap($target).data('version');
    },

    get_recaptcha_index: function ($target) {
      return this.find_rechaptcha_wrap($target).data('recaptcha-index');
    },

    has_recaptcha: function (version) {
      var _ = this;
      var $recaptcha_wrap = _.find_rechaptcha_wrap();
      if ($recaptcha_wrap.length) {
        if (version && $recaptcha_wrap.data('version') != version) {
          return false;
        }
        return true;
      }
      return false;
    },

    reset_recaptcha: function($target){
      var _ = this;
      //Invisible y Version 2 tienen el método reset
      if (_.has_recaptcha('invisible') || _.has_recaptcha('v2')) {
        //Resetea el recapctha y limpia el textarea name=g-recaptcha-response
        app.google_recaptcha.reset(_.get_recaptcha_index($target));
      } else if( _.has_recaptcha('v3') ){
        $target.find('[name="g-recaptcha-response"]').val("");
      }
    },

    get_number_value: function (value, orientation) {
      var _ = this;
      var ws = _.window_size();
      var n = 1;
      orientation = orientation || 'horizontal';
      if (_.is_numeric(value)) {
        var object = app.number_data(value);
        n = object.value;
        if (object.unit == '%') {
          if (orientation == 'horizontal') {
            n = (object.value / 100) * ws.width;
          } else if (orientation == 'vertical') {
            n = (object.value / 100) * ws.height;
          }
        }
      }
      return app.parse_number(n);
    },

    value_by_ratio: function (ratio, value) {
      var _ = this;
      if (_.is_auto(value)) {
        return value;
      }
      if (_.is_numeric(value)) {
        var object = app.number_data(value);
        if( object.unit ){
          return (ratio * parseFloat(object.value)) + object.unit;
        }
        return (ratio * parseFloat(object.value));
      }
      return '';
    },

    is_number: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    is_numeric: function (n) {
      return !isNaN(parseInt(n));
    },

    is_auto: function (value) {
      return $.inArray(value, ['auto', 'initial', 'inherit', 'normal']) > -1;
    },

    number_full_width: function () {
      var _ = this;
      return _.get_number_value('100%', 'horizontal');
    },

    number_full_height: function () {
      var _ = this;
      return _.get_number_value('100%', 'vertical');
    },

    validator: {
      is_email: function (email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
      },
      min_length: function (value, length) {
        return $.trim(value).length >= length;
      }
    },

    css: {
      number: function (value, unit) {
        var _ = this;
        unit = unit || '';
        var arr = ['auto', 'initial', 'inherit', 'normal'];
        if ($.inArray(value, arr) > -1) {
          return value;
        }
        value = value.toString().replace(/[^0-9.\-]/g, '');
        if (_.is_number(value)) {
          return value + unit;
        }
        return 1;
      },
      is_number: function (n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      },
    },

    ajax: function (options, event, hide_log) {
      var _ = this;
      var defaults = {
        type: 'post',
        data: {
          ajax_nonce: MPP_PUBLIC_JS.ajax_nonce,
        },
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (response) {
        },
        complete: function (jqXHR, textStatus) {
        },
      };
      options = $.extend(true, {}, defaults, options);

      if (!hide_log) {
        //Debug
        clog('==================== AJAX PROCESS ====================');
        clog('Popup ID: ' + _.popup_id + ', options.data:');
        clog(options.data);
      }

      $.ajax({
        url: MPP_PUBLIC_JS.ajax_url,
        type: options.type,
        dataType: options.dataType,
        data: options.data,
        beforeSend: options.beforeSend,
        success: function (response) {
          if (!hide_log) {
            clog('====== AJAX Event: ' + event + ' ========');
            clog('Popup ID: ' + _.popup_id + ', ajax success, response:');
            clog(response);
          }
          if ($.isFunction(options.success)) {
            options.success.call(this, response);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          clog('ajax error, jqXHR');
          clog(jqXHR);
          clog('ajax error, errorThrown');
          clog(errorThrown);
        },
        complete: function (jqXHR, textStatus) {
          if ($.isFunction(options.complete)) {
            options.complete.call(this, jqXHR, textStatus);
          }
        }
      });
    },

    queryStringToJson: function (url) {
      if (url === '') return '';
      url = url || location.search;
      if (url.indexOf('?') === 0) {
        url = url.slice(1);
      }
      var pairs = url.split('&');
      var result = {};
      for (var idx in pairs) {
        var pair = pairs[idx].split('=');
        if (!!pair[0]) {
          result[pair[0]] = decodeURIComponent(pair[1] || '');
        }
      }
      return result;
    }
  };

  app.set_custom_cookie = function (cookie) {
    app.cookie.set(cookie.name, true, cookie.duration == 'days' ? cookie.days : 0);
  };

  app.set_custom_cookies_on_event = function (popup_id, event, cookie_name) {
    var custom_cookies = app.get_custom_cookies(popup_id);
    $.each(custom_cookies, function (index, cookie) {
      if (cookie.enable != 'on') return;
      if ((cookie_name && cookie_name === cookie.name && cookie.event === event) || (!cookie_name && cookie.event === event)) {
        if (!app.cookie.get(cookie.name)) {
          app.set_custom_cookie(cookie);
        }
      }
    });
  };

  app.get_custom_cookies = function (popup_id) {
    return MPP_POPUP_OPTIONS[popup_id] ? MPP_POPUP_OPTIONS[popup_id].custom_cookies : {};
  };

  app.has_cookie_not_show_popup = function (popup_id) {
    var has_cookie_not_show_popup = false;
    var custom_cookies = app.get_custom_cookies(popup_id);
    $.each(custom_cookies, function (cookie_name, cookie) {
      if (app.cookie.get(cookie.name) !== null && cookie.behavior && cookie.behavior.indexOf('not_show_popup') > -1) {
        has_cookie_not_show_popup = true;
      }
    });
    return has_cookie_not_show_popup;
  };

  app.get_cookie_event = function (event, options) {
    if (options.cookies && options.cookies[event]) {
      var cookie = options.cookies[event];
      if (cookie.enabled && app.cookie.get(cookie.name) !== null) {
        return app.cookie.get(cookie.name);
      }
    }
    return null;
  };

  app.create_cookie_loads_counter = function (popup_id) {
    var options = MPP_POPUP_OPTIONS[popup_id] ? MPP_POPUP_OPTIONS[popup_id] : false;
    if (options && options.open.loadCounter > 0 && !MPP_PUBLIC_JS.is_admin) {
      var loads = app.cookie.get(options.cookies.loadCounter.name);
      loads = loads === null ? 0 : Number(loads);
      var pageViews = loads;
      loads++;
      app.cookie.set(options.cookies.loadCounter.name, loads, 0);
      return {
        loadCounter: options.open.loadCounter,
        pageViews: pageViews,
      };
    }
    return {
      loadCounter: 0,
      pageViews: 0,
    }
  };

  app.show_popup_on_conversion = function (options) {
    var show = true;
    if (app.get_cookie_event('onConversion', options)) {
      show = false;
      if (options.afterConversion.message.length) {
        show = true;
      }
    }
    return show;
  };

  app.is_content_locker_enabled = function (options) {
    return options.contentLocker.enabled;
  };

  app.is_content_locker_enabled_with_password = function (options) {
    return (app.is_content_locker_enabled(options) && options.contentLocker.unlock == 'password');
  };

  app.show_popup_content_locker_whole_page = function (options) {
    return (app.is_content_locker_enabled(options) && options.contentLocker.type == 'whole_page')
  };

  app.is_content_locker_unlocked = function (options) {
    if (app.is_content_locker_enabled_with_password(options)) {
      return app.cookie.get(options.contentLocker.cookies.unlockWithPassword) !== null;
    } else {
      return app.cookie.get(options.contentLocker.cookies.unlockWithForm) !== null;
    }
  };

  app.unlock_content_locker_and_set_cookies = function (options) {
    if (app.is_content_locker_enabled(options)) {
      //Mostramos el contenido
      app.unlock_page_content(options);
      //Y creamos las cookies
      if (app.is_content_locker_enabled_with_password(options)) {
        app.cookie.set(options.contentLocker.cookies.unlockWithPassword, true, options.contentLocker.cookies.duration);
      } else {
        app.cookie.set(options.contentLocker.cookies.unlockWithForm, true, options.contentLocker.cookies.duration);
      }
    }
  };

  app.unlock_page_content = function (options) {
    $('.mpp-content-locker').fadeIn(300);
  };

  app.is_empty = function (value) {
    if (value === undefined || value === null) {
      return true;
    } else if (typeof value == 'object' && value instanceof $) {
      return value.length === 0;
    } else {
      return (value === false || $.trim(value).length === 0);
    }
  };

  app.parse_number = function (n, def) {
    n = parseFloat(n);
    if (isFinite(n)) {
      return n;
    }
    def = def || 1;
    return def;
  };

  app.in_scroll_top = function (value, compare) {
    compare = compare || '>=';
    var object = app.number_data(value);
    var n = app.parse_number(object.value);
    if (compare === '>=') {
      if (object.unit == '%') {
        return $(window).scrollTop() >= ($(document).height() - $(window).height()) * (n / 100);
      }
      return $(window).scrollTop() >= n;
    } else {
      if (object.unit == '%') {
        return $(window).scrollTop() <= ($(document).height() - $(window).height()) * (n / 100);
      }
      return $(window).scrollTop() <= n;
    }
  };

  app.in_scroll_element = function ($element, position, tolerance) {
    tolerance = tolerance || 0;
    position = position || 'top';
    var element_offset = $element.offset().top;
    var element_height = $element.outerHeight();
    var window_offset = $(window).scrollTop();
    var window_height = $(window).height();
    var diff = window_offset + window_height - element_offset;

    //No modificar la tolerancia para elementos con altura pequeña
    if (element_height > 10) {
      tolerance = Math.max(0, Math.min(element_height - 1, tolerance));
    }

    if (window_offset > element_offset) {
      if (window_offset + tolerance > element_offset + element_height) {
        return false;
      } else {
        return true;
      }
    }

    if (position == 'bottom') {
      var max_scroll = $('body').height() - window_height;
      diff = diff - element_height;
      if (diff > tolerance || window_offset > max_scroll) {
        return true;
      }
      return false;
    } else {
      if (diff > tolerance) {
        return true;
      }
      return false;
    }
  };

  app.number_data = function (value) {
    var number = {
      value: value,
      unit: undefined,
    };
    if (!value) {
      return number;
    }
    value = value.toString();
    if ($.inArray(value, ['auto', 'initial', 'inherit', 'normal']) > -1) {
      number.value = value;
      number.unit = undefined;
    } else if (value.indexOf('px') > -1) {
      number.value = value.replace('px', '');
      number.unit = 'px';
    } else if (value.indexOf('%') > -1) {
      number.value = value.replace('%', '');
      number.unit = '%';
    } else if (value.indexOf('em') > -1) {
      number.value = value.replace('em', '');
      number.unit = 'em';
    }
    return number;
  };

  app.cookie = {
    set: function (name, value, days) {
      var expires = "";
      if (days) {
        days = parseFloat(days, 10);
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
      }
      document.cookie = name + "=" + value + expires + "; path=/";
    },
    get: function (name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
          c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
          return c.substring(nameEQ.length, c.length);
        }
      }
      return null;
    },
    remove: function (name) {
      this.set(name, "", -1);
    }
  };
  app.reverse_object = function (object) {
    var newObject = {};
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    for (var i = keys.length - 1; i >= 0; i--) {
      var value = object[keys[i]];
      newObject[keys[i]] = value;
    }
    return newObject;
  };

  app.is_control_keypress = function (e) {
    // Allow: backspace=8, delete=46, tab=9, escape=27, enter=13
    if ($.inArray(e.keyCode, [8, 46, 9, 27, 13]) !== -1 ||
      // Allow: Ctrl/cmd+A
      (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: Ctrl/cmd+C
      (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: Ctrl/cmd+V
      (e.keyCode == 86 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: Ctrl/cmd+X
      (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return true;
    }
    return false;
  }
  app.is_number_keypress = function (e, simbol) {
    //https://stackoverflow.com/questions/469357/html-text-input-allows-only-numeric-input
    //Allow .
    if (simbol && simbol == '.' && $.inArray(e.keyCode, [110, 190]) !== -1) {
      return true;
    }
    //Allow -
    if (simbol && simbol == '-' && $.inArray(e.keyCode, [189, 109]) !== -1) {
      return true;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      return false;
    }
    return true;
  };

  app.isjQuery = function (obj) {
    return (obj && (obj instanceof jQuery || obj.constructor.prototype.jquery));
  };

  app.get_popup_id = function ($target) {
    return $target.closest('.mpp-box').data('popup-id');
  };

  app.get_instance = function (popup_id) {
    var $popup = app.get_popup_object(popup_id);
    return $popup.data('MasterPopup');
  };

  app.get_popup_object = function (popup_id) {
    var $popup;
    if (app.isjQuery(popup_id)) {
      if (popup_id.hasClass('mpp-container')) {
        $popup = popup_id.find('>.mpp-box');
      } else if (popup_id.hasClass('mpp-box')) {
        $popup = popup_id;
      } else {
        $popup = popup_id.closest('.mpp-box');
      }
    } else {
      $popup = $('.mpp-container-' + popup_id + '> .mpp-box');
    }
    return $popup;
  };

  app.open_popup_by_id = function (popup_id, options) {
    var $popup;
    if (options && typeof options === 'object') {
      $popup = $('.mpp-popup-' + popup_id).MasterPopups(options);
    } else if (!app.is_empty(MPP_POPUP_OPTIONS[popup_id])) {
      $popup = $('.mpp-popup-' + popup_id).MasterPopups(MPP_POPUP_OPTIONS[popup_id]);
    }
    return $popup;
  };

  app.open = function (popup_id, options) {
    return app.open_popup_by_id(popup_id, options);
  };

  app.get_popup_elements = function (popup_id) {
    var $popup = app.get_popup_object(popup_id);
    return $popup.find('.mpp-element');
  };

  app.close = function (popup_id) {
    var instance = app.get_instance(popup_id);
    if (instance) {
      instance.close();
    }
  };

  app.on = function (event_name, callback) {
    app.callbacks.push({
      name: event_name,
      callback: callback,
    });
  };

  app.call_events = function (event_name, $, popup_instance, popup_id, options, success) {
    if (app.callbacks) {
      app.callbacks.map(function (obj) {
        if (obj.name === event_name && typeof obj.callback === 'function') {
          obj.callback.call(this, $, popup_instance, popup_id, options, success);
        }
      });
    }
  };

  app.isCmdKey = function (e) {
    return !!e.ctrlKey || !!e.metaKey;//Mac support
  };

  app.isShiftKey = function (e) {
    return !!e.shiftKey;
  };
  app.isIE = function () {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }
    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf('rv:');
      return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }
    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }
    // other browser
    return false;
  }

  app.copy_to_clipboard = function (text) {
    var dummy = document.createElement("textarea");
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
  };


  app.downloadURI = function (uri, name) {
    var link = document.createElement("a");
    link.href = uri;
    link.download = name;
    link.target = '_blank';
    link.style.display = "none";

    document.body.appendChild(link);
    if (typeof MouseEvent !== "undefined") {
      link.dispatchEvent(new MouseEvent("click"));
    } else {
      link.click();
    }
    document.body.removeChild(link);
  };

  app.redirect = function (url, target, delay) {
    delay = delay || 0;
    target = target || '_self';
    if (url && url != 'http://' && url != '#') {
      if (target == '_blank') {
        if (delay === 0) {
          window.open(url, target);
        } else {
          setTimeout(function () {
            window.open(url, target);
          }, delay);
        }
      } else {
        if (delay === 0) {
          window.location = url;
        } else {
          setTimeout(function () {
            window.location = url;
          }, delay);
        }
      }
    }
  };

  app.regex_parser = function (input) {
    //Validate input
    if (typeof input !== "string") {
      throw new Error("Invalid input. Input must be a string");
    }

    //Parse input
    var m = input.match(/(\/?)(.+)\1([a-z]*)/i);

    //Invalid flags
    if (m[3] && !/^(?!.*?(.).*?\1)[gmixXsuUAJ]+$/.test(m[3])) {
      c('RegExp: Invalid flags');
      return new RegExp(input);
    }

    //Create the regular expression
    return new RegExp(m[2], m[3]);
  };

  app.execute_wp_ajax = function (popup_id, options) {
    var instance = app.get_instance(popup_id);
    instance.ajax(options, "execute_wp_ajax", !app.debug);
  };

  app.init_countdown = function ($element) {
    var $countdown = $element.find('.mpp-countdown');
    if ($countdown.length && typeof $.fn.MasterPopupsCountdown === 'function') {
      $countdown.MasterPopupsCountdown();
    }
  };

  app.init_recaptcha = function ($element) {
    var $recaptcha_wrap = $element.find('.mpp-recaptcha-wrap');
    var $recaptcha = $element.find('.mpp-recaptcha');
    var recaptcha_version = $recaptcha_wrap.data('version');
    var recaptcha_theme = $recaptcha_wrap.data('theme');
    var params = {
      'sitekey': MPP_PUBLIC_JS.google_recaptcha.site_key,
      'theme': recaptcha_theme,
      'size': recaptcha_version == 'invisible' ? 'invisible' : 'normal'
    };

    if (recaptcha_version == 'v2' || recaptcha_version == 'invisible') {
      //Solo colocar callback dentro de un popup, no en el editor de popup
      var popup_id = app.get_popup_id($element);
      if (recaptcha_version == 'invisible' && $element.closest('.mpp-container').length) {
        params['callback'] = function (token) {
          //El token para estas versiones está dentro del textarea name=g-recaptcha-response que se crea automáticamente
          var instance = app.get_instance(popup_id);
          if (!instance) {
            return;
          }
          var $form = instance.get_device_content($element.data('device'));
          instance.submit_form($form);
        }
      }
      var recaptcha_index = app.google_recaptcha.render($recaptcha[0], params);
      $recaptcha_wrap.attr('data-recaptcha-index', recaptcha_index);
    } else if (recaptcha_version == 'v3') {
      //La versión 3 de recaptcha no crea el campo para almacenar el token
      $element.find('.mpp-element-content .mpp-recaptcha').html('<input type="hidden" name="g-recaptcha-response">')
    }
  };

  app.google_recaptcha = {
    render: function (container, parameters) {
      if (typeof grecaptcha != "undefined" && typeof grecaptcha.render == "function") {
        return grecaptcha.render(container, parameters);
      }
      return false;
    },
    reset: function (opt_widget_id) {
      if (typeof grecaptcha != "undefined" && typeof grecaptcha.reset == "function") {
        return grecaptcha.reset(opt_widget_id);
      }
      return false;
    },
    //getResponse solo es para version 2 e invisible, no funciona con la version 3
    getResponse: function (opt_widget_id) {
      if (typeof grecaptcha != "undefined" && typeof grecaptcha.getResponse == "function") {
        try {
          return grecaptcha.getResponse(opt_widget_id);
        } catch (err) {
          console.log(err);
          app.show_message_after_submit_popup(null, app.recaptcha_error);
        }
      }
      return '';
    },
    execute: function (opt_widget_id) {
      if (typeof grecaptcha != "undefined" && typeof grecaptcha.execute == "function") {
        return grecaptcha.execute(opt_widget_id);
      }
      return '';
    },
    //Para recaptcha version 3
    readyExcecute: function (callback) {
      if (typeof grecaptcha != "undefined" && typeof grecaptcha.ready == "function") {
        grecaptcha.ready(function () {
          try {
            grecaptcha.execute(MPP_PUBLIC_JS.google_recaptcha.site_key, { action: 'submit' }).then(callback);
          } catch (err) {
            console.log(err);
            app.show_message_after_submit_popup(null, app.recaptcha_error);
          }
        });
      } else {
        app.show_message_after_submit_popup(null, app.recaptcha_error);
      }
    }
  };

  app.show_message_after_submit_popup = function (popup_id, message) {
    if (popup_id) {
      var instance = app.get_instance(popup_id);
      if (instance) {
        instance.show_message_after_submit(message);
      }
    } else {
      $('.mpp-is-open').each(function (index, popup) {
        var popup_id = app.get_popup_id($(popup));
        var instance = app.get_instance(popup_id);
        if (instance) {
          instance.show_message_after_submit(message);
        }
      });
    }
  };


  $.fn.MasterPopups = function (options) {
    if (typeof options === "string") {
      console.log('Options is string');
    } else {
      return this.each(function () {
        var popup_id = $(this).data('popup-id');
        if (options === undefined && popup_id) {
          options = MPP_POPUP_OPTIONS[popup_id];
        }
        if ($(this).data('MasterPopup')) {
          var open_event = 'click';
          if ($(this).data('popup')) {
            open_event = options.open.event || 'click';
          }
          return $(this).data('MasterPopup').open(open_event);
        }
        $(this).data('MasterPopup', new MasterPopups(this, options));
      });
    }
  };


  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg, msg2);
  }

  function clog(msg, msg2) {
    if (app.debug) {
      if( msg2 ){
        console.log(msg, msg2);
      } else {
        console.log(msg);
      }
    }
  }

  //Document Ready
  $(function () {
    mpp_manage_popup_templates();
  });

  function mpp_manage_popup_templates() {
    var $public_popup_templates = $('.public-popup-templates');
    var $control = $public_popup_templates.find('.mpp-control-popup-templates');
    var $wrap = $public_popup_templates.find('.mpp-wrap-popup-templates');
    var $categories = $public_popup_templates.find('.mpp-categories-popup-templates');
    var $tags = $public_popup_templates.find('.mpp-tags-popup-templates');

    $control.on('click', 'ul li', function (event) {
      var $btn = $(this);
      $btn.addClass('mpp-active').siblings().removeClass('mpp-active');
      var filter_category = $categories.length ? $categories.find('.mpp-active').data('filter') : 'all';
      var filter_tag = $tags.length ? $tags.find('.mpp-active').data('filter') : 'all';
      var filter_group = $btn.data('group');
      if (filter_group == 'category' && $tags.length) {
        $tags.find('li[data-filter="all"]').trigger('click');
      }

      var $items = $wrap.find('.mpp-item-popup-template').filter(function (index) {
        var data_category = $(this).data('category');
        var data_tags = $(this).data('tags');
        return data_category.indexOf(filter_category) > -1 && data_tags.indexOf(filter_tag) > -1;
      });

      $wrap.fadeTo(150, 0.15);
      $wrap.find('.mpp-item-popup-template').fadeOut(400).removeClass('mpp-scale-1');
      setTimeout(function () {
        $items.fadeIn(350).addClass('mpp-scale-1');
        $wrap.fadeTo(300, 1);
      }, 300);
    });

    $wrap.on('click', '.mpp-item-popup-template', function (event) {
      //console.log('Click popup template, Open popup');
    });
  };


  $(function (event) {
    //Copy Options to Clipboard
    $(document).on('click', '.mpp-box', function (e) {
      var popup_id = app.get_popup_id($(this));
      if (app.isCmdKey(e) && !app.isShiftKey(e)) {
        cc('=== POPUP ID', popup_id);
        app.copy_to_clipboard(popup_id);
      } else if (app.isCmdKey(e) && app.isShiftKey(e)) {
        cc('=== POPUP ID', popup_id);
        var obj = {};
        if (!MPP_POPUP_DISPLAY_OPTIONS) {
          cc('MPP_POPUP_DISPLAY_OPTIONS is undefined');
          return;
        }
        if (!MPP_POPUP_DISPLAY_OPTIONS[popup_id]) {
          cc('MPP_POPUP_DISPLAY_OPTIONS[popup_id] is undefined');
          return;
        }
        obj.display_options = MPP_POPUP_DISPLAY_OPTIONS[popup_id];
        cc('Display options', obj.display_options);
        if (obj.display_options.should_display) {
          obj.popup_options = MPP_POPUP_OPTIONS[popup_id];
          cc('Popup options', obj.popup_options);
        }
        app.copy_to_clipboard(JSON.stringify(obj));
      }
    });
  });


  return app;


})(jQuery, window, document);


//https://stackoverflow.com/questions/2655925/how-to-apply-important-using-css
(function ($) {
  if ($.fn._css) {
    return;
  }

  // Escape regex chars with \
  var escape = function (text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
  };

  // For those who need them (< IE 9), add support for CSS functions
  var isStyleFuncSupported = !!CSSStyleDeclaration.prototype.getPropertyValue;
  if (!isStyleFuncSupported) {
    CSSStyleDeclaration.prototype.getPropertyValue = function (a) {
      return this.getAttribute(a);
    };
    CSSStyleDeclaration.prototype.setProperty = function (styleName, value, priority) {
      this.setAttribute(styleName, value);
      var priority = typeof priority != 'undefined' ? priority : '';
      if (priority != '') {
        // Add priority manually
        var rule = new RegExp(escape(styleName) + '\\s*:\\s*' + escape(value) +
          '(\\s*;)?', 'gmi');
        this.cssText =
          this.cssText.replace(rule, styleName + ': ' + value + ' !' + priority + ';');
      }
    };
    CSSStyleDeclaration.prototype.removeProperty = function (a) {
      return this.removeAttribute(a);
    };
    CSSStyleDeclaration.prototype.getPropertyPriority = function (styleName) {
      var rule = new RegExp(escape(styleName) + '\\s*:\\s*[^\\s]*\\s*!important(\\s*;)?',
        'gmi');
      return rule.test(this.cssText) ? 'important' : '';
    }
  }

  // The style function
  $.fn._css = function (styleName, value, priority) {
    // DOM node
    var self = this;
    var node = self.get(0);
    // Ensure we have a DOM node
    if (typeof node == 'undefined') {
      return self;
    }
    var addCSS = function (htmlNode) {
      // CSSStyleDeclaration
      var style = htmlNode.style;
      // Getter/Setter
      if (typeof styleName != 'undefined') {
        if (typeof value != 'undefined') {
          // Set style property
          priority = typeof priority != 'undefined' ? priority : '';
          style.setProperty(styleName, value, priority);
          return self;
        } else {
          // Get style property
          return style.getPropertyValue(styleName);
        }
      } else {
        // Get CSSStyleDeclaration
        return style;
      }
    }
    var output = self;
    $.each(this, function (key, htmlNode) {
      output = addCSS(htmlNode);
    });
    return output;
  };
})(jQuery);


/*!
 * jQuery serializeMyObject - v0.2 - 1/20/2010
 * http://benalman.com/projects/jquery-misc-plugins/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */

// Whereas .serializeArray() serializes a form into an array, .serializeMyObject()
// serializes a form into an (arguably more useful) object.
(function ($, undefined) {
  '$:nomunge'; // Used by YUI compressor.
  $.fn.serializeMyObject = function () {
    var obj = {};
    $.each(this.serializeArray(), function (i, o) {
      var n = o.name,
        v = o.value;
      obj[n] = obj[n] === undefined ? v
        : $.isArray(obj[n]) ? obj[n].concat(v)
          : [obj[n], v];
    });
    return obj;
  };
})(jQuery);