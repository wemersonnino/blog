window.MaxCanvasEditor = (function (document, window, $) {
  "use strict";
  var app = {};
  var MPP = window.AdminMasterPopup;
  var MPP_Selectable = {
    contextMenu: {
      selected: {
        device: 'desktop',
        index: -1,
        element: undefined
      }
    },
    desktop: {
      instance: null,
      items: [],
      last_selected: -1,//index
    },
    mobile: {
      instance: null,
      items: [],
      last_selected: -1,//index
    }
  };
  var MPP_Draggable = {
    delay: 10,
    isDragging: false,
  }

  app.get_selected_element = function(device){
    var $element = null;
    $(MPP_Selectable[device].items).each(function(i, element){
      if( $(element).hasClass('mc-selected')){
        $element = $(element);
        return false;//break;
      }
    });
    return $element;
  };

  function Plugin(el, options) {
    var _ = this;
    _.$canvas_wrap = $(el);
    _.$canvas = _.$canvas_wrap.find('#mc');
    _.$canvas_viewport = _.$canvas_wrap.find('#mc-viewport');
    _.$canvas_device = _.$canvas_wrap.find('#mc-device');
    _.$context_menu = _.$canvas_wrap.find('#mc-context-menu');
    _.$axis_x = _.$canvas_wrap.find('#mc-x-rule');
    _.$axis_y = _.$canvas_wrap.find('#mc-y-rule');
    _.$frame = _.$canvas_wrap.find('.ampp-popup');
    _.$settings = _.$canvas.find('#mc-settings');
    _.$types = _.$canvas.find('#mc-types');
    _.defaults = {
      app: null,
      xbox: null,
      canvasResizable: '#mc-resizable-handler',
      canvasDeviceResizable: '#mc-device-resizable-handler',
    };
    _.options = $.extend(true, {}, _.defaults, options, _.$canvas_wrap.data('options'));
    _.$canvas_wrap.data('options', _.options);
    _.axis_x_left = 1500;//class-mc-editor.php build_rule(): for(-15 <-> 30)
    _.axis_y_top = 500;//class-mc-editor.php build_rule(): for(-5 <-> 10)

    _.init();
  }

  Plugin.prototype = {
    init: function () {
      var _ = this;
      _.build();
      _.set_initial_styles();
      _.set_resizable_canvas();
      _.events();

      $('#mc-settings input[type="radio"], #mc-settings input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_polaris',
        radioClass: 'iradio_polaris',
        increaseArea: '-18%'
      });

    },

    build: function () {
      var _ = this;
      //Info de las reglas
      // _.$canvas.append('<div id="mc-x-rule-guide" class="mc-y-guide"><span>0</span></div>');
      // _.$canvas.append('<div id="mc-y-rule-guide" class="mc-x-guide"><span>0</span></div>');

      //Guías
      _.$canvas_viewport.append('<div class="mc-x-guide mc-frame-x-guide mc-frame-top-guide"></div>');
      _.$canvas_viewport.append('<div class="mc-x-guide mc-frame-x-guide mc-frame-bottom-guide"></div>');
      _.$canvas_viewport.append('<div class="mc-y-guide mc-frame-y-guide mc-frame-left-guide"></div>');
      _.$canvas_viewport.append('<div class="mc-y-guide mc-frame-y-guide mc-frame-right-guide"></div>');

    },

    set_initial_styles: function () {
      var _ = this;
      if ($('body').hasClass('post-new-php')) {
        return;
      }
      var popup_info = _.get_popup_info();
      var canvas_device_height = _.$canvas_device.outerHeight();
      //$canvas-viewport-height: $canvas-device-height + ($canvas-resizable-height + 2px) + ($canvas-device-margin-vertical * 2);
      var canvas_viewport_height = canvas_device_height + ($(_.options.canvasResizable).outerHeight() + 2) + (popup_info.margin_top * 2)
      _.$canvas_viewport.css('height', canvas_viewport_height);
      //$canvas-height: $canvas-viewport-height + $axis-x-height;
      var canvas_height = canvas_viewport_height + _.$axis_x.outerHeight();
      _.$canvas.css('height', canvas_height);
    },

    set_resizable_canvas: function () {
      var _ = this;
      _.$canvas.resizable({
        minHeight: 280,
        maxHeight: 1000,
        handles: { 's': _.options.canvasResizable },
        resize: function (event, ui) {
          _.$canvas_viewport.css({
            'height': ui.size.height - _.$axis_x.outerHeight()
          });
        }
      });

      _.$canvas_device.resizable({
        minWidth: 320,
        minHeight: 320,
        handles: { 's': _.options.canvasDeviceResizable },
        resize: function (event, ui) {
          _.options.xbox.set_field_value($('.xbox-field-id-mpp_browser-height'), ui.size.height, 'px');
        }
      });
    },

    update_axis_position: function () {
      var _ = this;
      var popup_info = _.get_popup_info();
      var top = parseInt((-_.axis_y_top + _.$axis_x.outerHeight()) + popup_info.top - _.$canvas_viewport.scrollTop());
      var left = parseInt((-_.axis_x_left + _.$axis_y.outerWidth()) + popup_info.left - _.$canvas_viewport.scrollLeft());

      //console.log('get_popup_info', _.get_popup_info());
      _.update_pos_guides_ref_popup();

      _.$axis_y.css({
        top: top
      });
      _.$axis_x.css({
        left: left
      });
    },

    get_popup_info: function () {
      var _ = this;
      //var margin_left = Math.round(_.$canvas_device.css('margin-left').replace('px', ''));//Es 0 al hacer click encima del popup?
      var margin_top = Math.round(_.$canvas_device.css('margin-top').replace('px', ''));
      var margin_left = Math.round( Math.max(0, _.$canvas_viewport.outerWidth() - _.$canvas_device.outerWidth()) / 2);

      return {
        width: _.$frame.outerWidth(true),
        height: _.$frame.outerHeight(true),
        top: margin_top + _.$frame.position().top,
        left: margin_left + _.$frame.position().left,
        margin_top: margin_top,
        margin_left: margin_left,
      };
    },

    events: function () {
      var _ = this;
      //_.canvas_mousemove_event();
      //_.add_and_remove_guides();
      _.element_events();
      _.update_axis_position();
      _.on_click_icon_settings();
      _.open_close_panels();
      _.add_new_element();

      _.$canvas_viewport.on('keydown', function(e){
        _.on_keydown_viewport(e);
      });

      _.$canvas.on('after_add_element', function(event, args, $element){
        _.after_add_element(event, args, $element);
      });

      _.$canvas_viewport.scroll(function () {
        _.update_axis_position();
      });
      setInterval(function () {
        _.update_axis_position();
      }, 5000);

      $(window).resize(function (event) {
        if (event.target == this || $(event.target).attr('id') == 'mc-device' || $(event.target).attr('id') == 'mc') {
          _.update_axis_position();
        }
      }).trigger('resize');

      _.$frame.on('ampp_position_changed, ampp_size_changed', function (event, value) {
        _.update_axis_position();
      });

      _.$canvas.find('input[name="mc-show-guides"]').on('ifClicked', function (event) {
        if ($(this).is(':checked')) {
          _.$canvas.find('.mc-x-guide.mc-draggable-guide, .mc-y-guide.mc-draggable-guide').hide();
        } else {
          _.$canvas.find('.mc-x-guide.mc-draggable-guide, .mc-y-guide.mc-draggable-guide').show();
        }
      });
    },

    after_add_element: function (event, args, $element) {
      var _ = this;
      _.destroy_selectable();
      _.init_selectable();
    },

    add_new_element: function () {
      var _ = this;
      _.$types.find('.xbox-custom-add:not(.mc-working)').on('click', function (event) {
        var $btn = $(this);
        $btn.addClass('mc-working');
        _.$types.find('.ampp-loader').show();
        var type = $btn.data('item-type');
        var $row = _.options.app.$dk_els_row;
        if (_.options.app.get_active_device() == 'mobile') {
          $row = _.options.app.$mb_els_row;
        }
        setTimeout(function () {
          $row.find('>.xbox-label .xbox-custom-add[data-item-type="' + type + '"]').trigger('click');
          _.$types.find('.ampp-loader').hide();
          $btn.removeClass('mc-working');

          //GDPR text-html
          if (type == 'custom_field_input_checkbox_gdpr') {
            _.$types.find('.xbox-add-group-item[data-item-type="text-html"]').trigger('click');
          }
        }, 100);
      });
    },

    open_close_panels: function () {
      var _ = this;
      _.$canvas.find('#mc-open-settings').on('click', function (event) {
        var width = _.$settings.outerWidth();
        if (_.$types.hasClass('mc-is-open')) {
          _.$canvas.find('.mc-open-types').first().trigger('click');
        }
        if (_.$settings.hasClass('mc-is-open')) {
          $(this).find('i').removeClass('xbox-icon-backward').addClass('xbox-icon-wrench');
          _.$settings.stop().animate({ "left": '-=' + width }, 300);
        } else {
          $(this).find('i').removeClass('xbox-icon-wrench').addClass('xbox-icon-backward');
          _.$settings.stop().animate({ "left": '+=' + width }, 300);
        }
        _.$settings.toggleClass('mc-is-open');
      });

      _.$canvas.find('.mc-open-types').on('click', function (event) {
        var width = _.$types.outerWidth();
        if (_.$settings.hasClass('mc-is-open')) {
          _.$canvas.find('#mc-open-settings').trigger('click');
        }
        if (_.$types.hasClass('mc-is-open')) {
          _.$canvas.find('.mc-open-types').find('i').removeClass('xbox-icon-backward').addClass('xbox-icon-plus');
          _.$types.animate({ "left": '-=' + width }, 300);
        } else {
          _.$canvas.find('.mc-open-types').find('i').removeClass('xbox-icon-plus').addClass('xbox-icon-backward');
          _.$types.animate({ "left": '+=' + width }, 300);
        }
        _.$types.toggleClass('mc-is-open');
      });
    },

    y_guide_ref_popup: function (pos_left) {
      var _ = this;
      var popup_info = _.get_popup_info();
      return Math.round( pos_left - popup_info.left - _.$axis_y.outerWidth() );
      _.$axis_x.outerHeight()
    },

    x_guide_ref_popup: function (pos_top) {
      var _ = this;
      var popup_info = _.get_popup_info();
      return Math.round( pos_top - popup_info.top - _.$axis_x.outerHeight() );
    },

    update_pos_guides_ref_popup: function () {
      var _ = this;
      var popup_info = _.get_popup_info();
      _.$canvas_viewport.find('.mc-y-guide[data-refpopup]:not(.ui-draggable-dragging)').each(function(index, el){
        var $guide = $(el);
        var ref = $guide.data('refpopup');
        if( ref !== undefined ){
          ref = parseFloat(ref);
          var newLeft = parseFloat(ref) + popup_info.left + _.$axis_y.outerWidth();
          $guide.css('left', newLeft);
        }
      });
      _.$canvas_viewport.find('.mc-x-guide[data-refpopup]:not(.ui-draggable-dragging)').each(function(index, el){
        var $guide = $(el);
        var ref = $guide.data('refpopup');
        if( ref !== undefined ){
          ref = parseFloat(ref);
          var newTop = parseFloat(ref) + popup_info.top + _.$axis_x.outerHeight();
          $guide.css('top', newTop);
        }
      });
    },

    add_and_remove_guides: function () {
      // var _ = this;
      // _.$canvas.find('#mc-x-rule-guide').on('click', function (event) {
      //   var left = Math.round($(this).position().left);
      //   var refpopup = _.y_guide_ref_popup(left);
      //   _.$canvas_viewport.append('<div class="mc-y-guide mc-draggable-guide" data-refpopup="'+refpopup+'"><i class="xbox-icon xbox-icon-times-circle"></i></div>');
      //   var $guide = _.$canvas_viewport.find('.mc-y-guide.mc-draggable-guide').last();
      //   $guide.css('left', left).draggable({
      //     axis: 'x',
      //     containment: _.$canvas_viewport,
      //     stop: function(event, ui){
      //       refpopup = _.y_guide_ref_popup(ui.position.left);
      //       ui.helper.data('refpopup', refpopup).attr('data-refpopup', refpopup);
      //     }
      //   });
      // });
      // _.$canvas.find('#mc-y-rule-guide').on('click', function (event) {
      //   var top = Math.round($(this).position().top);
      //   var refpopup = _.x_guide_ref_popup(top);
      //   _.$canvas_viewport.append('<div class="mc-x-guide mc-draggable-guide" data-refpopup="'+refpopup+'"><i class="xbox-icon xbox-icon-times-circle"></i></div>');
      //   var $guide = _.$canvas_viewport.find('.mc-x-guide.mc-draggable-guide').last();
      //   $guide.css('top', top).draggable({
      //     axis: 'y',
      //     containment: _.$canvas_viewport,
      //     stop: function(event, ui){
      //       refpopup = _.x_guide_ref_popup(ui.position.top);
      //       ui.helper.data('refpopup', refpopup).attr('data-refpopup', refpopup);
      //     }
      //   });
      // });
      // _.$canvas.on('click', '.mc-draggable-guide i.xbox-icon', function (event) {
      //   $(this).closest('.mc-draggable-guide').remove();
      // });
    },

    canvas_mousemove_event: function () {
      // var _ = this;
      // _.$canvas.mousemove(function (event) {
      //   var position = _.get_target_position(event, this);
      //   var x = parseInt(-_.axis_x_left + position.x - _.$axis_x.position().left);
      //   var y = parseInt(-_.axis_y_top + position.y - _.$axis_y.position().top);
      //   _.$canvas.find('#mc-x-rule-guide').css('left', parseInt(position.x)).find('span').text(x);
      //   _.$canvas.find('#mc-y-rule-guide').css('top', parseInt(position.y)).find('span').text(y);
      // });
    },

    get_active_device: function () {
      return this.options.app.get_active_device();
    },

    element_events: function () {
      var _ = this;

      _.init_selectable('desktop');
      _.init_selectable('mobile');
      _.contextmenu_events();

      _.$canvas.find('.mc-element').each(function (index, el) {
        //Se desactivan al iniciar por el click obligatorio en el primer item: .options.app.$tab_device.on('click');
        _.init_resizable_element($(el));
        _.init_draggable_element($(el));
      });

      _.$canvas.on('mousedown', '.mc-element', function (event) {
        var $element = $(this);

        //event.stopPropagation();
        _.$canvas.find('.mc-element').removeClass('mc-selected');
        $element.addClass('mc-selected');
        _.add_selected_element($element);

        // _.$canvas.find('.mc-element').removeClass('mc-selected');
        // $element.addClass('mc-selected');
        //
        // //_.$canvas.removeClass('mc-not-selected');//Tool, Icon settings
        //
        // MPP.focus_without_scrolling($element);//for keyboard events
      });

      _.$canvas.on('click mouseenter touchstart', '.mc-element', function (event) {
        var $element = $(this);
        //_.$canvas.removeClass('mc-not-selected');//Tool, Icon settings
        MPP.focus_without_scrolling($element);//for keyboard events

        _.init_selectable();
        _.refresh_selected_elements();
        _.enable_disable_icon_tools(event);
        _.init_draggable_element($element);
        if (!$element.data('ui-resizable')) {
          _.init_resizable_element($element);
        }
      });

      _.$canvas_wrap.on('click', '.mc-ctx-item-link', function (event) {
        var $item = $(this);
        var action = $item.data('action');
        var autoClose = $item.data('auto-close');
        var $selected_element = MPP_Selectable.contextMenu.selected.element;
        //console.log("action", action);
        //console.log("autoClose", autoClose);
        //console.log("contextMenu", MPP_Selectable.contextMenu.selected);

        if( autoClose ){
          _.set_visibility_context_menu(false);
        }

        if( ! action || ! $selected_element || $selected_element.length == 0 ){
          return;
        }
        switch ( action ){
          case 'duplicate-element':
            _.options.app.duplicate_element($selected_element);
            break;
          case 'remove-element':
            _.options.app.remove_elements(MPP_Selectable[_.get_active_device()].items);
            break;
          case 'copy-style':
            _.options.app.copy_style_element($selected_element);
            break;
          case 'paste-style':
            _.options.app.paste_style_element($selected_element);
            break;
        }
      });

      //Destroy
      _.$canvas_viewport.on('click', function (event) {
        _.$canvas.find('.mc-element').removeClass('mc-selected');
        var $target = $(event.target);
        if( $target.closest('.mc-element').length ){
          $target.closest('.mc-element').addClass('mc-selected');
        }
        _.enable_disable_icon_tools(event);
        _.refresh_selected_elements();
        //_.$canvas.addClass('mc-not-selected');//Tool, Icon settings

        if ($target.hasClass('mc-element') || $target.closest('.mc-element').length) {
          return;
        }
        _.destroy_draggable_elements();
        _.destroy_resizable_elements();
      });
      
      _.options.app.$tab_device.on('click', function (event) {
        _.destroy_draggable_elements();
        _.destroy_resizable_elements();
      });

      _.options.app.$post_body.on('click', function (event) {
        var $target = $(event.target);
        if( ! $target.closest('#mc-context-menu').length ){
          _.set_visibility_context_menu(false);
        }
      });
    },

    contextmenu_events: function(){
      var _ = this;
      _.$canvas.on('contextmenu', '.mc-element', function (event) {
        event.preventDefault();
        //console.log("contextmenu", event.which, event);
        var $element = $(this);
        var device = $element.data('device');
        var index = $element.data('index');
        var offsetCanvas = _.$canvas_wrap.offset();
        _.$context_menu.css({top: event.pageY - offsetCanvas.top, left: event.pageX - offsetCanvas.left + 10});
        _.set_visibility_context_menu(true);
        MPP_Selectable.contextMenu.selected = {
          device: device,
          index: index,
          element: $element
        };
        //Ocultamos la opción de duplicar para los elements que no se deben duplicar
        var $ctx_item_duplicate = _.$context_menu.find('.mc-ctx-item.mc-duplicate-element');
        $ctx_item_duplicate.show();
        if ($.inArray($element.data('type'), _.options.app.unique_form_elements()) > -1) {
          $ctx_item_duplicate.hide();
        }
      });
    },

    enable_disable_icon_tools: function(event){
      var _ = this;
      var device = device || _.get_active_device();
      _.$canvas_wrap.find('.mc-icon-can-disable').toggleClass('mc-disabled', MPP_Selectable[device].items.length < 2);
    },

    init_selectable: function (device) {
      var _ = this;
      var device = device || _.get_active_device();
      if( ! MPP_Selectable[device].instance || ! MPP_Selectable[device].instance.enabled ){
        var selectableContainer = document.getElementById('mc-'+device+'-content');
        MPP_Selectable[device].instance = new Selectable({
          filter: selectableContainer.children,
          appendTo: selectableContainer,
          classes: {
            lasso: "mc-multiple-lasso",
            selected: 'mc-multiple-selected',
            container: "mc-multiple-container",
            selecting: "mc-multiple-selecting",
            selectable: "mc-multiple-selectable",
            unselecting: "mc-multiple-unselecting"
          },
          ignore: ".mc-controls"
        });
        MPP_Selectable[device].instance.on("start", function(e, item) {
          if( $(e.target).closest('.mc-element').length ){
            MPP_Selectable[device].instance.disable();
          }
          if( MPP.isCmdKey(e) || MPP.isShiftKey(e) ){
            return;
          }
          if( item === undefined || ! _.exist_selected_element($(item.node)) ){
            _.clear_selected_elements();
          }
        });
        MPP_Selectable[device].instance.on("end", function(e, items) {
          if( items !== undefined && items.length === 0 ){
            _.$canvas_viewport.trigger('click');
          }
          setTimeout(function(){
            _.init_draggable_elements();
            _.enable_disable_icon_tools(e);
          }, 50);
          if( items && items.length > 1 ){
            //Click en primer elemento cuando la selección es múltiple y no se ha hecho click en ninguno
            $(items[0].node).trigger('click');
          }
        });
        MPP_Selectable[device].instance.on("selecteditem", function(item) {
          _.add_selected_element(item.node);
        });
      }
    },

    add_selected_element: function (item) {
      var _ = this;
      var device = device || _.get_active_device();
      if( MPP.isjQuery(item) ){
        item = item.get(0);
      }
      if( ! _.exist_selected_element($(item)) ){
        MPP_Selectable[device].items.push(item);
        _.refresh_selected_elements();
      }
    },

    exist_selected_element: function ($item) {
      var _ = this;
      var exist = false;
      var device = _.get_active_device();
      $(MPP_Selectable[device].items).each(function(i, element){
        if( $item.data('index') == $(element).data('index') ){
          exist = true;
          return false;//break;
        }
      });
      return exist;
    },

    clear_selected_elements: function (device) {
      var _ = this;
      var device = device || _.get_active_device();
      MPP_Selectable[device].items = [];
      _.refresh_selected_elements();
    },

    destroy_selectable: function () {
      var _ = this;
      var device = _.get_active_device();
      if( MPP_Selectable[device].instance ){
        MPP_Selectable[device].instance.destroy();
        _.$canvas.find('.mc-multiple-lasso').remove();
      }
    },

    init_draggable_elements: function () {
      var _ = this;
      _.$canvas.find('.mc-element').each(function (index, el) {
        _.init_draggable_element($(el));
      });
    },

    init_draggable_element: function ($element) {
      var _ = this;
      if ($element.data('ui-draggable')) {
        return;
      }
      var device = _.get_active_device();
      var dragOptions = {
        delay: MPP_Draggable.delay,//100
        containment: _.$canvas_viewport.closest('#wpwrap'),
        create: function (event, ui) {
          // console.log('== Create drag');
        },
        drag: function (event, ui) {
          MPP_Draggable.isDragging = true;
          _.set_position_to_element_controls(ui.helper, ui.position);
          //Multiple drag
          if( MPP_Selectable[device].items.length > 1 ){
            var currentPosition = $(this).position();
            var prevPosition = $(this).data('prevPosition');
            if (!prevPosition) {
              prevPosition = ui.originalPosition;
            }
            var offsetLeft = currentPosition.left-prevPosition.left;
            var offsetTop = currentPosition.top-prevPosition.top;
            _.move_selected_elements(ui, offsetLeft, offsetTop);
            $(MPP_Selectable[device].items).each(function () {
              $(this).removeData('prevPosition');
            });
            $(this).data('prevPosition', currentPosition);
          }
        },
        start: function (event, ui) {
          ui.helper.addClass('mc-selected');
          _.add_selected_element(ui.helper.get(0));
          _.destroy_selectable();
        },
        stop: function (event, ui) {
          MPP_Draggable.isDragging = false;
          _.init_selectable();
          //Update positions
          _.on_stop_draggable_elements(event, ui);
        }
      };
      $element.draggable(dragOptions);
    },

    on_stop_draggable_elements: function(event, ui){
      var _ = this;
      var device = _.get_active_device();
      var $group_item = _.options.app.get_group_item(ui.helper.data('device'), ui.helper.data('index'));
      var $field = $group_item.find('.xbox-field-id-mpp_e-size-width');
      ui.helper.css('width', MPP.css.number($field.find('.xbox-element').val(), $field.find('input.xbox-unit-number').val()));
      $field = $group_item.find('.xbox-field-id-mpp_e-size-height');
      ui.helper.css('height', MPP.css.number($field.find('.xbox-element').val(), $field.find('input.xbox-unit-number').val()));

      _.set_position_to_element_controls(ui.helper, ui.position);
      
      if( MPP_Selectable[device].items && MPP_Selectable[device].items.length == 1 ){
        //Update xbox fields
        var field_values = [
          { name: 'e-position-top', value: parseInt(ui.position.top) },
          { name: 'e-position-left', value: parseInt(ui.position.left) }
        ];
        _.options.app.set_field_values(field_values, ui.helper.data('index'));
      } else {
        $(MPP_Selectable[device].items).each(function(i, element){
          var position = $(element).position();
          //Update xbox fields
          var field_values = [
            { name: 'e-position-top', value: parseInt(position.top) },
            { name: 'e-position-left', value: parseInt(position.left) }
          ];
          _.options.app.set_field_values(field_values, $(element).data('index'));
        });
      }
    },

    move_selected_elements: function(ui, offsetLeft, offsetTop){
      var _ = this;
      var device = _.get_active_device();
      $(MPP_Selectable[device].items).each(function(i, element){
        if( ui.helper.data('index') == $(element).data('index') ){
          return true;//continue
        }
        var position = $(element).position();
        var left = position.left;
        var top = position.top;
        $(element).css('left', parseInt(left + offsetLeft));
        $(element).css('top', parseInt(top + offsetTop));
        _.refresh_selected_elements();
      });
    },

    refresh_selected_elements: function(event){
      var _ = this;
      var device = _.get_active_device();
      _.$canvas.find('.mc-element').removeClass('mc-multiple-selected');
      $(MPP_Selectable[device].items).each(function(i, element){
        $(element).addClass('mc-multiple-selected');
      });
    },

    destroy_draggable_elements: function () {
      var _ = this;
      _.$canvas.find('.mc-element').each(function (index, el) {
        if ($(el).data('ui-draggable')) {
          $(el).draggable('destroy');
        }
      });
    },

    init_resizable_element: function ($element) {
      var _ = this;
      $element.resizable({
        handles: 'all',
        create: function (event, ui) {
          _.set_position_to_element_controls($(event.target), $(event.target).position());
        },
        resize: function (event, ui) {
          _.set_position_to_element_controls(ui.element, ui.position);
        },
        start: function(event, ui){
          _.destroy_selectable();
        },
        stop: function (event, ui) {
          _.init_selectable();
        }
      });
    },

    destroy_resizable_elements: function () {
      var _ = this;
      _.$canvas.find('.mc-element').each(function (index, el) {
        if ($(el).data('ui-resizable')) {
          $(el).resizable('destroy');
        }
      });
    },

    set_position_to_element_controls: function ($element, position) {
      position = position || $element.position();
      $element.find('.mc-controls .mc-position-element-left').text(parseInt(position.left));
      $element.find('.mc-controls .mc-position-element-top').text(parseInt(position.top));
    },

    get_target_position: function (event, element) {
      var docElem = document.documentElement;
      var rect = element.getBoundingClientRect();
      var scrollTop = docElem.scrollTop ? docElem.scrollTop : document.body.scrollTop;
      var scrollLeft = docElem.scrollLeft ? docElem.scrollLeft : document.body.scrollLeft;
      var elementLeft = rect.left + scrollLeft;
      var elementTop = rect.top + scrollTop;
      var x = event.pageX - elementLeft;
      var y = event.pageY - elementTop;
      return { x: x, y: y };
    },

    get_class_starts_with: function ($elment, starts_with) {
      return $.grep($elment.attr('class').split(" "), function (v, i) {
        return v.indexOf(starts_with) === 0;
      }).join();
    },

    on_keydown_viewport: function(event){
      var _ = this;
      var key = event.which;
      switch (event.which) {
        case 83: //S(Open/hide settings)
          _.$canvas.find('#mc-open-settings').trigger('click');
          break;
        case 90: //Ctrl + z
          if (MPP.isCmdKey(event)) {
            _.options.app.undo_changes(event);
          }
      }
    },

    on_click_icon_settings: function () {
      var _ = this;
      _.$canvas_wrap.on('click', '.mc-icon-setting:not(.mc-disabled), .mc-icon-tool:not(.mc-disabled)', function (event) {
        var device = _.get_active_device();
        var action = $(this).data('action');
        var info = _.get_elements_info();
        var bounds = _.get_elements_bounds();
        if( action == 'undo'){
          _.options.app.undo_changes(event);
          return;
        }
        if( action == 'distribute-heights' || action == 'distribute-widths' ){
          _.distribute_elements(event, action, info, bounds);
          return;
        }

        $(MPP_Selectable[device].items).each(function(i, element){
          _.transform_element_by_action(event, $(element), action, info, bounds);
        });
      });
    },

    distribute_elements: function (event, action, info, boundsOriginal) {
      var _ = this;
      var value = '';
      if( boundsOriginal.length <= 2 ) return;

      switch (action) {
        case 'distribute-heights':
          var bounds = boundsOriginal.sort(function(a,b){
            return a.top - b.top;
          });
          var space = bounds[bounds.length - 1].top - (bounds[0].topHeight);
          var newItems = bounds.slice(1, bounds.length - 1);
          var sum = 0;
          newItems.forEach(function(obj){
            sum += obj.height;
          });
          space = space - sum;
          var constant = Math.round(space/(bounds.length - 1));
          var temp = bounds[0].topHeight;
          newItems.forEach(function(obj){
            var newPos = temp + constant;
            value = newPos;
            _.options.app.set_field_values([{ name: 'e-position-top', value: value }], obj.index);
            temp = newPos + obj.height;
            //Important, Para el movimiento de "Multiple Selectable"
            obj.$element.removeData('prevPosition');
          });
          break;

        case 'distribute-widths':
          var bounds = boundsOriginal.sort(function(a,b){
            return a.left - b.left;
          });
          var space = bounds[bounds.length - 1].left - (bounds[0].leftWidth);
          var newItems = bounds.slice(1, bounds.length - 1);
          var sum = 0;
          newItems.forEach(function(obj){
            sum += obj.width;
          });
          space = space - sum;
          var constant = Math.round(space/(bounds.length - 1));
          var temp = bounds[0].leftWidth;
          newItems.forEach(function(obj){
            var newPos = temp + constant;
            value = newPos;
            _.options.app.set_field_values([{ name: 'e-position-left', value: value }], obj.index);
            temp = newPos + obj.width;
            //Important, Para el movimiento de "Multiple Selectable"
            obj.$element.removeData('prevPosition');
          });
          break;
      }
    },

    transform_element_by_action: function (event, $element, action, info, bounds) {
      var _ = this;
      var index = $element.data('index');
      var value = '';
      var sizes = {
        element: {
          width: $element.outerWidth(),
          height: $element.outerHeight(),
        },
        frame: {
          width: _.$frame.outerWidth(),
          height: _.$frame.outerHeight(),
        },
      };

      //Important, Para el movimiento de "Multiple Selectable"
      $element.removeData('prevPosition');


      //Position
      switch (action) {
        case 'position-top':
          _.options.app.set_field_values([{ name: 'e-position-top', value: '0' }], index);
          break;

        case 'position-center-y':
          value = parseInt((sizes.frame.height - sizes.element.height) / 2);
          _.options.app.set_field_values([{ name: 'e-position-top', value: value }], index);
          break;

        case 'position-bottom':
          value = sizes.frame.height - sizes.element.height;
          _.options.app.set_field_values([{ name: 'e-position-top', value: value }], index);
          break;

        case 'position-left':
          _.options.app.set_field_values([{ name: 'e-position-left', value: '0' }], index);
          break;

        case 'position-center-x':
          value = parseInt((sizes.frame.width - sizes.element.width) / 2);
          _.options.app.set_field_values([{ name: 'e-position-left', value: value }], index);
          break;

        case 'position-right':
          value = sizes.frame.width - sizes.element.width;
          _.options.app.set_field_values([{ name: 'e-position-left', value: value }], index);
          break;
      }

      //Aligment
      switch (action) {
        case 'alignment-top':
          value = parseInt(info.minTop);
          _.options.app.set_field_values([{ name: 'e-position-top', value: value }], index);
          break;

        case 'alignment-center-y':
          var movePosMaxHeight = parseInt((info.maxTop - info.minTop)/2);
          var newPosMaxHeight = info.maxTop - movePosMaxHeight;
          if( info.indexMaxHeight == info.indexMinTop ){
            newPosMaxHeight = info.minTop;
          }
          var temp = parseInt((info.maxHeight - sizes.element.height)/2);
          value = parseInt(newPosMaxHeight + temp);
          if( index == info.indexMaxHeight){
            _.options.app.set_field_values([{ name: 'e-position-top', value: newPosMaxHeight }], index);
          } else {
            _.options.app.set_field_values([{ name: 'e-position-top', value: value }], index);
          }
          break;

        case 'alignment-bottom':
          value = parseInt(info.maxBottom - sizes.element.height);
          _.options.app.set_field_values([{ name: 'e-position-top', value: value }], index);
          break;

        case 'alignment-left':
          value = parseInt(info.minLeft);
          _.options.app.set_field_values([{ name: 'e-position-left', value: value }], index);
          break;

        case 'alignment-center-x':
          var movePosMaxWidth = parseInt((info.maxLeft - info.minLeft)/2);
          var newPosMaxWidth = info.maxLeft - movePosMaxWidth;
          if( info.indexMaxWidth == info.indexMinLeft ){
            newPosMaxWidth = info.minLeft;
          }
          var temp = parseInt((info.maxWidth - sizes.element.width)/2);
          value = parseInt(newPosMaxWidth + temp);
          if( index == info.indexMaxWidth){
            _.options.app.set_field_values([{ name: 'e-position-left', value: newPosMaxWidth }], index);
          } else {
            _.options.app.set_field_values([{ name: 'e-position-left', value: value }], index);
          }
          break;

        case 'alignment-right':
          value = parseInt(info.maxRight - sizes.element.width);
          _.options.app.set_field_values([{ name: 'e-position-left', value: value }], index);
          break;
      }

      //Sizes
      switch (action) {
        case 'max-width':
          _.options.app.set_field_values([{
            name: 'e-size-width',
            value: info.maxWidth,
            unit: 'px'
          }], index);
          break;
        case 'max-height':
          _.options.app.set_field_values([{
            name: 'e-size-height',
            value: info.maxHeight,
            unit: 'px'
          }], index);
          break;
        case 'full-width':
          _.options.app.set_field_values([{
            name: 'e-size-width',
            value: sizes.frame.width,
            unit: 'px'
          }], index);
          break;
        case 'full-height':
          _.options.app.set_field_values([{
            name: 'e-size-height',
            value: sizes.frame.height,
            unit: 'px'
          }], index);
          break;
      }
    },

    get_elements_info: function () {
      var _ = this;
      var device = _.get_active_device();
      var obj = {
        indexMaxWidth: 0,
        indexMaxHeight: 0,
        indexMinLeft: 0,
        indexMinTop: 0,
        indexMaxTop: 0,
        maxLeft: -10000,
        maxTop: -10000,
        maxWidth: -10000,
        maxHeight: -10000,
        minLeft: 10000,
        minTop: 10000,
        minWidth: 10000,
        minHeight: 10000,
        maxBottom: -10000,
        maxRight: -10000,
      }
      $(MPP_Selectable[device].items).each(function(i, element){
        var position = $(element).position();
        var left = position.left;
        var top = position.top;
        var width = $(element).outerWidth();
        var height = $(element).outerHeight();
        var index = $(element).data('index');
        if( left > obj.maxLeft ){
          obj.maxLeft = left;
        }
        if( top > obj.maxTop ){
          obj.maxTop = top;
          obj.indexMaxTop = index;
        }
        if( width > obj.maxWidth ){
          obj.maxWidth = width;
          obj.indexMaxWidth = index;
        }
        if( height > obj.maxHeight ){
          obj.maxHeight = height;
          obj.indexMaxHeight = index;
        }
        if( left < obj.minLeft ){
          obj.minLeft = left;
          obj.indexMinLeft = index;
        }
        if( top < obj.minTop ){
          obj.minTop = top;
          obj.indexMinTop = index;
        }
        if( width < obj.minWidth ){
          obj.minWidth = width;
        }
        if( height < obj.minHeight ){
          obj.minHeight = height;
        }

        if( (top + height) > obj.maxBottom ){
          obj.maxBottom = top + height;
        }
        if( (left + width) > obj.maxRight ){
          obj.maxRight = left + width;
        }
      });
      return obj;
    },

    get_elements_bounds: function () {
      var _ = this;
      var device = _.get_active_device();
      var items = [];
      $(MPP_Selectable[device].items).each(function(i, element){
        if( $(element).data('type') == 'custom_field_input_hidden' ){
          return;
        }
        var position = $(element).position();
        var left = position.left;
        var top = position.top;
        var width = $(element).outerWidth();
        var height = $(element).outerHeight();
        var index = $(element).data('index');
        var obj = {
          index: index,
          $element: $(element),
          top: top,
          left: left,
          height: height,
          width: width,
          topHeight: top + height,
          leftWidth: left + width,
        };
        items.push(obj);
      });
      return items;
    },

    set_visibility_context_menu: function (visibility) {
      var _ = this;
      if( visibility ){
        _.$context_menu.css('display', 'block');
      } else {
        _.$context_menu.css('display', 'none');
      }
    }



  };

  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg + ': ');
    console.log(msg2);
  }

  function clog(msg) {
    console.log(msg);
  }

  $.fn.maxCanvasEditor = function (options) {
    var args = Array.prototype.slice.call(arguments, 1);

    return this.each(function () {
      var _data = $(this).data('mc-editor');

      if (!_data) {
        $(this).data('mc-editor', (_data = new Plugin(this, options)));
      }
      if (typeof options === "string") {
        if (_data[options]) {
          _data[options].apply(_data, args);
        }
      }
    });
  };

  app.MPP_Selectable = MPP_Selectable;

  return app;

}(document, window, jQuery));