window.MppPopupEditor = (function (window, document, $) {
  //Document Ready
  var app = {
    debug: false,
    prefix: 'mpp_',
    copy_element: {
      device: 'desktop',
      index: -1,
      styles: [],
    },
    DELAY_UPDATE_DEFAULTS: 400,
    changes: {
      desktop: [],
      mobile: [],
      undo: false,
    },
    last_element_tab: null,
  };
  app.DELAY_ON_ADD_ELEMENT = app.DELAY_UPDATE_DEFAULTS + 1200;
  app.DELAY_AFTER_ADD_ELEMENT = app.DELAY_ON_ADD_ELEMENT + 700;
  var MPP;//Admin Master Popups
  var xbox;//Xbox Framework
  var ElementContent;
  var MaxCanvasEditor;

  $(function () {
    xbox = window.XBOX;
    MPP = window.AdminMasterPopup;
    ElementContent = window.MppElementContent;
    MaxCanvasEditor = window.MaxCanvasEditor;
    app.init();
  });

  app.get_popup_id = function () {
    return $('#post_ID').val();
  };

  app.init = function () {
    app.$form = $('body.post-type-master-popups form[name="post"]');
    app.$post_body = $('body.post-type-master-popups #post-body');

    //Save post
    app.$post_body.on('click', '#save-popup:not(.ampp-disabled)', app.submit_save_post);

    app.$tab_device = app.$post_body.find('.tab-device-editor');
    app.$dk_els_row = app.$post_body.find('.xbox-row.xbox-row-id-mpp_desktop-elements');
    app.$mb_els_row = app.$post_body.find('.xbox-row.xbox-row-id-mpp_mobile-elements');
    app.$dk_els_list = app.$dk_els_row.find('.xbox-group-control').first();
    app.$mb_els_list = app.$mb_els_row.find('.xbox-group-control').first();
    app.$dk_els_group = app.$dk_els_row.find('.xbox-group-wrap').first();
    app.$mb_els_group = app.$mb_els_row.find('.xbox-group-wrap').first();
    app.$canvas_wrap = app.$post_body.find('#mc-wrap');
    app.$canvas = app.$post_body.find('#mc');
    app.$canvas_viewport = app.$canvas_wrap.find('#mc-viewport');
    app.$canvas_dk_content = app.$canvas.find('.ampp-desktop-content');
    app.$canvas_mb_content = app.$canvas.find('.ampp-mobile-content');

    app.$canvas_viewport.on('click', app.on_click_canvas_viewport);

    app.add_type_icon_to_elements(app.$dk_els_list);
    app.add_type_icon_to_elements(app.$mb_els_list);

    app.add_visibility_icon_to_elements(app.$dk_els_list);
    app.add_visibility_icon_to_elements(app.$mb_els_list);

    app.$dk_els_group.on('click', '> .xbox-group-item', app.on_click_group_item);
    app.$mb_els_group.on('click', '> .xbox-group-item', app.on_click_group_item);
    app.$dk_els_list.on('click', '.xbox-group-control-item', app.on_click_control_item);
    app.$mb_els_list.on('click', '.xbox-group-control-item', app.on_click_control_item);

    app.$dk_els_row.on('xbox_after_add_group_item', app.after_add_group_item);
    app.$mb_els_row.on('xbox_after_add_group_item', app.after_add_group_item);
    app.$dk_els_row.on('xbox_on_active_group_item', app.on_active_group_item);
    app.$mb_els_row.on('xbox_on_active_group_item', app.on_active_group_item);
    app.$dk_els_row.on('xbox_after_remove_group_item', app.after_remove_group_item);
    app.$mb_els_row.on('xbox_after_remove_group_item', app.after_remove_group_item);
    app.$dk_els_group.on('xbox_on_sortable_group_item', app.xbox_on_sortable_group_item);

    app.$tab_device.on('click', '.xbox-visibility-group-item', app.toggle_element_visibility);
    app.$canvas.on('click mousedown touchstart', '.mc-element', app.on_active_max_element);

    app.$canvas.on('drag', '.mc-element', app.on_drag_draggable_element);
    app.$canvas.on('resize', '.mc-element', app.on_resize_resizable_element);
    app.$canvas.on('resizestop', '.mc-element', app.on_stop_resizable_element);
    app.$canvas.on('keydown', '.mc-element', app.on_keydown_element);

    app.$canvas.on('mouseenter', '.mc-element', app.on_mouseenter_element);
    app.$canvas.on('mouseleave', '.mc-element', app.on_mouseleave_element);
    app.$canvas.on('dblclick', '.mc-element', app.on_dblclick_element);

    //Inicializamos lienzo de edición
    app.$canvas_wrap.maxCanvasEditor({
      app: app,
      xbox: xbox,
    });
    app.McEditor = app.$canvas_wrap.data('mc-editor');

    app.$post_body.on('click', '.ampp-open-icon-library', app.open_icon_library);
    app.$post_body.on('click', '.ampp-open-object-library', app.open_object_library);
    app.$tab_device.on('click', '.xbox-tab-body .xbox-tab-menu .xbox-item', function () {
      app.last_element_tab = $(this).data('item');
    });

    app.show_hide_form_elements();

    app.activate_form_type();
    app.countdown_events();
    app.init_google_recaptcha();

    //Copy Desktop Design
    app.$post_body.on('click', '.mc-copy-desktop-design', app.maybe_copy_desktop_elements);
    app.$post_body.on('click', '.ampp-link-go-tab', app.link_go_main_tab);
    //Clear cookies
    app.$post_body.on('click', '.ampp-btn-clear-cookie', app.clear_cookies);

    app.$canvas.on('mpp_element_content_updated', function (event, $element, $new_content) {
      app.init_countdown($element, $new_content);
      app.init_google_recaptcha($element, $new_content);
    });
  };

  app.countdown_events = function () {
    app.$post_body.on('statusChange', '.xbox-field-id-mpp_e-countdown-show-message input[type="hidden"]', app.add_countdown_message);

    setTimeout(function () {
      app.init_countdowns();
    }, 1000);//wait until app.units are loaded
  };

  app.init_countdowns = function () {
    app.$canvas.find('.mpp-element-countdown').each(function (index, element) {
      app.init_countdown($(element));
    });
  };

  app.init_countdown = function ($element, $new_content) {
    if ($element.data('type') == 'countdown') {
      $countdown = $element.find('.mpp-countdown');
      if ($countdown.length && typeof $.fn.MasterPopupsCountdown === 'function') {
        $countdown.MasterPopupsCountdown();

        //Para que se agreguen los estilos en línea ya que inicializar se destruye el countdown
        $element.trigger('mouseenter');
        $element.trigger('mouseleave');

        var $group_item = app.get_group_item($element.data('device'), $element.data('index'));
        var values = ElementContent.get_values_countdown($group_item);
        $countdown.css({
          'color': values['e-countdown-label-font-color'],
          'font-size': values['e-countdown-label-font-size'] + 'px'
        });
      }
    }
  };

  app.init_google_recaptcha = function ($target, $new_content) {
    $target = $target || app.$canvas.find('.mpp-element-field_recaptcha');
    $target.each(function(index, element){
      if ($(element).data('type') == 'field_recaptcha') {
        MasterPopups.init_recaptcha($(element));
      }
    });
  };

  app.add_countdown_message = function () {
    if ($(this).val() == 'on') {
      app.$canvas.find('#mc-types').find('.xbox-custom-add[data-item-type="text-html"]').trigger('click');
      setTimeout(function () {
        var $group_item = app.get_last_group_item(app.get_active_device());
        if ($group_item.data('type') != 'text-html') {
          return;
        }
        var field_values = [
          { name: 'e-content-textarea', value: 'This offer has expired!' },
          { name: 'e-attributes-class', value: 'mpp-countdown-message' },
          { name: 'e-font-size', value: '30', unit: 'px' },
        ];
        app.set_field_values(field_values, $group_item.data('index'));
      }, app.DELAY_AFTER_ADD_ELEMENT);
    }
  };

  app.submit_save_post = function (event) {
    event.preventDefault();

    //Prevenir que formularios personalizados con campos requeridos impidan que se envíe el formulario.
    app.$canvas.find('.ampp-el-content [required]').remove();

    var form_valid = true;
    var check_form = false;
    if (typeof app.$form[0].checkValidity === 'function') {
      check_form = true;
    }
    if (check_form) {
      form_valid = app.$form[0].checkValidity();
    }
    if (check_form && !form_valid) {
      var message = app.$form.getValidationMessages('.xbox-field input[type="date"], .xbox-field input[type="time"]');
      if (message) {
        alert(message);
      } else {
        alert('Error: Some fields are not valid.');
      }
      return;
    }

    var $btn = $(this);
    $btn.addClass('ampp-disabled').find('i').remove();
    $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.saving_changes,
      content: MPP_ADMIN_JS.text.please_wait,
      hide_confirm: true,
      hide_cancel: true,
      hide_close: true,
      wrap_class: 'ampp-transparent-confirm',
    });

    var save_interval = setInterval(function () {
      if (!$('#publish').hasClass('disabled')) {
        clearInterval(save_interval);
        //Remove source fields
        app.$post_body.find('.xbox-source-item').remove();

        //Eliminar el input hidden anterior
        app.$dk_els_row.find('input[name="mpp_desktop-elements"]').remove();
        app.$mb_els_row.find('input[name="mpp_mobile-elements"]').remove();

        //Serializamos datos para evitar "Warning: Unknown: Input variables exceeded 1000":max_input_vars
        var data_desktop = app.$dk_els_row.find('input[name],select[name],textarea[name]').serialize();
        var data_mobile = app.$mb_els_row.find('input[name],select[name],textarea[name]').serialize();

        app.$dk_els_row.find('.xbox-row').css('visibility', 'hidden');
        app.$dk_els_row.find('input[name],select[name],textarea[name]').remove();
        app.$dk_els_row.append('<input type="hidden" name="mpp_desktop-elements"/>');
        app.$dk_els_row.find('input[name="mpp_desktop-elements"]').val(data_desktop);

        app.$mb_els_row.find('.xbox-row').css('visibility', 'hidden');
        app.$mb_els_row.find('input[name],select[name],textarea[name]').remove();
        app.$mb_els_row.append('<input type="hidden" name="mpp_mobile-elements"/>');
        app.$mb_els_row.find('input[name="mpp_mobile-elements"]').val(data_mobile);

        //Save post
        $('#publish').click();
      }
    }, 300);
  };

  app.open_icon_library = function (event) {
    event.preventDefault();
    var $group_item = $(this).closest('.xbox-group-item');
    var $field = $group_item.find('.xbox-field-id-mpp_e-content-textarea');
    var $textarea = $field.find('.xbox-element');

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_get_icons_library',
      icon_font: true,
      svg: false,
      index: $group_item.data('index'),
    };
    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.object_library,
      content: {
        data: data,
        dataType: 'html',
        url: XBOX_JS.ajax_url,
        onSuccess: function (response) {
        }
      },
      hide_confirm: true,
      hide_cancel: true,
      wrap_class: 'ampp-object-library',
    });

    $(document).off('click', '.ampp-object-library .xbox-icons-wrap .xbox-item-icon-selector');
    $(document).on('click', '.ampp-object-library .xbox-icons-wrap .xbox-item-icon-selector', function (event) {
      $textarea.insertTextInCursor('<i class="' + $(this).data('value') + '"></i>');
      xbox.set_field_value($field, $textarea.val());
      $(this).closest('.ampp-object-library').find('.xbox-confirm-close-btn').trigger('click');
    });
    return false;
  };

  app.open_object_library = function (event) {
    event.preventDefault();
    var $group_item = $(this).closest('.xbox-group-item');
    var $field = $group_item.find('.xbox-field-id-mpp_e-content-object');

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_get_icons_library',
      icon_font: true,
      svg: true,
      index: $group_item.data('index'),
    };

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.object_library,
      content: {
        data: data,
        dataType: 'html',
        url: XBOX_JS.ajax_url,
        onSuccess: function (response) {
        }
      },
      hide_confirm: true,
      hide_cancel: true,
      wrap_class: 'ampp-object-library',
    });

    $(document).off('click', '.ampp-object-library .xbox-icons-wrap .xbox-item-icon-selector');
    $(document).on('click', '.ampp-object-library .xbox-icons-wrap .xbox-item-icon-selector', function (event) {
      xbox.set_field_value($field, $(this).data('value'));
      $(this).closest('.ampp-object-library').find('.xbox-confirm-close-btn').trigger('click');
    });
    return false;
  };

  app.after_add_group_item = function (event, args) {

    if (!args.duplicate) {
      setTimeout(function () {
        app.set_field_values(MPP_TYPES[args.type].field_values, args.index, null, true);
      }, app.DELAY_UPDATE_DEFAULTS);

      switch (args.type) {
        case 'object':
          //Si el tipo de elemento es 'object' abrimos popup
          args.$group_item.find('.xbox-row-id-mpp_e-content-object .ampp-open-object-library').trigger('click');
          break;
        case 'custom_field_input_checkbox_gdpr':
          setTimeout(function () {
            var $last_group_item = app.get_last_group_item(app.get_active_device());
            app.add_gdpr_values($last_group_item);
          }, app.DELAY_AFTER_ADD_ELEMENT);
          break;
        case '--':
          break;

      }
    }

    app.add_type_icon_to_element(args);
    app.add_element_to_canvas(args);
    app.show_hide_form_elements();

    if (args.duplicate) {
      setTimeout(function () {
        args.$group_item.trigger('click');
      }, 150);
    }
  };

  app.add_gdpr_values = function ($group_item) {
    if ($group_item.data('type') != 'text-html') {
      return;
    }
    var index = $group_item.data('index');
    var data = xbox.get_group_object_values($group_item);
    var group_values = app.get_group_values(data, app.get_active_device(), index);
    var field_values = [
      { name: 'e-content-textarea', value: 'Read and accept the Terms and Conditions' },
      { name: 'e-position-left', value: parseInt(group_values['e-position-left']) + 30 },
      { name: 'e-onclick-action', value: 'redirect-to-url' },
      { name: 'e-onclick-url', value: 'http://google.com' },
      { name: 'e-onclick-target', value: '_blank' },
      { name: 'e-cursor', value: 'pointer' },
    ];
    app.set_field_values(field_values, index);
  };

  app.show_hide_form_elements = function () {
    var $group_control = app.get_group_control(app.get_active_device());
    $.each(app.unique_form_elements(), function (index, type) {
      var $btn = app.$canvas.find('#mc-types .xbox-custom-add[data-item-type="' + type + '"]');
      if ($group_control.children('.xbox-group-control-item[data-type="' + type + '"]').length) {
        $btn.hide();
      } else {
        $btn.show();
      }
    });
  };

  app.on_active_max_element = function (event) {
    event.preventDefault();
    event.stopPropagation();
    var $control_item = app.get_control_item($(this).data('device'), $(this).data('index'));
    xbox.active_control_item(event, $control_item);
  };

  app.on_click_group_item = function (event) {
    var device = app.get_active_device($(this));
    var $element = app.get_element(device, $(this).data('index'));
    $element.addClass('mc-selected').siblings().removeClass('mc-selected');
    app.$canvas.removeClass('mc-not-selected');
  };

  app.on_click_control_item = function (event) {
  };

  app.on_click_canvas_viewport = function (event) {
    app.refresh_selected_control_items(event);
  };

  app.refresh_selected_control_items = function (event) {
    var device = app.get_active_device();
    var $group_control = app.get_group_control();
    var $control_items = $group_control.children('.xbox-group-control-item');
    $control_items.removeClass('xbox-multiple-active');
    $(MaxCanvasEditor.MPP_Selectable[device].items).each(function (i, element) {
      var index = $(element).data('index');
      $control_items.eq(index).addClass('xbox-multiple-active');
    });
  };

  app.on_active_group_item = function (event, args) {
    var device = app.get_active_device();
    var $element = app.get_element(device, args.index);
    $element.addClass('mc-selected').siblings().removeClass('mc-selected');
    app.$canvas.removeClass('mc-not-selected');
    args.$control_item.addClass('xbox-multiple-active');

    if (MPP.isCmdKey(args.event) || MPP.isShiftKey(args.event)) {
      //Agregar elemento actual
      app.McEditor.add_selected_element($element, device);
      var $previous_element = app.get_element(device, args.old_index);
      app.McEditor.add_selected_element($previous_element, device);
    } else {
      if (!app.McEditor.exist_selected_element($element, device)) {
        app.McEditor.clear_selected_elements();
      }
    }
    app.refresh_selected_control_items();

    //Update last tab element
    app.select_last_element_tab(args, device);
  };

  app.select_last_element_tab = function (args, device) {
    if (app.last_element_tab && args.$group_item) {
      var tab_item = app.last_element_tab.replace(/desktop|mobile/, '');
      var $tab_element = args.$group_item.find('.xbox-tab-menu .xbox-item[data-item="' + device + tab_item + '"]');
      var $tab = args.$group_item.find('.xbox-tab');
      var xboxTabs = $tab.data('xbox-tabs');
      if (!$tab_element.hasClass('active') && xboxTabs && typeof xboxTabs._change == 'function') {
        xboxTabs._change($tab_element.find('a'));
      }
    }
  };

  app.after_remove_group_item = function (event, index, type) {
    var device = app.get_active_device($(this));
    var $container = app.get_device_container(device);
    if ($container.children('.ampp-element').length == 1) {
      $container.children('.ampp-element').attr('data-index', -1).data('index', '-1');
    } else {
      app.get_element(device, index).remove();
      setTimeout(function () {
        app.sort_elements($container);
      }, 700);//Esperar que se eliminen todos los elementos si esque son varios
    }
    app.show_hide_form_elements();
  };

  app.add_type_icon_to_element = function (args) {
    var icon_class = args.$btn.find('i').attr('class');
    if (args.duplicate) {
      icon_class = args.$btn.closest('.xbox-actions').find('.xbox-sort-group-item i').attr('class');
    }
    args.$control_item.find('.xbox-sort-group-item i').attr('class', icon_class);
  };

  app.set_field_values = function (field_values, index, $target, set_default) {
    var $group_item = app.get_group_item(app.get_active_device($target), index);
    var $element = app.get_element(app.get_active_device($target), index);
    //var start = new Date().getTime();
    //console.log("start", start);
    $.each(field_values, function (i, field) {
      var $field = $group_item.find('.xbox-field-id-mpp_' + field.name);
      xbox.set_field_value($field, field.value, field.unit, set_default);
      if (set_default && field_values.length === i + 1) {
        app.set_visibility_control_loading($element, false);
        //var end = new Date().getTime();
        //console.log("Time: ", (end - start)/1000);
      }
    });
    if (field_values.length == 0) {
      app.set_visibility_control_loading($element, false);
    }
  };

  app.add_element_to_canvas = function (args) {
    var device = app.get_active_device(args.$btn);
    var $container = app.get_device_container(device);
    var $source_item = $container.find('.ampp-element').last();
    if (args.duplicate) {
      $source_item = $container.find('.ampp-element').eq(args.index - 1);
    }
    var $new_element = $source_item.clone();
    $new_element = app.cook_element($new_element, args, device);

    if ($source_item.data('index') > -1) {
      $source_item.after($new_element);
    } else {
      $source_item.remove();
      $container.append($new_element);
    }
    //Ordenar
    app.sort_elements($container);

    if (args.duplicate) {
      //Nueva posición al duplicar
      var data = xbox.get_group_object_values(args.$group_item);
      var group_values = app.get_group_values(data, device, args.index);
      var field_values = [
        { name: 'e-position-top', value: parseInt(group_values['e-position-top']) },
        { name: 'e-position-left', value: parseInt(group_values['e-position-left']) }
      ];
      app.set_field_values(field_values, args.index);
    } else {
      app.set_visibility_control_loading($new_element, true);
    }

    app.$canvas.trigger('after_add_element', [args, $new_element]);
  };

  //Preparamos el elemento con sus estilos antes de ser agregado al lienzo
  app.cook_element = function ($element, args, device) {
    $element.find('.ui-resizable-handle').remove();
    $element.removeClass('ui-resizable ui-draggable ui-draggable-handle');
    $element.attr('data-type', args.type);
    $element.attr('data-index', args.index);
    $element.alterClass('mpp-element-*', 'mpp-element-' + args.type);
    $element.alterClass('ampp-element-*', 'ampp-element-' + args.index);

    //Estilos
    var data = xbox.get_group_object_values(args.$group_item);
    var group_values = app.get_group_values(data, device, args.index);

    if (!args.duplicate) {
      group_values = app.merge_group_values(group_values, MPP_TYPES[args.type].field_values);
    }

    var $field = args.$group_item.find('.xbox-field').first();

    //Element
    var styles = {
      'z-index': args.index + 1,
      'width': MPP.css.number(group_values['e-size-width'], group_values['e-size-width_unit']),
      'height': MPP.css.number(group_values['e-size-height'], group_values['e-size-height_unit']),
      'top': MPP.css.number(group_values['e-position-top'], 'px'),
      'left': MPP.css.number(group_values['e-position-left'], 'px'),

      //Advanced
      'overflow': group_values['e-overflow'],
    };
    if (args.duplicate) {
      var top = parseInt(group_values['e-position-top']) + 20;
      var left = parseInt(group_values['e-position-left']) + 20;
      styles.top = MPP.css.number(top, 'px');
      styles.left = MPP.css.number(left, 'px');
    }

    $.each(styles, function (property, value) {
      app.set_style_to_element({
        event: args.event,
        $target: $field,
        $element: $element,
        property: property,
        value: value,
        style_type: 'normal',
      });
    });

    //Element content
    styles = {
      //Content
      'content': ElementContent.get_content($element, group_values, args),

      //Size & Position
      'padding-top': MPP.css.number(group_values['e-padding-top'], 'px'),
      'padding-right': MPP.css.number(group_values['e-padding-right'], 'px'),
      'padding-bottom': MPP.css.number(group_values['e-padding-bottom'], 'px'),
      'padding-left': MPP.css.number(group_values['e-padding-left'], 'px'),

      //Font
      'font-family': group_values['e-font-family'],
      'font-size': MPP.css.number(group_values['e-font-size'], group_values['e-font-size_unit']),
      'color': group_values['e-font-color'],
      'font-weight': group_values['e-font-weight'],
      'font-style': group_values['e-font-style'],
      'text-align': group_values['e-text-align'],
      'line-height': MPP.css.number(group_values['e-line-height'], group_values['e-line-height_unit']),
      'white-space': group_values['e-white-space'],
      'text-transform': group_values['e-text-transform'],
      'text-decoration': group_values['e-text-decoration'],
      'letter-spacing': group_values['e-letter-spacing'],
      'text-shadow': group_values['e-text-shadow'],

      //Background
      'background': '',
      'background-color': group_values['e-bg-color'],
      'background-repeat': group_values['e-bg-repeat'],
      'background-size': group_values['e-bg-size'],
      'background-position': group_values['e-bg-position'],
      'background-image': 'url(' + group_values['e-bg-image'] + ')',

      //Border
      'border-color': group_values['e-border-color'],
      'border-style': group_values['e-border-style'],
      'border-top-width': MPP.css.number(group_values['e-border-top-width'], 'px'),
      'border-right-width': MPP.css.number(group_values['e-border-right-width'], 'px'),
      'border-bottom-width': MPP.css.number(group_values['e-border-bottom-width'], 'px'),
      'border-left-width': MPP.css.number(group_values['e-border-left-width'], 'px'),
      'border-radius': MPP.css.number(group_values['e-border-radius'], 'px'),

      //Advanced
      'opacity': MPP.css.number(group_values['e-opacity']),
      'box-shadow': group_values['e-box-shadow'],
    };
    $.each(styles, function (property, value) {
      app.set_style_to_element({
        event: args.event,
        $target: $field,
        $element: $element.find('.ampp-el-content'),
        property: property,
        value: value,
        style_type: 'normal',
      });
    });

    //Hover
    styles = {
      'color': group_values['e-hover-font-color'],
      'background-color': group_values['e-hover-bg-color'],
      'border-color': group_values['e-hover-border-color'],
    };
    $.each(styles, function (property, value) {
      app.set_style_to_element({
        event: args.event,
        $target: $field,
        $element: $element.find('.ampp-el-content'),
        property: property,
        value: value,
        style_type: 'hover',
      });
    });

    return $element;
  };

  app.xbox_on_sortable_group_item = function (event, old_index, new_index) {
    var $group_wrap = $(this);
    var device = app.get_active_device($group_wrap);
    var $container = app.get_device_container(device);
    var $element = app.get_element(device, old_index);
    var $element_reference = app.get_element(device, new_index);

    if (old_index < new_index) {
      $element.insertAfter($element_reference);
    } else {
      $element.insertBefore($element_reference);
    }
    app.sort_elements($container);
  };

  app.set_style_to_popup = function (object) {
    var $target = '';
    if (object.$element == 'popup') {
      $target = app.$canvas.find('.ampp-popup');
    } else if (object.$element == 'wrap') {
      $target = app.$canvas.find('.ampp-wrap');
    } else if (object.$element == 'content') {
      $target = app.$canvas.find('.ampp-content');
    } else if (object.$element == 'overlay') {
      $target = app.$canvas.find('.ampp-overlay');
    }
    if ($target.length) {
      var new_css = {};
      new_css[object.property] = object.value;
      $target.css(new_css);
    }
  };

  app.set_style_to_element = function (object) {
    var element = '';
    var $group_item = object.$target.closest('.xbox-group-item');
    object.type = $group_item.data('type');
    object.index = $group_item.data('index');
    object.device = app.get_active_device(object.$target);
    object.style_type = object.style_type || 'normal';

    if (typeof object.$element == 'object') {
      object.$el_content = object.$element;
      if (object.$element.hasClass('ampp-element')) {
        element = 'element';
        object.style = object.$element.find('.ampp-el-content').data('style');
      } else if (object.$element.hasClass('ampp-el-content')) {
        element = 'el_content';
        object.style = object.$el_content.data('style');
      }
    } else {
      element = object.$element;
      object.$container = app.get_device_container(object.device);
      object.$element = app.get_element(object.device, object.index);
      object.$el_content = object.$element.find('.ampp-el-content');
      object.style = object.$el_content.data('style');
    }
    if (!object.$element.length || !object.$el_content.length || MPP.is_empty(object.style)) {
      return;
    }

    if (object.property == 'content') {
      if (element == 'el_content') {
        object.$el_content.html(object.value);
        app.$canvas.trigger('mpp_element_content_updated', [
          object.$el_content.closest('.ampp-element'),
          object.value]);
      }
    } else {
      if (object.style[object.style_type][object.property] == object.value) {
        return;//valor actual es igual al nuevo valor que quiere agregar
      }

      var new_css = {};
      new_css[object.property] = object.value;

      if (element == 'element' && object.style_type == 'normal') {
        object.$element.css(new_css);
        if (object.property == 'top' || object.property == 'left') {
          setTimeout(function () {
            app.$canvas_wrap.maxCanvasEditor('set_position_to_element_controls', object.$element);
          }, 5);
        }
      }

      if (element == 'el_content' && object.style_type == 'normal') {
        if (object.property == 'font-family') {
          app.add_link_rel_google_font(object.$el_content, object.value);
        }
        if (object.type === 'countdown') {
          object.$el_content.attr('style', '');
          app.add_css_to_countdown(object.$target, new_css, object.style_type);
        } else {
          object.$el_content.css(new_css);
        }
      }

      //Update css
      if (element == 'el_content') {
        if (object.property == 'background') {
          //Eliminar propiedades para que app.on_mouseleave_element() funcione correctamente.
          delete object.style[object.style_type]['background-color'];
          delete object.style[object.style_type]['background-repeat'];
          delete object.style[object.style_type]['background-size'];
          delete object.style[object.style_type]['background-position'];
          delete object.style[object.style_type]['background-image'];
        }
        if (object.property !== undefined) {
          object.style[object.style_type][object.property] = object.value;
          object.$el_content.attr('data-style', JSON.stringify(object.style));
        }
      }

    }


    if (object.name !== undefined && !app.changes.undo) {
      var firstItem = app.changes[object.device][0];
      var newItem = {
        index: object.index,
        name: object.name,
        property: object.property,
        value: object.value
      };
      app.$canvas_wrap.find('.mc-icon-tool[data-action="undo"]').removeClass('mc-disabled');

      if (!firstItem) {
        app.changes[object.device].unshift(newItem);
      } else {
        //Verificar si es otro elemento
        if (firstItem.index != object.index) {
          //En ese caso guardar cambio
          app.changes[object.device].unshift(newItem);
        } else {
          //Si es el mismo elemento, verificar si la propiedad es diferente
          if (firstItem.property != object.property) {
            app.changes[object.device].unshift(newItem);
          }
          //Si es la misma propiedad
          else if (firstItem.value != object.value) {//Sólo agregar si el valor es diferente
            var secondItem = app.changes[object.device][1];//Segundo item
            if (!secondItem || secondItem.property != object.property) {
              app.changes[object.device].unshift(newItem);
            } else {
              //Si existe el segundo item, entonces sólo actualizamos valor del primer item
              if (secondItem && secondItem.property == object.property) {
                app.changes[object.device][0].value = newItem.value;
              }
            }
          }
        }
      }
    }

    return object;
  };

  app.undo_changes = function (event) {
    var device = app.get_active_device();
    var changes = app.changes[device];
    //console.log('undo_changes', changes);

    var firstItem = changes[0];
    if (!firstItem) {

      return;
    }
    var $group_item = app.get_group_item(device, firstItem.index);
    var $field = $group_item.find('.xbox-field-id-mpp_' + firstItem.name);
    var exists = false;
    var value = '', unit;
    $.each(changes.slice(1), function (i, obj) {
      if (obj.name == firstItem.name && obj.index == firstItem.index) {
        value = obj.value;
        exists = true;
        return false;//break
      }
    });

    if (!exists) {
      var type = $field.closest('.xbox-row').data('field-type');
      switch (type) {
        case 'text':
        case 'colorpicker':
        case 'number':
        case 'switcher':
        case 'file':
          value = $field.find('.xbox-element').data('initial-value');
          if (type == 'number ') {
            unit = $field.find('.xbox-element').data('unit');
          }
          break;
        case 'select':
          value = $field.find('.xbox-element input[type="hidden"]').data('initial-value');
      }
    }

    app.changes.undo = true;
    app.changes[device].shift();
    xbox.set_field_value($field, value, unit);
    app.changes.undo = false;

    if (!app.changes[device].length) {
      app.$canvas_wrap.find('.mc-icon-tool[data-action="undo"]').addClass('mc-disabled');
    }

  };

  app.undo_changes_element = function ($element, device, index) {
    // var changes = $element.data('changes');
    // var $group_item = app.get_group_item(device, index);
    // var value, unit;
    // if (!MPP.is_empty(changes)) {
    //   console.log('No está vacío changes', changes);
    //   var reverse = changes.reverse();
    //   var first = reverse[0];
    //   var count = 1;
    //   //Comprobamos si hay varios registros del último cambio
    //   $.each(reverse, function (index, val) {
    //     if (val !== undefined && index > 0) {
    //       if (first.name == val.name) {
    //         count++;
    //       }
    //     }
    //   });
    //   var exists = false;
    //   var undo = {};
    //   var new_changes = [];
    //   reverse[0] = undefined;
    //   //Recorremos los cambios para obtener el valor del registro previo
    //   $.each(reverse, function (index, val) {
    //     if (val !== undefined && index > 0) {
    //       if (!exists && first.name == val.name) {
    //         exists = true;
    //         undo = val;
    //         if (count <= 2) {
    //           new_changes.push(val);
    //         }
    //       } else {
    //         new_changes.push(val);
    //       }
    //     }
    //   });
    //   $element.removeData('changes');
    //   //console.log('exists', exists);
    //   if (exists) {
    //     value = MPP.number_object(undo.value).value;
    //     unit = MPP.number_object(undo.value).unit;
    //     xbox.set_field_value($group_item.find('.xbox-field-id-mpp_' + undo.name), value, unit);
    //   } else {
    //     var $field = $group_item.find('.xbox-field-id-mpp_' + first.name);
    //     var type = $field.closest('.xbox-row').data('field-type');
    //     switch (type) {
    //       case 'text':
    //       case 'colorpicker':
    //       case 'number':
    //       case 'switcher':
    //       case 'file':
    //         value = $field.find('.xbox-element').data('initial-value');
    //         if (type == 'number ') {
    //           unit = $field.find('.xbox-element').data('unit');
    //         }
    //         break;
    //       case 'select':
    //         value = $field.find('.xbox-element input[type="hidden"]').data('initial-value');
    //     }
    //     xbox.set_field_value($field, value, unit);
    //   }
    //   new_changes = new_changes.reverse();
    //   $element.data('changes', new_changes);
    // }
  };

  app.add_css_to_countdown = function ($target, cssObj, style_type) {
    $.each(cssObj, function (property, value) {
      var object = {
        $target: $target,
        selector: '.ampp-el-content',
        property: property,
        value: value,
      }

      switch (property) {
        case 'width':
        case 'height':
        case 'box-shadow':
        case 'border-color':
        case 'border-style':
        case 'border-top-width':
        case 'border-right-width':
        case 'border-bottom-width':
        case 'border-left-width':
          object.selector = '.mpp-countdown .mpp-count-digit';
          break;

        case 'font-family':
        case 'color':
        case 'font-size':
        case 'font-weight':
        case 'font-style':
        case 'text-align':
        case 'text-shadow':
        case 'text-transform':

        case 'background-color':
        case 'background':
          if (['font-family', 'font-weight', 'font-style', 'text-align', 'text-transform'].indexOf(property) !== -1) {
            object.selector = '.mpp-countdown';
            app.set_style_to_countdown(object);
          }
          if (['background-color', 'background'].indexOf(property) !== -1) {
            object.selector = '.mpp-countdown .mpp-count-digit';
            app.set_style_to_countdown(object);
          }
          object.selector = '.mpp-countdown .mpp-count-digit .mpp-count';
          break;

        case 'border-radius':
          object.selector = '.mpp-countdown .mpp-count-digit';
          app.set_style_to_countdown(object);

          object.value = value + ' ' + value + ' 0 0';
          object.selector = '.mpp-countdown .mpp-count-digit .mpp-count.mpp-top';
          app.set_style_to_countdown(object);

          object.value = '0 0 ' + value + ' ' + value;
          object.selector = '.mpp-countdown .mpp-count-digit .mpp-count.mpp-bottom';
          break;
      }
      app.set_style_to_countdown(object);
    });
  }

  app.set_style_to_countdown = function (object) {
    var $group_item = object.$target.closest('.xbox-group-item');
    object.index = object.index !== undefined ? object.index : -1;
    if ($group_item.length) {
      object.index = $group_item.data('index');
    } else {
      object.index = object.$target.closest('.ampp-element').data('index');
    }

    if (!isNaN(object.index) && object.index === -1) {
      return;
    }

    object.device = app.get_active_device(object.$target);
    object.$element = app.get_element(object.device, object.index);
    object.$selector = object.$element.find(object.selector);
    if (object.$selector.length) {
      object.$selector.css(object.property, object.value);
    }

  }

  app.get_group_values = function (data, device, group_index) {
    var full_values = {};
    $.each(data, function (index, field) {
      var name = field.name.replace('mpp_' + device + '-elements[' + group_index + ']', '');
      //checkbox [mpp_e-field-name][]
      if (name.indexOf('[]', name.length - 2) !== -1) {//for checkboxs
        full_values[name] = full_values[name] ? full_values[name] + ',' + field.value : field.value;
      } else {
        full_values[name] = field.value;
      }
    });
    var values = {};
    $.each(full_values, function (name, value) {
      name = name.replace('mpp_', '');
      name = name.slice(1, -1);//Remove "[" and "]"
      if (name.indexOf('][') === -1) {
        values[name] = value;
      } else {
        name = name.replace('][', '');
        values[name] = value.split(',');
      }
    });
    return values;
  };

  app.get_field_value = function (field_id, device, index) {
    var $group_item = app.get_group_item(device, index);
    var data = xbox.get_group_object_values($group_item);
    var group_values = app.get_group_values(data, device, index);
    if (group_values && group_values.hasOwnProperty(field_id)) {
      return group_values[field_id];
    }
    return '';
  };

  app.add_type_icon_to_elements = function ($els_list) {
    $els_list.find('.xbox-group-control-item').each(function (index, el) {
      var type = $(el).data('type');
      if (MPP_TYPES[type]) {
        $(el).find('.xbox-sort-group-item i').attr('class', MPP_TYPES[type].icon);
      }
    });
  };

  app.add_visibility_icon_to_elements = function ($els_list) {
    $els_list.find('.xbox-group-control-item').each(function (index, el) {
      var $group_item = app.get_group_item(app.get_active_device(), index);
      var visibility = XBOX.get_group_item_visibility($group_item);
      if (visibility != 'visible') {
        $(el).find('.xbox-visibility-group-item i').attr('class', 'xbox-icon xbox-icon-eye-slash');
      }
    });
  };

  app.toggle_element_visibility = function (event) {
    event.stopPropagation();
    var index = $(this).closest('.xbox-group-control-item').data('index');
    var $group_item = app.get_group_item(app.get_active_device(), index);
    var $input = $group_item.find('.xbox-input-group-item-visibility');
    var $element = app.get_element(app.get_active_device(), index);
    var value = 'visible';

    if ($input.val() == 'visible') {
      $(this).find('i').attr('class', 'xbox-icon xbox-icon-eye-slash');
      value = 'hidden';
    } else {
      $(this).find('i').attr('class', 'xbox-icon xbox-icon-eye');
    }
    $input.val(value);
    app.set_style_to_element({
      event: event,
      $target: $(this),
      $element: $element,
      property: 'visibility',
      value: value,
      style_type: 'normal',
    });
  };

  app.on_drag_draggable_element = function (event, ui) {
    if (ui.helper.data('type') == 'text-html') {
      var $group_item = app.get_group_item(ui.helper.data('device'), ui.helper.data('index'));
      var $field = $group_item.find('.xbox-field-id-mpp_e-size-width');
      ui.helper.css('width', MPP.css.number($field.find('.xbox-element').val(), 'px'));
      $field = $group_item.find('.xbox-field-id-mpp_e-size-height');
      ui.helper.css('height', MPP.css.number($field.find('.xbox-element').val(), 'px'));
    }
  };

  app.on_resize_resizable_element = function (event, ui) {
    if ($(this).data('type') == 'image' || $(this).data('type') == 'object') {
      $(this).resizable("option", "aspectRatio", 1).data('uiResizable')._aspectRatio = 1;
    }
  };

  app.on_stop_resizable_element = function (event, ui) {
    var field_values;
    if (ui.element.data('type') == 'image' || ui.element.data('type') == 'object') {
      var $image = ui.element.find('.ampp-el-content > img');
      if ($image.length) {
        var height = ui.size.height;
        setTimeout(function () {
          var image_height = parseInt($image.outerHeight());
          height = image_height;
          field_values = [
            { name: 'e-size-height', value: height }
          ];
          app.set_field_values(field_values, ui.element.data('index'));
        }, 100);
      }
    }
    //Update size
    field_values = [
      { name: 'e-size-width', value: parseInt(ui.size.width) },
      { name: 'e-size-height', value: parseInt(ui.size.height) }
    ];
    app.set_field_values(field_values, ui.element.data('index'));

    //Update position (Necesario cuando redimensiona desde un control izquierdo)
    field_values = [
      { name: 'e-position-top', value: ui.position.top },
      { name: 'e-position-left', value: ui.position.left }
    ];
    app.set_field_values(field_values, ui.element.data('index'));
  };

  app.on_keydown_element = function (event) {
    var key = event.which;
    //cc('==== key',key);
    var device = app.get_active_device();
    var itemsToRemove = [];
    $(MaxCanvasEditor.MPP_Selectable[device].items).each(function (i, element) {
      var index = $(element).data('index');
      var position = $(element).position();
      switch (key) {
        case 37: //Left
          if (MPP.isShiftKey(event)) {
            value = position.left - 10;
          } else {
            value = position.left - 1;
          }
          app.set_field_values([{ name: 'e-position-left', value: value.toString() }], index);
          break;

        case 38: //Up
          if (MPP.isShiftKey(event)) {
            value = position.top - 10;
          } else {
            value = position.top - 1;
          }
          app.set_field_values([{ name: 'e-position-top', value: value.toString() }], index);
          break;

        case 39: //Right
          if (MPP.isShiftKey(event)) {
            value = position.left + 10;
          } else {
            value = position.left + 1;
          }
          app.set_field_values([{ name: 'e-position-left', value: value.toString() }], index);
          break;

        case 40: //Down
          if (MPP.isShiftKey(event)) {
            value = position.top + 10;
          } else {
            value = position.top + 1;
          }
          app.set_field_values([{ name: 'e-position-top', value: value.toString() }], index);
          break;

        case 46: //Remove element (Delete)
        case 8: //Remove element (Backspace)
          itemsToRemove.push(element);
          break;
      }
    });

    //Remove items
    if (itemsToRemove.length) {
      app.remove_elements(itemsToRemove);
      itemsToRemove = [];
    }

    var $selected = MaxCanvasEditor.get_selected_element(device);
    var $element = $selected || $(this);
    var type = $element.data('type');
    device = $element.data('device');
    var index = $element.data('index');
    var value = '';

    switch (key) {
      case 68: //Ctrl  + d (Duplicate)
      case 74: //Ctrl  + j
        if ($.inArray(type, app.unique_form_elements()) > -1) {
          return;//Salir de la función
        }
        if (MPP.isCmdKey(event)) {
          app.duplicate_element($element);
        }
        break;
      case 67: //Ctrl  + c
        if ($.inArray(type, app.unique_form_elements()) > -1) {
          return;//Salir de la función
        }
        if (MPP.isCmdKey(event)) {
          $element.data('copy', true);
        }
        break;
      case 86: //Ctrl  + v
        if ($.inArray(type, app.unique_form_elements()) > -1) {
          return;//Salir de la función
        }
        if (MPP.isCmdKey(event)) {
          if ($element.data('copy')) {
            app.duplicate_element($element);
          }
        }
        break;
      case 90: //Ctrl + z
        // if (MPP.isCmdKey(event)) {
        //   //app.undo_changes_element($element, device, index);
        // }
        break;
      default:
        break;
    }
    return false;
  };


  app.on_mouseenter_element = function (event, ui) {
    var $element = $(this);
    var $el_content = $element.find('.ampp-el-content');
    var $group_item = app.get_group_item($element.data('device'), $element.data('index'));
    var style = $el_content.data('style');

    if ($element.data('type') === 'countdown') {
      $el_content = $el_content.find('.mpp-count-digit .mpp-count');
    }

    if ($group_item.find('.xbox-field-id-mpp_e-hover-font-enable .xbox-element').val() == 'on') {
      $el_content.css({
        'color': style.hover.color
      });
    }
    if ($group_item.find('.xbox-field-id-mpp_e-hover-bg-enable .xbox-element').val() == 'on') {
      $el_content.css({
        'background': style.hover.background
      });
    }
    if ($group_item.find('.xbox-field-id-mpp_e-hover-border-enable .xbox-element').val() == 'on') {
      $el_content.css({
        'border-color': style.hover['border-color']
      });
    }
  };

  app.on_mouseleave_element = function (event, ui) {
    //Le agregamos los estilos normales
    var $element = $(this);
    var $el_content = $element.find('.ampp-el-content');
    var style = $el_content.data('style');

    if ($element.data('type') === 'countdown') {
      app.add_css_to_countdown($el_content, style.normal, 'normal');
    } else {
      $el_content.css(style.normal);
    }
  };

  app.on_dblclick_element = function (event) {
    var $max_element = $(this);
    var $group_item = app.get_group_item($max_element.data('device'), $max_element.data('index'));
    var $field, $textarea;
    var $tab = $group_item.find('>.xbox-tab');
    var canvas_height = app.$canvas.outerHeight();
    if ($tab.hasClass('accordion')) {
      $tab.find('>.xbox-tab-body > h3:eq(0) a').trigger('click');
    } else {
      $tab.find('>.xbox-tab-header .xbox-item:eq(0) a').trigger('click');
    }

    switch ($max_element.data('type')) {
      case 'close-icon':
        $field = $group_item.find('.xbox-field-id-mpp_e-content-close-icon');
        MPP.scroll_to($field, 500, canvas_height, function () {
          $field.find('input.xbox-search-icon').focus();
        });
        break;

      case 'object':
        $field = $group_item.find('.xbox-field-id-mpp_e-content-object');
        MPP.scroll_to($field, 500, canvas_height, function () {
        });
        break;

      case 'text-html':
      case 'shape':
      case 'button':
      case 'field_submit':
        $textarea = $group_item.find('.xbox-field-id-mpp_e-content-textarea .xbox-element');
        MPP.scroll_to($textarea, 500, canvas_height, function () {
          MPP.set_focus_end($textarea);
        });
        break;
      case 'image':
        $field = $group_item.find('.xbox-field-id-mpp_e-content-image');
        MPP.scroll_to($field, 500, canvas_height, function () {
          MPP.set_focus_end($field.find('.xbox-element'));
        });
        break;
      case 'video':
        $field = $group_item.find('.xbox-field-id-mpp_e-content-video');
        if (!$field.is(':visible')) {
          $field = $group_item.find('.xbox-field-id-mpp_e-content-video-html5');
        }
        MPP.scroll_to($field, 500, canvas_height, function () {
          MPP.set_focus_end($field.find('.xbox-element'));
        });
        break;
      case 'shortcode':
        $textarea = $group_item.find('.xbox-field-id-mpp_e-content-shortcode .xbox-element');
        MPP.scroll_to($textarea, 500, canvas_height, function () {
          MPP.set_focus_end($textarea);
        });
        break;
      case 'field_first_name':
      case 'field_last_name':
      case 'field_email':
      case 'field_phone':
      case 'field_message':
      case 'custom_field_input_text':
      case 'custom_field_input_hidden':
      case 'custom_field_input_checkbox':
      case 'custom_field_input_checkbox_gdpr':
      case 'custom_field_dropdown':
        $field = $group_item.find('.xbox-field-id-mpp_e-field-name');
        MPP.scroll_to($field, 500, canvas_height, function () {
          MPP.set_focus_end($field.find('.xbox-element'));
        });
        break;
    }
  };

  app.duplicate_element = function ($item) {
    if (!MPP.is_empty($item) && typeof $item == 'object') {
      if ($item.hasClass('xbox-group-control-item')) {
        $item.find('.xbox-duplicate-group-item').trigger('click');
        return true;
      } else if ($item.hasClass('ampp-element')) {
        $item = app.get_control_item($item.data('device'), $item.data('index'));
        if ($item) {
          $item.find('.xbox-duplicate-group-item').trigger('click');
          return true;
        }
      }
    }
    return false;
  };

  app.remove_elements = function (items) {
    //Usado en mc-editor.js cuando se hace click en Eliminar desde el menú contextual
    var device = app.get_active_device();
    var itemsToRemove = [];
    $(items).each(function (i, element) {
      var $control_item = app.get_control_item(device, $(element).data('index'));
      itemsToRemove.push($control_item);
    });
    if (itemsToRemove.length) {
      xbox.maybe_remove_group_items(itemsToRemove);
      MaxCanvasEditor.MPP_Selectable[device].items = [];
    }
  };

  app.copy_style_element = function ($element) {
    app.set_visibility_control_loading($element, true);
    var styles_to_copy = [
      'e-size-width', 'e-size-height',
      'e-padding-top', 'e-padding-right', 'e-padding-bottom', 'e-padding-left',

      'e-bg-repeat', 'e-bg-size', 'e-bg-position', 'e-bg-image', 'e-bg-color',
      'e-bg-enable-gradient', 'e-bg-color-gradient', 'e-bg-angle-gradient',
      'e-hover-bg-enable', 'e-hover-bg-color',

      'e-border-top-width', 'e-border-right-width', 'e-border-bottom-width', 'e-border-left-width', 'e-border-color', 'e-border-style', 'e-border-radius',
      'e-hover-border-enable', 'e-hover-border-color', 'e-focus-border-enable', 'e-focus-border-color',

      'e-font-family', 'e-font-color', 'e-font-size', 'e-font-weight', 'e-font-style', 'e-text-align', 'e-line-height', 'e-white-space', 'e-text-transform', 'e-text-decoration', 'e-letter-spacing', 'e-text-shadow',
      'e-hover-font-enable', 'e-hover-font-color',

      //'e-animation-enable', 'e-open-animation', 'e-open-delay', 'e-open-duration',

      'e-opacity', 'e-overflow', 'e-box-shadow',
    ];
    app.copy_element.device = $element.data('device');
    app.copy_element.index = $element.data('index');

    setTimeout(function () {
      app.set_visibility_control_loading($element, false);
    }, 600);
    return false;
  };

  app.set_visibility_control_loading = function ($element, visible) {
    $element.find('.mc-loading').toggle(visible);
  };

  app.paste_style_element = function ($element) {
    if (app.copy_element.index < 0) {
      return;
    }
    app.set_visibility_control_loading($element, true);
    var source_group_info = app.get_group_info(app.copy_element.device, app.copy_element.index);
    var group_info = app.get_group_info($element.data('device'), $element.data('index'));
    var fields_values_to_copy = [];
    var fields_iguales = [];

    //Eliminamos los campos que no se deben copiar
    delete source_group_info.group_values['e-content-textarea'];
    delete source_group_info.group_values['e-position-top'];
    delete source_group_info.group_values['e-position-left'];
    delete source_group_info.group_values['e-field-name'];
    delete source_group_info.group_values['e-field-placeholder'];
    delete source_group_info.group_values['e-field-value'];
    delete source_group_info.group_values['e-countdown-type'];
    delete source_group_info.group_values['e-content-date'];
    delete source_group_info.group_values['e-content-time'];
    delete source_group_info.group_values['e-countdown-labels'];
    delete source_group_info.group_values['e-recaptcha-version'];
    delete source_group_info.group_values['e-recaptcha-theme'];

    $.each(source_group_info.group_values, function (field_name, value) {
      if (value != group_info.group_values[field_name]) {
        fields_values_to_copy.push({
          name: field_name,
          value: value,
          unit: source_group_info.group_values[field_name + '_unit'],
        });
      }
    });

    //console.log('fields_values_to_copy', fields_values_to_copy);

    if (fields_values_to_copy.length > 0) {
      app.set_field_values(fields_values_to_copy, $element.data('index'));
    }
    setTimeout(function () {
      app.set_visibility_control_loading($element, false);
    }, 600);
  };

  app.maybe_copy_desktop_elements = function (e) {
    $.xboxConfirm({
      title: 'Copy Desktop Design',
      content: 'Your mobile popup design will be replaced by your desktop popup design.',
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      onConfirm: function () {
        setTimeout(function () {
          app.process_copy_desktop_elements(e);
        }, 150);
      }
    });
  }

  app.process_copy_desktop_elements = function (e) {
    $.xboxConfirm({
      title: '',
      content: MPP_ADMIN_JS.text.please_wait,
      hide_confirm: true,
      hide_cancel: true,
      hide_close: true,
      wrap_class: 'ampp-transparent-confirm',
      close_delay: 1200,
      onOpen: function () {
        setTimeout(function () {
          app.copy_desktop_elements(e);
        }, 700);
      }
    });
  }

  app.copy_desktop_elements = function (e) {
    var old_device = 'desktop';
    var new_device = 'mobile';
    var $canvas_content_cloned = app.$canvas_dk_content.clone();
    var $group_control_cloned = app.$dk_els_row.find('.xbox-group-control').first().clone();
    var $group_wrap_cloned = app.$dk_els_row.find('.xbox-group-wrap').first().clone();

    //Canvas content
    $canvas_content_cloned.find('.mc-element').each(function (index, el) {
      app.update_element_attribute($(el), 'data-device', old_device, new_device);
    });

    //Group control
    $group_control_cloned.find('input[name]').each(function (index, el) {
      app.update_element_attribute($(el), 'name', old_device, new_device);
      app.update_element_attribute($(el), 'id', old_device, new_device);
    });

    //Tab
    $group_wrap_cloned.find('li.xbox-item').each(function (index, el) {
      app.update_element_attribute($(el), 'class', old_device, new_device);
      app.update_element_attribute($(el), 'data-item', old_device, new_device);
      app.update_element_attribute($(el), 'data-tab', old_device, new_device);
      app.update_element_attribute($(el).find('>a'), 'href', old_device, new_device);
    });
    $group_wrap_cloned.find('.xbox-tab-content').each(function (index, el) {
      app.update_element_attribute($(el), 'class', old_device, new_device);
      app.update_element_attribute($(el), 'data-tab', old_device, new_device);
    });
    $group_wrap_cloned.find('.xbox-accordion-title').each(function (index, el) {
      app.update_element_attribute($(el), 'class', old_device, new_device);
      app.update_element_attribute($(el), 'data-item', old_device, new_device);
    });

    //Fields
    $group_wrap_cloned.find('input[name],select[name],textarea[name]').each(function (index, el) {
      app.update_element_attribute($(el), 'name', old_device, new_device);
      app.update_element_attribute($(el), 'id', old_device, new_device);
    });
    $group_wrap_cloned.find('label').each(function (index, el) {
      app.update_element_attribute($(el), 'for', old_device, new_device);
    });

    //Clear
    //app.$canvas_mb_content.html('');
    //app.$mb_els_list.html('');
    //app.$mb_els_group.html('');

    //Insertando elementos clonados
    app.$canvas_mb_content.html($canvas_content_cloned.html());
    app.$mb_els_list.html($group_control_cloned.html());
    app.$mb_els_group.html($group_wrap_cloned.html());

    //Init elements
    app.$mb_els_group.children('.xbox-group-item').each(function (index, el) {
      xbox.reinit_js_plugins($(el));
    });

    //Clear Selectable
    app.McEditor.destroy_selectable();
    app.McEditor.init_selectable();

    //Select the first element
    app.$mb_els_list.find('.xbox-group-control-item:eq(0)').trigger('click');
  };

  app.update_element_attribute = function ($el, attr, old_device, new_device) {
    var old_attr = $el.attr(attr);
    var new_attr = '';
    if (old_attr !== undefined) {
      var regex = new RegExp(old_device, 'g');
      new_attr = old_attr.replace(regex, new_device);
      $el.attr(attr, new_attr);
    }
  };

  app.sort_elements = function ($container) {
    $container.children('.ampp-element').each(function (index, el) {
      $(el).data('index', index).attr('data-index', index).css('z-index', index + 1);
    });
  };

  app.get_group_info = function (device, index) {
    var $group_item = app.get_group_item(device, index);
    var data = xbox.get_group_object_values($group_item);
    return {
      $group_item: $group_item,
      data: data,
      group_values: app.get_group_values(data, device, index)
    };
  };

  app.get_active_device = function ($el) {
    if (MPP.is_empty($el)) {
      return app.$canvas.find('#mc-device').data('device');
    } else {
      if ($el.closest('.ampp-content').length) {
        return $el.closest('.ampp-content').attr('class').indexOf('mobile') > -1 ? 'mobile' : 'desktop';
      }
      if ($el.closest('.xbox-tab-content').length) {
        return $el.closest('.xbox-tab-content').attr('class').indexOf('mobile') > -1 ? 'mobile' : 'desktop';
      }
    }
    return 'desktop';
  };

  app.get_device_container = function (device) {
    return app.$canvas.find('.ampp-' + device + '-content');
  };

  app.get_device_group = function (device) {
    return device == 'desktop' ? app.$dk_els_group : app.$mb_els_group;
  };

  app.get_group_item = function (device, index) {
    var $group = app.get_device_group(device);
    return $group.children('.xbox-group-item[data-index="' + index + '"]');
  };

  app.get_last_group_item = function (device) {
    var $group = app.get_device_group(device);
    return $group.children('.xbox-group-item').last();
  };

  app.get_group_control = function (device) {
    if (typeof device == 'object') {
      return device.closest('.xbox-type-group').find('.xbox-group-control').first();
    }
    device = device || app.get_active_device();
    return device == 'desktop' ? app.$dk_els_list : app.$mb_els_list;
  };

  app.get_control_item = function (device, index) {
    var $group_control = app.get_group_control(device);
    return $group_control.children('.xbox-group-control-item[data-index="' + index + '"]');
  };

  app.get_last_control_item = function (device) {
    var $group_control = app.get_group_control(device);
    return $group_control.children('.xbox-group-control-item').last();
  };

  app.get_element = function (device, index) {
    device = device || app.get_active_device();
    var $container = app.get_device_container(device);
    return $container.children('.ampp-element[data-index="' + index + '"]');
  };

  app.get_last_element = function (device) {
    device = device || app.get_active_device();
    var $container = app.get_device_container(device);
    return $container.children('.ampp-element').last();
  };

  app.merge_group_values = function (values, new_values) {
    var obj = {};
    var modified = false;
    $.each(new_values, function (index, field) {
      if (field.name) {
        modified = true;
        obj[field.name] = field.value !== undefined ? field.value : '';
        if (field.unit) {
          obj[field.name + '_unit'] = field.unit;
        }
      } else {
        return false;//break each loop
      }
    });
    if (modified) {
      new_values = obj;
    }
    return $.extend({}, values, new_values);
  }

  app.add_link_rel_google_font = function ($el_content, value) {
    if ($.inArray(value, MPP_ADMIN_JS.google_fonts) > -1) {
      value = value.replace(/\s+/g, '+');
      $el_content.next('link').remove();
      $el_content.after('<link href="//fonts.googleapis.com/css?family=' + value + ':100,200,300,400,500,600,700,800,900&subset=latin,latin-ext,greek,greek-ext,cyrillic,cyrillic-ext,vietnamese"  rel="stylesheet" type="text/css">');
    }
  };

  app.unique_form_elements = function () {
    return ['field_first_name', 'field_last_name', 'field_email', 'field_phone', 'field_message', 'field_submit', 'field_recaptcha'];
  };

  app.activate_form_type = function () {
    app.$post_body.on('ifClicked', '.xbox-field-id-mpp_form-submission-type input', function (event) {
      if ($(this).val() == 'contact-form') {
        app.$post_body.find('.tab-item-contact-form a').trigger('click');
      } else {
        app.$post_body.find('.tab-item-subscription-form a').trigger('click');
      }
    });
  };

  app.link_go_main_tab = function (event) {
    event.preventDefault();
    if ($(this).hasClass('ampp-link-go-tab-form-submission')) {
      app.$post_body.find('li.tab-item-form-submission a').trigger('click');
    }
  };

  app.clear_cookies = function (event) {
    event.preventDefault();
    if ($(this).hasClass('cookie-on-load')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.onLoad);
    } else if ($(this).hasClass('cookie-on-exit')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.onExit);
    } else if ($(this).hasClass('cookie-on-inactivity')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.onInactivity);
    } else if ($(this).hasClass('cookie-on-scroll')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.onScroll);
    } else if ($(this).hasClass('cookie-on-conversion')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.onConversion);
    } else if ($(this).hasClass('cookie-content-locker')) {
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.unlockWithForm);
      MPP.cookie.remove(MPP_ADMIN_JS.cookies.unlockWithPassword);
    }
    alert('Cookie deleted');
  };

  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg, msg2);
  }

  function clog(msg) {
    if (app.debug) {
      console.log(msg);
    }
  }

  return app;

})(window, document, jQuery);
