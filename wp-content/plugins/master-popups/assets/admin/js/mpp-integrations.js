;(function (window, document, $) {
  var MPP;
  var xbox;
  var app = {
    debug: false,
  };

  //Document Ready
  $(function () {
    app.init();
  });

  app.init = function () {
    xbox = window.XBOX;
    MPP = window.AdminMasterPopup;
    app.$settings = $('#settings-master-popups');
    app.$tab_integration = app.$settings.find('.tab-content-crm-integrations');
    app.$tab_activation = app.$settings.find('.tab-content-activation');
    app.$services_list = app.$tab_integration.find('.xbox-field-id-services-list');
    app.$services_row = app.$tab_integration.find('.xbox-row-id-integrated-services');
    app.$services_group = app.$services_row.find('.xbox-group-wrap').first();
    app.$services_control = app.$services_row.find('.xbox-group-control').first();

    app.update_services_status();
    app.rename_and_show_fields_to_all_services();

    app.$tab_integration.on('click', '.ampp-integrate-service', app.new_service_integration);
    app.$tab_integration.on('click', '.ampp-logout-account', app.logout_service_account);
    app.$tab_integration.on('click', '.ampp-check-account:not(.btn-disabled)', app.connect_service);
    app.$tab_integration.on('click', '.ampp-get-custom-fields:not(.btn-disabled)', app.get_custom_fields);
    app.$services_row.on('xbox_after_add_group_item', app.after_add_group_item);
    app.$services_row.on('xbox_after_remove_group_item', app.after_remove_group_item);

    //Plugin Activation
    app.update_plugin_activation_status();
    app.$tab_activation.on('click', '#activation-validate-purchase:not(.btn-disabled)', app.update_plugin_status);
    app.$tab_activation.on('click', '#sale-offer-send:not(.btn-disabled)', app.send_email_activation_offer);
    app.$tab_activation.on('focusin', 'input[type="text"]', function (e) {
      $(this).closest('.xbox-field').removeClass('xbox-error');
    });

    //Evento que se ejecuta al cambiar el tipo de autorización
    app.$tab_integration.on('xbox_changed_value', '.xbox-field-id-service-auth-type .xbox-element', function (event, value) {
      app.on_change_auth_type(event, value)
    });

  };

  app.update_plugin_activation_status = function () {
    var $status_info = app.$tab_activation.find('.ampp-activation-status');
    var status = app.$tab_activation.find('.xbox-field-id-activation-status .xbox-element').val();
    if (status == 'on') {
      $status_info.alterClass('xbox-color-red', 'xbox-color-green').text('Plugin Activated');
    } else {
      $status_info.alterClass('xbox-color-green', 'xbox-color-red').text('Not Activated');
    }
  };

  app.update_plugin_status = function (event) {
    var $btn = $(this);
    $btn.addClass('btn-disabled');

    var $section_offer = $('.xbox-section-id-activation-offer');
    $section_offer.hide();
    var $username = app.$tab_activation.find('.xbox-field-id-activation-username .xbox-element');
    //var $api_key = app.$tab_activation.find('.xbox-field-id-activation-api-key .xbox-element');
    var $purchase_code = app.$tab_activation.find('.xbox-field-id-activation-purchase-code .xbox-element');
    var $email = app.$tab_activation.find('.xbox-field-id-activation-email .xbox-element');
    var type = app.$tab_activation.find('.xbox-field-id-activation-type .xbox-element:checked').val();
    var $domain = app.$tab_activation.find('.xbox-field-id-activation-domain .xbox-element');
    var has_error = false;
    if ($.trim($username.val()).length < 2) {
      $username.closest('.xbox-field').addClass('xbox-error');
      has_error = true;
    }
    //Api key is deprecated
    // if ($.trim($api_key.val()).length < 2) {
    //     $api_key.closest('.xbox-field').addClass('xbox-error');
    //     has_error = true;
    // }
    if ($.trim($purchase_code.val()).length < 2) {
      $purchase_code.closest('.xbox-field').addClass('xbox-error');
      has_error = true;
    }
    if (type == 'deactivation' && $.trim($domain.val()).length < 2) {
      $domain.closest('.xbox-field').addClass('xbox-error');
      has_error = true;
    }

    if (has_error) {
      $btn.removeClass('btn-disabled');
      return;
    }

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_update_plugin_status',
      user_name: $username.val(),
      //api_key: $api_key.val(),//Api key is deprecated
      purchase_code: $purchase_code.val(),
      email: $email.val(),
      domain: $domain.val(),
      type: type,
      auth: app.$tab_activation.find('.xbox-field-id-activation-auth .xbox-element').val(),
    };

    var $xbox_content = $btn.closest('.xbox-content');
    var $status = app.$tab_activation.find('.xbox-field-id-activation-status');
    var $status_info = app.$tab_activation.find('.ampp-activation-status');
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
        $xbox_content.find('.ampp-message').remove();
        $xbox_content.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function (response) {
        c('Activation Plugin: response');
        c(response);
        if (!response) {
          return;
        }
        var message = response.message;
        if (response.success) {
          message += ' The changes have already been saved.';
          if (type == 'activation') {
            xbox.set_field_value($status, 'on');
            $status_info.alterClass('xbox-color-red', 'xbox-color-green').text('Plugin Activated');
            app.show_activation_offer($section_offer, data, response);
          } else {
            if (response.local_deactivation === true) {
              xbox.set_field_value($status, 'off');
              $status_info.alterClass('xbox-color-green', 'xbox-color-red').text('Not Activated');
            }
          }
          $xbox_content.append(MPP.message('success', false, '', message));
        } else {
          $xbox_content.append(MPP.message('error', false, '', message));
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
        $xbox_content.append(MPP.message('error', false, '', jqXHR.statusText));
      },
      complete: function (jqXHR, textStatus) {
        $xbox_content.find('.ampp-loader').remove();
        $btn.removeClass('btn-disabled');
      }
    });
  };

  app.show_activation_offer = function ($section_offer, data, response) {
    if( response.debug.response ){
      $section_offer.fadeIn(200);
      $section_offer.data('action', response.debug.response.action);
      MPP.scroll_to($section_offer, 0, 180);
    }
  };

  app.get_coupon_code = function(purchase_code){
    var coupon_code = purchase_code;
    coupon_code = coupon_code.toUpperCase();
    var split = coupon_code.split('-');
    coupon_code = split[split.length-1].split("").reverse().join("").slice(0,2) + split[1].split("").reverse().join("").slice(0,2) + "26" + split[0].split("").reverse().join("").slice(0,2);
    return coupon_code;
  };

  app.send_email_activation_offer = function () {
    var $btn = $(this);
    $btn.addClass('btn-disabled');
    var $username = app.$tab_activation.find('.xbox-field-id-activation-username .xbox-element');
    var $purchase_code = app.$tab_activation.find('.xbox-field-id-activation-purchase-code .xbox-element');
    var $email = app.$tab_activation.find('.xbox-field-id-sale-offer-email .xbox-element');
    var $message = app.$tab_activation.find('.xbox-field-id-sale-offer-message .xbox-element');
    var type = app.$tab_activation.find('.xbox-field-id-activation-type .xbox-element:checked').val();
    var has_error = false;
    if ($.trim($email.val()).length < 2) {
      $email.closest('.xbox-field').addClass('xbox-error');
      has_error = true;
    }
    if (has_error) {
      $btn.removeClass('btn-disabled');
      return;
    }

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_send_email_activation_offer',
      message: $message.val(),
      user_name: $username.val(),
      purchase_code: $purchase_code.val(),
      coupon_code: app.get_coupon_code($purchase_code.val()),
      email: $email.val(),
      type: type,
      auth: app.$tab_activation.find('.xbox-field-id-activation-auth .xbox-element').val(),
      subaction: $('.xbox-section-id-activation-offer').data('action')
    };

    var $xbox_content = $btn.closest('.xbox-content');
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
        $xbox_content.find('.ampp-message').remove();
        $xbox_content.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function (response) {
        c('Activation Offer: response');
        c(response);
        if (!response) {
          return;
        }
        var message = response.message;
        if (response.success) {
          $xbox_content.append(MPP.message('success', false, '', message));
        } else {
          $xbox_content.append(MPP.message('error', false, '', message));
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
        $xbox_content.append(MPP.message('error', false, '', jqXHR.statusText));
      },
      complete: function (jqXHR, textStatus) {
        $xbox_content.find('.ampp-loader').remove();
        $btn.removeClass('btn-disabled');
      }
    });
  };

  app.update_services_status = function () {
    var $items = app.$services_group.find('.xbox-group-item');
    $items.each(function (index, el) {
      var status = $(el).find('.xbox-field-id-service-status .xbox-element').val();
      app.update_status_info($(el).find('.xbox-field-id-service-status-info'), status);
    });
  };

  app.rename_and_show_fields_to_all_services = function () {
    var $items = app.$services_group.find('.xbox-group-item');
    $items.each(function (index, el) {
      app.rename_show_access_data_fields_to_service($(el));
    });
  };

  app.on_change_auth_type = function (event, value) {
    var $select = $(event.currentTarget);
    var $group_item = $select.closest('.xbox-group-item');
    app.rename_show_access_data_fields_to_service($group_item);
  };

  app.rename_show_access_data_fields_to_service = function ($group_item) {
    var type = $group_item.data('type');
    if (!MPP_SERVICES[type]) {
      return;
    }

    var fields = app.integration_fields($group_item);
    var auth_type = fields.auth_type.find('.xbox-element input[type="hidden"]').val();
    var access_data = MPP_SERVICES[type].access_data;
    var help_url = MPP_SERVICES[type].help_url;
    var names_access_data = MPP_SERVICES[type].names_access_data;
    var auth_fields = MPP_SERVICES[type].auth_fields;
    var $desc;

    // console.log('type =============================> ', type);
    // console.log('auth_type', auth_type, );
    // console.log('auth_fields', auth_fields);
    // console.log('access_data', access_data);

    //Muestra Select Tipo de Autorización. Sólo se muestra cuando hay más de un tipo de autorización
    if( auth_fields !== undefined && MPP.sizeObj(auth_fields) > 1 ){
      fields.auth_type.closest('.xbox-row').show();
    }

    var access_data_fields = ['api_version', 'api_key', 'token', 'url', 'email', 'password' ];
    access_data_fields.forEach(function( field_name, index, array){
      app.change_access_name(names_access_data, field_name, fields[field_name]);
      $desc = fields[field_name].closest('.xbox-row').find('.xbox-field-description');
      app.add_help_to_field(help_url, field_name, $desc);
      app.show_access_data_fields(fields, access_data, field_name, auth_type, auth_fields);
    });
  };

  app.show_access_data_fields = function (fields, access_data, key, auth_type, auth_fields) {
    if( auth_fields !== undefined && auth_fields[auth_type] ){
      if( auth_fields[auth_type].indexOf(key) > -1 ){
        fields[key].closest('.xbox-row').show();
      } else {
        fields[key].closest('.xbox-row').hide();
      }
    } else {
      if(access_data[key]){
        fields[key].closest('.xbox-row').show();
      } else {
        fields[key].closest('.xbox-row').hide();
      }
    }
  };

  app.add_help_to_field = function (obj, help_key, $desc) {
    if (obj && obj[help_key]) {
      if (is_valid_url(obj[help_key])) {
        $desc.find('a').attr('href', obj[help_key]);
      } else {
        $desc.find('a').remove();
        $desc.html(obj[help_key]);
      }
    } else {
      $desc.hide();
    }
  };

  app.change_access_name = function (names_access_data, key, $field) {
    if( names_access_data !== undefined && names_access_data[key] ){
      $field.closest('.xbox-row').find('.xbox-element-label').text(names_access_data[key]);
    }
  };

  app.new_service_integration = function (event) {
    app.$services_row.find('>.xbox-label .xbox-custom-add[data-item-type="' + $(this).data('item-type') + '"]').trigger('click');
    $(this).removeClass('ampp-integrate-service xbox-btn-teal');
    var $icon = $(this).find('i').alterClass('xbox-icon-arrow-down', 'xbox-icon-check');
    $(this).html(MPP_ADMIN_JS.text.service.integrated).prepend($icon);
  };

  app.after_add_group_item = function (event, args) {
    app.rename_show_access_data_fields_to_service(args.$group_item);
  };

  app.after_remove_group_item = function (event, index, type) {
    app.remove_service_integration(index, type);
  };

  app.remove_service_integration = function (index, type) {
    var $service = app.$services_list.children('.ampp-service-item[data-item-type="' + type + '"]');
    $service.find('.xbox-btn').addClass('ampp-integrate-service xbox-btn-teal');
    var $icon = $service.find('.xbox-btn i').alterClass('xbox-icon-check', 'xbox-icon-arrow-down');
    $service.find('.xbox-btn').html(MPP_ADMIN_JS.text.service.integrate).prepend($icon);
  };

  app.update_status_info = function ($field, status) {
    var $el = $field.find('.ampp-service-status');
    var fields = app.integration_fields($el);
    if (status == 'on') {
      $el.alterClass('xbox-color-red', 'xbox-color-green').text(MPP_ADMIN_JS.text.service.status_on);
      fields.status_info.find('.ampp-logout-account').fadeIn(250);
      fields.api_key.find('.xbox-element').attr('readonly', '');
      fields.token.find('.xbox-element').attr('readonly', '');
      fields.url.find('.xbox-element').attr('readonly', '');
      fields.email.find('.xbox-element').attr('readonly', '');
      fields.password.find('.xbox-element').attr('readonly', '');
    } else {
      $el.alterClass('xbox-color-green', 'xbox-color-red').text(MPP_ADMIN_JS.text.service.status_off);
      fields.status_info.find('.ampp-logout-account').fadeOut(250);
      fields.api_key.find('.xbox-element').removeAttr('readonly');
      fields.token.find('.xbox-element').removeAttr('readonly');
      fields.url.find('.xbox-element').removeAttr('readonly');
      fields.email.find('.xbox-element').removeAttr('readonly');
      fields.password.find('.xbox-element').removeAttr('readonly');
    }
  };

  app.logout_service_account = function (event) {
    event.preventDefault();
    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.service.disconnect_title,
      content: MPP_ADMIN_JS.text.service.disconnect_content,
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      onConfirm: function () {
        app.disconnect_service(event);
      }
    });
    return false;
  };



  app.make_url_ouath2 = function (type, url, clientKey, clientSecret) {
    var redirect_url = '';
    switch (type){
      case 'mautic':
        redirect_url = MPP_ADMIN_JS.settings_url + '&oauth2='+type+'&url='+encodeURIComponent(url)+'&clientKey='+clientKey+'&clientSecret='+clientSecret;
        break;
      case 'salesforce':
        redirect_url = MPP_ADMIN_JS.settings_url + '&oauth2='+type+'&url=&clientKey='+clientKey+'&clientSecret='+clientSecret;
        break;
      case 'zoho_crm':
        url = url.replace(/https?:\/\//, '');//zoho_crm sólo acepta el dominio
        redirect_url = MPP_ADMIN_JS.settings_url + '&oauth2='+type+'&url='+encodeURIComponent(url)+'&clientKey='+clientKey+'&clientSecret='+clientSecret;
        break;
      case 'constant_contact':
        redirect_url = MPP_ADMIN_JS.settings_url + '&oauth2='+type+'&url=&clientKey='+clientKey+'&clientSecret='+clientSecret;
        break;
      case 'zoho_campaigns':
        redirect_url = MPP_ADMIN_JS.settings_url + '&oauth2='+type+'&url=&clientKey='+clientKey+'&clientSecret='+clientSecret;
        break;
    }
    //url = url.replace(/\/$/, '');
    return redirect_url;
  };

  app.connect_oauth2_service = function (event) {
    var $btn = $(event.currentTarget);
    var fields = app.integration_fields($btn);
    var $xbox_content = $btn.closest('.xbox-content-mixed');
    var type = fields.group_item.data('type');
    var auth_type = app.get_auth_type(event);
    var auth_fields = MPP_SERVICES[type].auth_fields;

    if( auth_type !== 'oauth2' ){
      return;
    }

    var url = fields.url.find('.xbox-element').val();
    var clientKey = fields.api_key.find('.xbox-element').val();
    var clientSecret = fields.token.find('.xbox-element').val();

    var validateFields = false;
    if( auth_fields !== undefined && auth_fields[auth_type] ){
      validateFields = true;
    }

    if( validateFields ){
      var error = false;
      var auth_arr = auth_fields[auth_type];

      if( !url && auth_arr.indexOf('url') > -1 ){
        error = true;
        fields.url.addClass('xbox-error');
      }
      if( !clientKey && auth_arr.indexOf('api_key') > -1 ){
        error = true;
        fields.api_key.addClass('xbox-error');
      }
      if( !clientSecret && auth_arr.indexOf('token') > -1 ){
        error = true;
        fields.token.addClass('xbox-error');
      }

      if( error ){
        $xbox_content.append(MPP.message('error', false, '', 'Please fill in all fields'));
        return;
      }
    }

    var auth_url = app.make_url_ouath2( type, url, clientKey, clientSecret );
    // console.log('go_auth_url type', type);
    // console.log('url', url );
    // console.log('auth_url', auth_url);
    window.location.href = auth_url;
  };

  app.get_auth_type = function (event) {
    var $btn = $(event.currentTarget);
    var fields = app.integration_fields($btn);
    var type = fields.group_item.data('type');
    var auth_type = MPP_SERVICES[type].auth_type;
    return auth_type || fields.auth_type.find('.xbox-element input[type="hidden"]').val();
  };

  app.get_api_version = function (event) {
    var $btn = $(event.currentTarget);
    var fields = app.integration_fields($btn);
    return fields.api_version.find('.xbox-element input[type="hidden"]').val();
  };

  app.connect_service = function (event) {
    var $btn = $(this);
    var fields = app.integration_fields($btn);
    var auth_type = app.get_auth_type(event);
    var api_version = app.get_api_version(event);
    var status = fields.status.find('.xbox-element').val();

    //console.log('auth_type', auth_type);
    //console.log('api_version', api_version);

    if( status === 'off' && auth_type === 'oauth2' ){
      app.connect_oauth2_service(event);
      return;
    }

    var $xbox_content = $btn.closest('.xbox-content-mixed');
    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_connect_service',
      service: fields.group_item.data('type'),
      auth_type: auth_type,
      api_version: api_version,
      api_key: fields.api_key.find('.xbox-element').val(),
      token: fields.token.find('.xbox-element').val(),
      url: fields.url.find('.xbox-element').val(),
      email: fields.email.find('.xbox-element').val(),
      password: fields.password.find('.xbox-element').val(),
    };

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
        $btn.addClass('btn-disabled');
        $xbox_content.find('.ampp-message').remove();
        $xbox_content.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function (response, textStatus) {
        c(response);
        cc('textStatus', textStatus);
        if (response) {
          if (response.success) {
            //c('Connected');
            xbox.set_field_value(fields.status, 'on');
            app.update_status_info(fields.status_info, 'on');
            $xbox_content.append(MPP.message('success', false, '', response.message));
          } else {
            //c('Not Connected');
            xbox.set_field_value(fields.status, 'off');
            app.update_status_info(fields.status_info, 'off');
            $xbox_content.append(MPP.message('error', false, '', response.message));
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
        $xbox_content.append(MPP.message('error', false, '', jqXHR.statusText));
      },
      complete: function (jqXHR, textStatus) {
        $xbox_content.find('.ampp-loader').remove();
        $btn.removeClass('btn-disabled');
      }
    });
  };

  app.disconnect_service = function (event) {
    var $btn = $(event.currentTarget);
    var fields = app.integration_fields($btn);
    var auth_type = app.get_auth_type(event);
    var $xbox_content = $btn.closest('.xbox-content');
    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_disconnect_service',
      service: fields.group_item.data('type'),
      auth_type: auth_type,
    };

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
        $btn.addClass('btn-disabled');
        $xbox_content.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function (response, textStatus) {
        c(response);
        cc('textStatus', textStatus);
        if (response && response.success) {
          xbox.set_field_value(fields.status, 'off');
          app.update_status_info(fields.status_info, 'off');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
        $xbox_content.append(MPP.message('error', false, '', jqXHR.statusText));
      },
      complete: function (jqXHR, textStatus) {
        $xbox_content.find('.ampp-loader').remove();
        $btn.removeClass('btn-disabled');
      }
    });
  };

  app.get_custom_fields = function (event) {
    var $btn = $(this);
    var fields = app.integration_fields($btn);
    var $xbox_content = $btn.closest('.xbox-content-mixed');
    var $textarea = fields.custom_fields.find('textarea.xbox-element');

    if (fields.status.find('.xbox-element').val() == 'off') {
      $xbox_content.find('.ampp-message').remove();
      $xbox_content.append(MPP.message('error', false, '', MPP_ADMIN_JS.text.service.please_connect));
      return false;
    }

    $btn.addClass('btn-disabled');
    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_get_custom_fields_service',
      service: fields.group_item.data('type'),
      auth_type: app.get_auth_type(event),
      api_version: app.get_api_version(event),
      api_key: fields.api_key.find('.xbox-element').val(),
      token: fields.token.find('.xbox-element').val(),
      url: fields.url.find('.xbox-element').val(),
      email: fields.email.find('.xbox-element').val(),
      password: fields.password.find('.xbox-element').val(),
      list_id: fields.list_id.find('.xbox-element').val(),
    };

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
        $xbox_content.find('.ampp-message').remove();
        $xbox_content.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
      },
      success: function (response) {
        c(response);
        if (response) {
          if (response.success) {
            $xbox_content.append(MPP.message('success', false, '', response.message));
            if (response.custom_fields.length >= 1) {
              var value = '';
              $.each(response.custom_fields, function (index, val) {
                value += val + '\n';
              });
              $textarea.val(value.trim());
            } else {
              $textarea.val('');
            }
          } else {
            $xbox_content.append(MPP.message('error', false, '', response.message));
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
        $xbox_content.append(MPP.message('error', false, '', jqXHR.statusText));
      },
      complete: function (jqXHR, textStatus) {
        $xbox_content.find('.ampp-loader').remove();
        $btn.removeClass('btn-disabled');
      }
    });
  };

  app.integration_fields = function ($target) {
    var $group_item;
    if ($target.hasClass('xbox-group-item')) {
      $group_item = $target;
    } else {
      $group_item = $target.closest('.xbox-group-item');
    }
    return {
      group_item: $group_item,
      status: $group_item.find('.xbox-field-id-service-status'),
      status_info: $group_item.find('.xbox-field-id-service-status-info'),
      api_version: $group_item.find('.xbox-field-id-service-api_version'),
      auth_type: $group_item.find('.xbox-field-id-service-auth-type'),
      api_key: $group_item.find('.xbox-field-id-service-api-key'),
      token: $group_item.find('.xbox-field-id-service-token'),
      url: $group_item.find('.xbox-field-id-service-url'),
      email: $group_item.find('.xbox-field-id-service-email'),
      password: $group_item.find('.xbox-field-id-service-password'),
      custom_fields: $group_item.find('.xbox-field-id-services-custom-fields'),
      list_id: $group_item.find('.xbox-field-id-services-list-id'),
    };
  };

  function is_valid_url(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
      '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
    return pattern.test(str);
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
