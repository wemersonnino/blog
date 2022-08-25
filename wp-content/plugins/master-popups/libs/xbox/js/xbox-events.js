XBOX.events = (function (window, document, $) {
  'use strict';
  var xbox_events = {};
  var xbox;

  xbox_events.init = function () {
    var $xbox = $('.xbox');

    xbox_events.on_change_colorpicker($xbox);

    xbox_events.on_change_code_editor($xbox);

    xbox_events.on_change_file($xbox);

    xbox_events.on_change_image_selector($xbox);

    xbox_events.on_change_icon_selector($xbox);

    xbox_events.on_change_number($xbox);

    xbox_events.on_change_oembed($xbox);

    xbox_events.on_change_radio($xbox);

    xbox_events.on_change_checkbox($xbox);

    xbox_events.on_change_switcher($xbox);

    xbox_events.on_change_select($xbox);

    xbox_events.on_change_text($xbox);

    xbox_events.on_change_date($xbox);

    xbox_events.on_change_time($xbox);

    xbox_events.on_change_textarea($xbox);

    xbox_events.on_change_wp_editor($xbox);

  };

  xbox_events.on_change_colorpicker = function ($xbox) {
    $xbox.on('input', '.xbox-type-colorpicker .xbox-element', function () {
      var $input = $(this);
      var value = $input.val();
      xbox.update_prev_values($(this), value);

      $(this).trigger('xbox_changed_value', value);

      //Actualizamos el cuadro color de vista previa
      var $field = $(this).closest('.xbox-field');
      var $preview_color = $field.find('.xbox-colorpicker-color');
      if( $preview_color.attr('value') != value ){
        $preview_color.attr('value', value).css('background-color', value);
      }

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), value, 'colorpicker');
    });
  };

  xbox_events.on_change_code_editor = function ($xbox) {
    $xbox.find('.xbox-code-editor').each(function (index, el) {
      var editor = ace.edit($(el).attr('id'));
      editor.getSession().on('change', function (e) {
        $(el).trigger('xbox_changed_value', editor.getValue());

        //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
        //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
        //xbox_events.change_field($(el), editor.getValue(), 'code_editor');
      });
    });
  };

  xbox_events.on_change_file = function ($xbox) {
    $xbox.on('change', '.xbox-type-file .xbox-element', function () {
      var $field = $(this).closest('.xbox-field');
      var multiple = $field.hasClass('xbox-has-multiple');
      var value = '';
      value = $(this).val();
      if( ! multiple ){
        value = $(this).val();
      } else {
        $field.find('.xbox-element').each(function(index, input){
          value += $(input).val() + ',';
        });
        value = value.replace(/,\s*$/, "");
        $(this).trigger('xbox_changed_value', value);
      }

      $(this).trigger('xbox_changed_value', value);

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), value, 'file');

      if (xbox.is_image_file(value) && !multiple) {
        var $wrap_preview = $(this).closest('.xbox-field').find('.xbox-wrap-preview').first();
        var preview_size = $wrap_preview.data('preview-size');
        var item_body;
        var obj = {
          url: value,
        };
        var $new_item = $('<li />', { 'class': 'xbox-preview-item xbox-preview-file' });
        $new_item.addClass('xbox-preview-image');
        item_body = '<img src="' + obj.url + '" style="width: ' + preview_size.width + '; height: ' + preview_size.height + '" data-full-img="' + obj.url + '" class="xbox-image xbox-preview-handler">';
        $new_item.html(item_body + '<a class="xbox-btn xbox-btn-iconize xbox-btn-small xbox-btn-red xbox-remove-preview"><i class="xbox-icon xbox-icon-times-circle"></i></a>');
        $wrap_preview.html($new_item);
      }
    });
    $xbox.on('xbox_after_add_files', '.xbox-type-file .xbox-field', function (e, selected_files, media) {
      var value;
      if (!media.multiple) {
        $(selected_files).each(function (index, obj) {
          value = obj.url;
        });
      } else {
        value = [];
        $(selected_files).each(function (index, obj) {
          value.push(obj.url);
        });
      }
      $(this).find('.xbox-element').trigger('xbox_changed_value', value);
      xbox_events.change_field($(this), value, 'file');
    });
  };

  xbox_events.on_change_image_selector = function ($xbox) {
    $xbox.on('imgSelectorChanged', '.xbox-type-image_selector .xbox-element', function () {
      if ($(this).closest('.xbox-image-selector').data('image-selector').like_checkbox) {
        var value = [];
        $(this).closest('.xbox-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
          value.push($(this).val());
        });
        $(this).trigger('xbox_changed_value', value);
        xbox_events.change_field($(this), value, 'image_selector');
      } else {
        $(this).trigger('xbox_changed_value', $(this).val());
        xbox_events.change_field($(this), $(this).val(), 'image_selector');
      }
    });
  };

  xbox_events.on_change_icon_selector = function ($xbox) {
    $xbox.on('change', '.xbox-type-icon_selector .xbox-element', function () {
      $(this).trigger('xbox_changed_value', $(this).val());
      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), $(this).val(), 'icon_selector');
    });
  };

  xbox_events.on_change_number = function ($xbox) {
    $xbox.on('change', '.xbox-type-number .xbox-unit-number', function () {
      $(this).closest('.xbox-field').find('.xbox-element').trigger('input');
    });
    $xbox.on('input', '.xbox-type-number .xbox-element', function () {
      $(this).trigger('xbox_changed_value', $(this).val());
      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), $(this).val(), 'number');
    });
    $xbox.on('change', '.xbox-type-number .xbox-element', function () {
      var value = $(this).val();
      var validValue = value;
      var arr = ['auto', 'initial', 'inherit'];
      if ($.inArray(value, arr) < 0) {
        validValue = value.toString().replace(/[^0-9.\-]/g, '');
      }
      //Validate values
      if( value != validValue ){
        value = validValue;
        var $field = $(this).closest('.xbox-field');
        xbox.set_field_value($field, value, $field.find('input.xbox-unit-number').val());
      }
      $(this).trigger('xbox_changed_value', value);
      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), value, 'number');
    });
  };

  xbox_events.on_change_oembed = function ($xbox) {
    $xbox.on('change', '.xbox-type-oembed .xbox-element', function () {
      $(this).trigger('xbox_changed_value', $(this).val());
      //xbox_events.change_field($(this), $(this).val(), 'oembed');
    });
  };

  xbox_events.on_change_radio = function ($xbox) {
    $xbox.on('ifChecked', '.xbox-type-radio .xbox-element', function () {
      var $input = $(this);
      //Ya no es necesario cambiar el atributo checked
      //Para obtener el valor por el name del input usar $('[name=test]:checked').val()
      //$input.closest('.xbox-radiochecks').find('input.xbox-element').removeAttr('checked').prop('checked', false);
      //$input.attr('checked', 'checked').prop('checked', true);
      $(this).trigger('xbox_changed_value', $input.val());
      xbox_events.change_field($(this), $input.val(), 'radio');
    });
  };

  xbox_events.on_change_checkbox = function ($xbox) {
    $xbox.on('ifChanged', '.xbox-type-checkbox .xbox-element', function () {
      var value = [];
      $(this).closest('.xbox-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
        value.push($(this).val());
      });
      $(this).trigger('xbox_changed_value', value);
      xbox_events.change_field($(this), value, 'checkbox');
    });
  };

  xbox_events.on_change_switcher = function ($xbox) {
    $xbox.on('statusChange', '.xbox-type-switcher .xbox-element', function () {
      $(this).trigger('xbox_changed_value', $(this).val());
      xbox_events.change_field($(this), $(this).val(), 'switcher');
    });
  };

  xbox_events.on_change_select = function ($xbox) {
    $xbox.on('change', '.xbox-type-select .xbox-element', function (event) {
      var $input = $(this).find('input[type="hidden"]');
      var value = $input.val();
      xbox.update_prev_values($input, value);
      $(this).trigger('xbox_changed_value', value);
      xbox_events.change_field($(this), value, 'select');
    });
  };

  xbox_events.on_change_text = function ($xbox) {
    $xbox.on('input', '.xbox-type-text .xbox-element', function () {
      var $input = $(this);
      var value = $input.val();
      xbox.update_prev_values($input, value);
      $input.trigger('xbox_changed_value', value);

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($input, value, 'text');

      var $helper = $input.next('.xbox-field-helper');
      if ($helper.length && $input.closest('.xbox-helper-maxlength').length && $input.attr('maxlength')) {
        $helper.text($input.val().length + '/' + $input.attr('maxlength'));
      }
    });
  };

  xbox_events.on_change_date = function ($xbox) {
    $xbox.on('change', '.xbox-type-date .xbox-element', function () {
      var $input = $(this);
      var value = $input.val();
      xbox.update_prev_values($input, value);
      $input.trigger('xbox_changed_value', value);

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($input, value, 'date');
    });
  };

  xbox_events.on_change_time = function ($xbox) {
    $xbox.on('change', '.xbox-type-time .xbox-element', function () {
      var $input = $(this);
      var value = $input.val();
      xbox.update_prev_values($input, value);
      $input.trigger('xbox_changed_value', value);

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($input, value, 'time');
    });
  };

  xbox_events.on_change_textarea = function ($xbox) {
    $xbox.on('input', '.xbox-type-textarea .xbox-element', function () {
      $(this).text($(this).val());
      $(this).trigger('xbox_changed_value', $(this).val());
      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), $(this).val(), 'textarea');
    });
  };

  xbox_events.on_change_wp_editor = function ($xbox) {
    var $wp_editors = $xbox.find('.xbox-type-wp_editor textarea.wp-editor-area');
    $xbox.on('input', '.xbox-type-wp_editor textarea.wp-editor-area', function () {
      $(this).trigger('xbox_changed_value', $(this).val());

      //No colocar porque relentiza xbox.set_field_value cuando hay muchos campos
      //Y es poco probable se necesite ver cambios en este campo para mostrar u ocultar campos
      //xbox_events.change_field($(this), $(this).val(), 'wp_editor');
    });
    if (typeof tinymce === 'undefined') {
      return;
    }
    setTimeout(function () {
      $wp_editors.each(function (index, el) {
        var ed_id = $(el).attr('id');
        var wp_editor = tinymce.get(ed_id);
        if (wp_editor) {
          wp_editor.on('change input', function (e) {
            var value = wp_editor.getContent();
            $(el).trigger('xbox_changed_value', wp_editor.getContent());
            xbox_events.change_field($(el), wp_editor.getContent(), 'wp_editor');
          });
        }
      });
    }, 1000);
  };

  xbox_events.change_field = function ($el, field_value, type ) {
    xbox_events.show_hide_row($el, field_value, type );
    xbox_events.change_name_if($el, field_value);
  };

  xbox_events.change_name_if = function ($el, field_value) {
    var prefix = $el.closest('.xbox').data('prefix');
    var $row_changed = $el.closest('.xbox-row');

    //Soporte nuevo, verifica en todos los campos
    var $rows = $row_changed.closest('.xbox').find('.xbox-row');
    var $group_item = $row_changed.closest('.xbox-group-item');
    var group_index = -1;
    if ($group_item.length) {
      group_index = $group_item.data('index');
    }

    //Soporte anterior para verificar solo campos dentro del mismo nivel
    // var $rows = $row_changed.siblings('.xbox-row');
    // var $group_item = $row_changed.closest('.xbox-group-item');
    // if ($group_item.length) {
    //   $rows = $group_item.find('.xbox-row');
    // } else {
    //   $rows.each(function (index, el) {
    //     if ($(el).data('field-type') == 'mixed') {
    //       $(el).find('.xbox-row').each(function (i, mixed_row) {
    //         $rows.push($(mixed_row)[0]);
    //       });
    //     }
    //   });
    // }

    $rows.each(function (index, el) {
      var $row = $(el);
      var field_title = $row.data('label-title');
      var data_name_if = $row.data('name-if');
      if( group_index >= 0 && ! $row.closest('.xbox-group-item[data-index='+group_index+']').length ){
        return true;//continue
      }
      if( ! data_name_if ){
        return true;//continue
      }
      var name_if = data_name_if.name_if;
      if (is_empty(name_if)) {
        return true;//continue
      }

      var change_title = false;
      $.each(name_if, function(field_id, posible_values){
        if ($row.is($row_changed) || $row_changed.data('field-id') != prefix + field_id) {
          return true;
        }
        change_title = true;
        var new_title = posible_values[field_value];
        if ( new_title !== undefined ) {
          field_title = new_title
        }
      });
      if( change_title ){
        $row.find('.xbox-element-label').first().text(field_title);
      }
    });
  };

  xbox_events.show_hide_row = function ($el, field_value, type) {
    var prefix = $el.closest('.xbox').data('prefix');
    var $row_changed = $el.closest('.xbox-row');
    var value = '';
    var operator = '==';

    //Soporte nuevo, verifica en todos los campos
    var $rows = $row_changed.closest('.xbox').find('.xbox-row');
    var $group_item = $row_changed.closest('.xbox-group-item');
    var group_index = -1;
    if ($group_item.length) {
      group_index = $group_item.data('index');
    }

    //Soporte anterior para verificar solo campos dentro del mismo nivel
    //var $rows = $row_changed.siblings('.xbox-row');
    // var $group_item = $row_changed.closest('.xbox-group-item');
    // if ($group_item.length) {
    //   $rows = $group_item.find('.xbox-row');
    // } else {
    //   $rows.each(function (index, el) {
    //     if ($(el).data('field-type') == 'mixed') {
    //       $(el).find('.xbox-row').each(function (i, mixed_row) {
    //         $rows.push($(mixed_row)[0]);
    //       });
    //     }
    //   });
    // }

    $rows.each(function (index, el) {
      var $row = $(el);
      var data_show_hide = $row.data('show-hide');
      if( ! data_show_hide ){
        return true;//continue
      }
      if( group_index >= 0 && ! $row.closest('.xbox-group-item[data-index='+group_index+']').length ){
        return true;//continue
      }

      var show_if = data_show_hide.show_if;
      var hide_if = data_show_hide.hide_if;
      var show = true;
      var hide = false;
      var check_show = true;
      var check_hide = true;

      if (is_empty(show_if) || is_empty(show_if[0])) {
        check_show = false;
      }
      if (is_empty(hide_if) || is_empty(hide_if[0])) {
        check_hide = false;
      }

      var field_id = $row_changed.data('field-id');

      //Si el campo donde se originó el cambio no afecta al campo actual, no hacer nada
      if ($row.is($row_changed) || ( field_id != prefix + show_if[0] && field_id != prefix + hide_if[0] ) ) {
        return true;
      }

      if (check_show) {
        if ($.isArray(show_if[0])) {

        } else {
          if (show_if.length == 2) {
            value = show_if[1];
          } else if (show_if.length == 3) {
            value = show_if[2];
            operator = !is_empty(show_if[1]) ? show_if[1] : operator;
            operator = operator == '=' ? '==' : operator;
          }
          if ($.inArray(operator, ['==', '!=', '>', '>=', '<', '<=']) > -1) {
            show = xbox.compare_values_by_operator(field_value, operator, value);
          } else if ($.inArray(operator, ['in', 'not in']) > -1) {
            if (!is_empty(value) && $.isArray(value)) {
              if( $.isArray(field_value) ){
                if( operator == 'in' ){
                  show = xbox_events.in_array_arrays(field_value, value);
                } else {
                  show = !xbox_events.in_array_arrays(field_value, value);
                }
              } else {
                show = operator == 'in' ? $.inArray(field_value, value) > -1 : $.inArray(field_value, value) == -1;
              }
            }
          }
        }
      }

      if (check_hide) {
        if ($.isArray(hide_if[0])) {

        } else {
          if (hide_if.length == 2) {
            value = hide_if[1];
          } else if (hide_if.length == 3) {
            value = hide_if[2];
            operator = !is_empty(hide_if[1]) ? hide_if[1] : operator;
            operator = operator == '=' ? '==' : operator;
          }
          if ($.inArray(operator, ['==', '!=', '>', '>=', '<', '<=']) > -1) {
            hide = xbox.compare_values_by_operator(field_value, operator, value);
          } else if ($.inArray(operator, ['in', 'not in']) > -1) {
            if (!is_empty(value) && $.isArray(value)) {
              if( $.isArray(field_value) ){
                if( operator == 'in' ){
                  hide = xbox_events.in_array_arrays(field_value, value);
                } else {
                  hide = !xbox_events.in_array_arrays(field_value, value);
                }
              } else {
                hide = operator == 'in' ? $.inArray(field_value, value) > -1 : $.inArray(field_value, value) == -1;
              }
            }
          }
        }
      }

      if (check_show) {
        if (check_hide) {
          if (show) {
            if (hide) {
              xbox_events.hide_row($row);
            } else {
              xbox_events.show_row($row);
            }
          } else {
            xbox_events.hide_row($row);
          }
        } else {
          if (show) {
            xbox_events.show_row($row);
          } else {
            xbox_events.hide_row($row);
          }
        }
      }

      if (check_hide) {
        if (hide) {
          xbox_events.hide_row($row);
        } else if (check_show) {
          if (show) {
            xbox_events.show_row($row);
          } else {
            xbox_events.hide_row($row);
          }
        } else {
          xbox_events.show_row($row);
        }
        // if( check_show ){
        // 	if( hide ){
        // 		xbox_events.hide_row($row);
        // 	} else {
        // 		if( show ){
        // 			xbox_events.show_row($row);
        // 		} else {
        // 			xbox_events.hide_row($row);
        // 		}
        // 	}
        // } else {
        // 	if( hide ){
        // 		xbox_events.hide_row($row);
        // 	} else {
        // 		xbox_events.show_row($row);
        // 	}
        // }
      }
    });
  };

  xbox_events.in_array_arrays = function (arrayValues, array2) {
    if( arrayValues == array2 ){
      return true;
    }
    for( var i = 0; i < arrayValues.length; i++ ){
      if( $.inArray( arrayValues[i], array2 ) > -1 ){
        return true
      }
    }
    return false;
  };

  xbox_events.show_row = function ($row) {
    var data_show_hide = $row.data('show-hide');
    var delay = parseInt(data_show_hide.delay);
    if (data_show_hide.effect == 'slide') {
      $row.slideDown(delay, function () {
        if ($row.hasClass('xbox-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    } else if (data_show_hide.effect == 'fade') {
      $row.fadeIn(delay, function () {
        if ($row.hasClass('xbox-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    } else {
      $row.show();
      if ($row.hasClass('xbox-row-mixed')) {
        $row.css('display', 'inline-block');
      }
    }
  };
  xbox_events.hide_row = function ($row) {
    var data_show_hide = $row.data('show-hide');
    var delay = parseInt(data_show_hide.delay);
    if (data_show_hide.effect == 'slide') {
      $row.slideUp(delay, function () {
      });
    } else if (data_show_hide.effect == 'fade') {
      $row.fadeOut(delay, function () {
      });
    } else {
      $row.hide();
    }
  };

  function is_empty(value) {
    return (value === undefined || value === false || $.trim(value).length === 0);
  }

  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg, msg2);
  }

  //Document Ready
  $(function () {
    xbox = window.XBOX;
    xbox_events.init();
  });

  return xbox_events;

})(window, document, jQuery);


//Events when you change some value of any field.
/*jQuery(document).ready(function($) {
	$('.xbox-type-colorpicker .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'colorpicker changed:' );
		console.log( value );
	});

	$('.xbox-code-editor').on('xbox_changed_value', function( event, value ){
		console.log( 'code_editor changed:' );
		console.log( value );
	});

	$('.xbox-type-file .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'file changed:' );
		console.log( value );
	});

	$('.xbox-type-image_selector .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'image_selector changed:' );
		console.log( value );
	});

	$('.xbox-type-number .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'number changed:' );
		console.log( value );
	});

	$('.xbox-type-oembed .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'oembed changed:' );
		console.log( value );
	});

	$('.xbox-type-radio .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'radio changed:' );
		console.log( value );
	});

	$('.xbox-type-checkbox .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'checkbox changed:' );
		console.log( value );
	});

	$('.xbox-type-switcher .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'switcher:' );
		console.log( value );
	});

	$('.xbox-type-select .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'select:' );
		console.log( value );
	});

	$('.xbox-type-text .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'Texto:' );
		console.log( value );
	});

	$('.xbox-type-textarea .xbox-element').on('xbox_changed_value', function( event, value ){
		console.log( 'textarea:' );
		console.log( value );
	});

	$('.xbox-type-wp_editor .wp-editor-area').on('xbox_changed_value', function( event, value ){
		console.log( 'wp_editor:' );
		console.log( value );
	});

	$xbox.on('xbox_on_init_wp_editor', function (e, wp_editor, args) {
    //After Init
    console.log('xbox_on_init_wp_editor', wp_editor);
    wp_editor.on('click', function (e) {
      console.log('Editor was clicked');
    });
    //Enable "Right to Left" button
    if (wp_editor.controlManager.buttons.rtl) {//Check if "Right to Left" exists
      wp_editor.controlManager.buttons.rtl.$el.trigger('click');
    }
  });

  $xbox.on('xbox_on_setup_wp_editor', function (e, wp_editor) {
    //Before Init
    console.log('xbox_on_setup_wp_editor', wp_editor);

    //Add your buttons
    wp_editor.settings.toolbar3 = 'fontselect | media, image';
  });

});*/


