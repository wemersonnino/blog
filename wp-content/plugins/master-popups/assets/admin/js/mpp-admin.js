window.AdminMasterPopup = (function (window, document, $) {
  var xbox;
  var app = {
    debug: true,
  };

  //Document Ready
  $(function () {
    xbox = window.XBOX;
    app.init();
  });


  app.init = function () {
    app.$xbox_form = $('.xbox-form');
    app.$post_body_audience = $('body.post-type-mpp_audience #post-body');
    app.$post_body_popup_editor = $('body.post-type-master-popups #post-body');
    app.$wp_list_table_popups =  $('body.post-type-master-popups .wp-list-table');

    app.$wp_list_table_popups.on('click', '.ampp-duplicate-popup', app.duplicate_popup);
    app.$wp_list_table_popups.on('click', '.ampp-change-status', app.change_popup_status);
    $('body.wp-admin').on('click', '.ampp-close-message', app.close_info_message);
    app.rename_wp_status();
    app.manage_popup_templates();

    //Save settings
    app.$xbox_form.on('click', '#xbox-save', function(event){
      var $btn = $(this);
      $btn.find('i').remove();
      $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
    });


  };

  app.rename_wp_status = function () {
    app.$wp_list_table_popups.on('click', '.editinline', function(){
      setTimeout(function(){
        $title = app.$wp_list_table_popups.find('.inline-edit-status .title').first();
        if( $title.length ){
          $title.text('WP ' + $title.text());
        }
      }, 10);
    });
  };

  app.close_info_message = function (event) {
    var selector = $(this).hasClass('ampp-close-row') ? '.xbox-row' : '.ampp-message';
    $(this).closest(selector).fadeOut(200, function(){
      $(this).remove();
    });
  };

  app.message = function (type, icon, header, content, $target) {
    if( $target !== undefined ){
      $target.closest('.xbox-content').find('.ampp-close-message').trigger('click');
    }
    var message_class = 'ampp-message ampp-message-' + type;
    if (icon === true) {
      message_class += ' ampp-icon-message';
    }
    var message = '<div class="' + message_class + '">';
    message += '<i class="xbox-icon xbox-icon-remove ampp-close-message"></i>';
    if (header) {
      message += '<header>' + header + '</header>';
    }
    message += '<p>' + content + '</p>';
    message += '</div>';
    return message;
  };

  app.duplicate_popup = function (event) {
    event.preventDefault();
    var $btn = $(this);
    $btn.removeClass('ampp-duplicate-popup');
    app.ajax({
      data: {
        ajax_nonce: MPP_ADMIN_JS.ajax_nonce,
        action: 'mpp_duplicate_popup',
        popup_id: $btn.data('popup_id'),
      },
      beforeSend: function () {
        $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function(response){
        c(response);
      },
      complete: function(){
        location.reload();
        $btn.find(".mpp-icon-spinner").remove();
      }
    });
  };

  app.change_popup_status = function (event) {
    event.preventDefault();
    var $btn = $(this);
    app.ajax({
      data: {
        ajax_nonce: MPP_ADMIN_JS.ajax_nonce,
        action: 'mpp_change_popup_status',
        popup_id: $btn.data('popup_id'),
      },
      beforeSend: function () {
        $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function(response){
        if( response.success ){
          var $status = $btn.closest('tr').find('.ampp-status');
          $status.text($status.data('text-'+response.new_status));
          $status.alterClass('ampp-status-*', 'ampp-status-' + response.new_status);
        }
      },
      complete: function(){
        $btn.find(".mpp-icon-spinner").remove();
      }
    });
  };

  app.manage_popup_templates = function () {
    var $control = app.$post_body_popup_editor.find('.ampp-control-popup-templates');
    var $wrap = app.$post_body_popup_editor.find('.ampp-wrap-popup-templates');
    var $categories = app.$post_body_popup_editor.find('.ampp-categories-popup-templates');
    var $tags = app.$post_body_popup_editor.find('.ampp-tags-popup-templates');

    $control.on('click', 'ul li', function (event) {
      var $btn = $(this);
      $btn.addClass('ampp-active').siblings().removeClass('ampp-active');
      var filter_category = $categories.length ? $categories.find('.ampp-active').data('filter') : 'all';
      var filter_tag = $tags.length ? $tags.find('.ampp-active').data('filter') : 'all';
      var filter_group = $btn.data('group');
      if(filter_group == 'category' && $tags.length){
        $tags.find('li[data-filter="all"]').trigger('click');
      }

      var $items = $wrap.find('.ampp-item-popup-template').filter(function (index) {
        var data_category = $(this).data('category');
        var data_tags = $(this).data('tags');
        return data_category.indexOf(filter_category) > -1 && data_tags.indexOf(filter_tag) > -1;
      });

      $wrap.fadeTo(150, 0.15);
      $wrap.find('.ampp-item-popup-template').fadeOut(400).removeClass('ampp-scale-1');
      setTimeout(function () {
        $items.fadeIn(350).addClass('ampp-scale-1');
        $wrap.fadeTo(300, 1);
      }, 300);
    });

    $wrap.on('click', '.ampp-item-popup-template', function (event) {
      $(this).addClass('ampp-active').siblings().removeClass('ampp-active');
      var json_url = $(this).data('url');
      $('input[name="mpp_xbox-import-field"]').eq(0).val(json_url);
      if ($('input[name="xbox-import-url"]').length) {
        if( json_url.indexOf('http') === -1 ){
          json_url = 'http:'+json_url;
        }
        $('input[name="xbox-import-url"]').eq(0).val(json_url);
      }

      //Add json string to the textarea
      $.ajax({
        url: json_url,
        dataType: 'json',
        success: function(data) {
          var json = JSON.stringify(data);
          if ( ! app.is_empty(json) ){
            app.$post_body_popup_editor.find("textarea[name='xbox-import-json']").val(json);
          }
        }
      });
    });

    //Botón import dentro de cada item
    $wrap.on('mouseenter', '.ampp-item-popup-template', function (event) {
      $(this).find('.ampp-btn-import-item').remove();
      $(this).append('<div class="ampp-btn-import-item xbox-btn">'+MPP_ADMIN_JS.text.import+' <i class="xbox-icon xbox-icon-download"></i></div>');
    });
    $wrap.on('mouseleave', '.ampp-item-popup-template', function (event) {
      $(this).find('.ampp-btn-import-item').remove();
    });
    app.$post_body_popup_editor.on('click', '.ampp-btn-import-item', function (event) {
      app.$post_body_popup_editor.find('#xbox-import').trigger('click');
    });
  };

  app.isJSON = function(str) {
    if( typeof( str ) !== 'string' ) {
      return false;
    }
    try {
      JSON.parse(str);
      return true;
    } catch (e) {
      return false;
    }
  };

  app.set_focus_end = function ($el) {
    var value = $el.val();
    $el.focus();
    $el.val('');
    $el.val(value);
  };

  app.scroll_to = function ($this, delay, offset, callback) {
    offset = offset || 300;
    delay = delay || 650;
    $('html,body').stop().animate({ scrollTop: Math.abs($this.offset().top - offset) }, delay, 'swing', callback);
    return false;
  };

  app.focus_without_scrolling = function (elem) {
    var x = window.scrollX, y = window.scrollY;
    elem.focus();
    window.scrollTo(x, y);
  };

  app.isjQuery = function (obj) {
    return (obj && (obj instanceof jQuery || obj.constructor.prototype.jquery));
  };

  app.get_unit = function ($target) {
    return $target.closest('.xbox-field').find('input.xbox-unit-number').val();
  };

  app.number_object = function (value) {
    var number = {
      value: value,
      unit: undefined,
    };
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

  app.is_number = function (n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  };

  app.css = {
    number: function (value, unit) {
      unit = unit || '';
      var arr = ['auto', 'initial', 'inherit', 'normal'];
      if ($.inArray(value, arr) > -1) {
        return value;
      }
      value = value.toString().replace(/[^0-9.\-]/g, '');
      if (this.is_number(value)) {
        return value + unit;
      }
      return 1;
    },
    is_number: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },
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

  app.ajax = function (options) {
    var defaults = {
      type: 'post',
      data: {
        ajax_nonce: XBOX_JS.ajax_nonce,
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
    $.ajax({
      url: XBOX_JS.ajax_url,
      type: options.type,
      dataType: options.dataType,
      data: options.data,
      beforeSend: options.beforeSend,
      success: function (response) {
        cc('Ajax Success', response);
        if ($.isFunction(options.success)) {
          options.success.call(this, response);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
      },
      complete: function (jqXHR, textStatus) {
        if ($.isFunction(options.complete)) {
          options.complete.call(this, jqXHR, textStatus);
        }
      }
    });
  };

  app.ajax_example = function () {
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: {
        action: 'mpp_action',
        data: data,
        ajax_nonce: XBOX_JS.ajax_nonce
      },
      beforeSend: function () {
      },
      success: function (response) {
        if (response) {
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
      }
    });
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

  app.isCmdKey = function (e) {
    return !!e.ctrlKey || !!e.metaKey;//Mac support
  };
  app.isShiftKey = function (e) {
    return !!e.shiftKey;
  };

  app.sizeObj = function(obj) {
    var size = 0, key;
    for (key in obj) {
      if (obj.hasOwnProperty(key)) size++;
    }
    return size;
  };

  //Funciones privadas
  function get_class_starts_with($elment, starts_with) {
    return $.grep($elment.attr('class').split(" "), function (v, i) {
      return v.indexOf(starts_with) === 0;
    }).join();
  }

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

//Insert text into textarea with jQuery
jQuery.fn.extend({
  insertTextInCursor: function (myValue) {
    return this.each(function (i) {
      if (document.selection) {
        //For browsers like Internet Explorer
        this.focus();
        var sel = document.selection.createRange();
        sel.text = myValue;
        this.focus();
      }
      else if (this.selectionStart || this.selectionStart == '0') {
        //For browsers like Firefox and Webkit based
        var startPos = this.selectionStart;
        var endPos = this.selectionEnd;
        var scrollTop = this.scrollTop;
        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
        this.focus();
        this.selectionStart = startPos + myValue.length;
        this.selectionEnd = startPos + myValue.length;
        this.scrollTop = scrollTop;
      } else {
        this.value += myValue;
        this.focus();
      }
    });
  }
});


jQuery.fn.getValidationMessages = function (fields) {
  var $ = jQuery;
  var message = "";
  var name = "";
  fields = fields || 'input, textarea';
  this.each(function () {
    $(this).find(fields).each(function (index, el) {
      if (el.checkValidity() === false) {
        name = $("label[for=" + el.id + "]").html() || el.placeholder || el.name || el.id;
        message = message + name + ": " + (this.validationMessage || 'Invalid value.') + "\n";
      }
    });
  });
  return message;
};
