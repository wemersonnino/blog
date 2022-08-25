;(function (window, document, $) {
  var xbox;
  var MPP;
  var app = {
    debug: true,
    selected_emails: [],
    selected_rows: [],
  };

  //Document Ready
  $(function () {
    app.init();
  });

  app.init = function () {
    xbox = window.XBOX;
    MPP = window.AdminMasterPopup;
    app.$post_body_audience = $('body.post-type-mpp_audience #post-body');
    app.$post_body_popup_editor = $('body.post-type-master-popups #post-body');
    app.audience_id = app.$post_body_audience.find('.xbox').data('object-id');
    app.$subscriber_table = app.$post_body_audience.find('.mpp-table-subscribers');

    //Save post
    app.$post_body_audience.on('click', '#save-popup', app.submit_save_audience_list);

    app.$post_body_audience.on('click', '.ampp-get-lists', app.get_lists_service);
    app.$post_body_audience.on('click', '.ampp-delete-subscriber', app.delete_subscriber);
    app.$post_body_audience.on('click', '.ampp-delete-all-subscribers', app.delete_all_subscribers);
    app.$post_body_audience.on('click', '.ampp-checkbox-all-subscribers', app.on_click_checkbox_delete_all_subscribers);

    app.$post_body_audience.on('click', '.ampp-get-segments', app.get_newsman_segments);
    app.$post_body_audience.on('mpp_on_select_list', app.on_select_list);
    $(document).on('click', '.ampp-row-list-id', function (event) {
      var list_id = $(this).data('list-id');
      app.$post_body_audience.trigger('mpp_on_select_list', [event, list_id]);
    });

    app.$post_body_audience.on('ifClicked', '.xbox-field-id-mpp_service .xbox-radiochecks input', app.on_click_service );

    app.init_table_subscribers();
  };

  app.init_table_subscribers = function(event){
    app.subscriberTable = app.$subscriber_table.DataTable({
      "dom": "lfrtipB",
      "buttons": [
        {
          extend: 'csv',
          text: 'Export CSV',
          className: 'xbox-btn xbox-btn-teal xbox-btn-small',
        },
        {
          extend: 'excel',
          text: 'Export Excel',
          className: 'xbox-btn xbox-btn-teal xbox-btn-small',
        }
      ],
      'lengthMenu': [[10, 20, 50, 100, 200, 500, -1], [10, 20, 50, 100, 200, 500, "All"]],
      'pageLength': 50,
      "oLanguage": {
        "sLengthMenu": "Display _MENU_ subscribers",
        "sZeroRecords": "No subscribers found",
        "sInfo": "Showing _START_ to _END_ of _TOTAL_ subscribers",
        "sInfoFiltered": " - filtering from _MAX_ subscribers",
        "sInfoEmpty": "No subscribers to show",
      },
      order: [[4, 'desc']],
      "columnDefs": [
        { orderable: false, targets: [5] }
      ]
    });
  };

  app.on_click_service = function(event){
    var $label = app.$post_body_audience.find('.xbox-row-id-mpp_list-id .xbox-element-label');
    var $btn_get_lists = app.$post_body_audience.find('.xbox-row-id-mpp_list-id .ampp-get-lists');

    if( $label.data('original') === undefined ){
      $label.data('original', $label.text());
    }
    $label.text($label.data('original'));

    if( $btn_get_lists.data('original') === undefined ){
      $btn_get_lists.data('original', $btn_get_lists.text());
    }
    $btn_get_lists.text($btn_get_lists.data('original'));

    var service = $(this).val();
    if( service === 'drip' ){
      app.get_drip_accounts(event);
    }
    if( service === 'ontraport' ){
      $label.text('Tag ID');
      $btn_get_lists.text('Get Tags');
    }
  };

  app.on_select_list = function(event, click_event, list_id){
    var fields = app.audience_fields();

    //Close overlay
    $(click_event.target).closest('.xbox-confirm').find('.xbox-confirm-close-btn').trigger('click');

    //Set list ID
    app.$post_body_audience.find('.xbox-row-id-mpp_list-id .xbox-element').val(list_id);

    //Load segments (For Newsman integration)
    if( fields.service.value === 'newsman'){
      app.get_newsman_segments(event);
    }
  };

  app.get_drip_accounts = function (event) {
    var fields = app.audience_fields();
    var accounts = fields.account_id.field.data('accounts');
    var $dropdown = fields.account_id.field.find('.ui.selection.dropdown');
    if( accounts ){
      return;
    }
    MPP.ajax({
      data: {
        action: 'mpp_get_drip_accounts',
        service: 'drip',
      },
      beforeSend: function () {
        $dropdown.addClass('loading');
      },
      success: function(response){
        if( response.success && response.accounts ){
          var accounts = response.accounts;
          fields.account_id.field.data('accounts', accounts);
          var values = [];
          for (var key in accounts) {
            if (accounts.hasOwnProperty(key)) {
              values.push({
                value: key,
                name: key+' - '+accounts[key],
              });
            }
          }
          $dropdown.dropdownXbox( 'setup menu', {values: values});
        } else {
          alert(response.message);
        }
      },
      complete: function(){
        $dropdown.removeClass('loading');
      }
    });
  };

  app.get_newsman_segments = function (event) {
    var fields = app.audience_fields();
    var $dropdown = fields.segment_id.field.find('.ui.selection.dropdown');

    fields.segment_id.field.closest('.xbox-content').find('.ampp-message').remove();

    if(fields.service.value == 'newsman' && fields.list_id.value.trim() == '' ){
      var message = MPP.message('error', false, '', 'Please, first add a List ID');
      fields.segment_id.field.closest('.xbox-content').append($(message).hide().fadeIn());
      return;
    }

    MPP.ajax({
      data: {
        action: 'mpp_get_newsman_segments',
        service: 'newsman',
        list_id: fields.list_id.value
      },
      beforeSend: function () {
        $dropdown.addClass('loading');
      },
      success: function(response){
        if( response.success && response.segments ){
          var segments = response.segments;
          var values = [];
          for (var key in segments) {
            if (segments.hasOwnProperty(key)) {
              values.push({
                value: key,
                name: key+' - '+segments[key],
              });
            }
          }
          $dropdown.dropdownXbox( 'setup menu', {values: values});
        } else {
          var message = MPP.message('error', false, '', response.message);
          fields.segment_id.field.closest('.xbox-content').append($(message).hide().fadeIn());
        }
      },
      complete: function(){
        $dropdown.removeClass('loading');
      }
    });
  };

  app.submit_save_audience_list = function (event) {
    event.preventDefault();
    var $btn = $(this);
    $btn.find('i').remove();
    $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
    var fields = app.audience_fields();

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.saving_changes,
      content: MPP_ADMIN_JS.text.please_wait,
      hide_confirm: true,
      hide_cancel: true,
      hide_close: true,
      wrap_class: 'ampp-transparent-confirm',
    });
    
    if( fields.service.value === 'master_popups' ){
      xbox.set_field_value(fields.list_status.field, 'on');
      setTimeout(function(){
        $('#publish').click();
      }, 2000);
      return;
    }

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_check_list_id_service',
      service: fields.service.value,
      list_id: fields.list_id.value,
      account_id: fields.account_id.value,//for Drip integration
      helper_id: fields.helper_id.value,
    };

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
      },
      success: function (response) {
        if (response && response.connected) {//Sólo cambiar estado cuando se logra conectar con el servicio
          if (response.success) {
            xbox.set_field_value(fields.list_status.field, 'on');
          } else {
            xbox.set_field_value(fields.list_status.field, 'off');
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
        //Save post
        $('#publish').click();
      }
    });
    //Callback, por demora en la conexion a cualquier servicio
    setTimeout(function () {
      $('#publish').click();
    }, 13000);
  };

  app.get_lists_service = function (event) {
    var $btn = $(this);
    var fields = app.audience_fields();
    fields.list_id.field.closest('.xbox-content').find('.ampp-message').remove();

    if(fields.service.value == 'drip' && fields.account_id.value == '' ){
      var message = MPP.message('error', false, '', 'Please, first select Account ID');
      fields.list_id.field.closest('.xbox-content').append($(message).hide().fadeIn());
      return;
    }

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_get_lists_service',
      service: fields.service.value,
      account_id: fields.account_id.value,//for Drip integration
      helper_id: fields.helper_id.value,
    };

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.service.title_popup_get_lists,
      content: {
        data: data,
        dataType: 'json',
        url: XBOX_JS.ajax_url,
        onSuccess: function (response) {
          c(response);
          var $wrap = $('.ampp-wrap-service-lists .xbox-confirm-content');
          if (response && response.success && !$.isEmptyObject(response.lists)) {
            var html = '<table class="ampp-table ampp-center">';
            html += '<tr><th>List ID</th><th>List Name</th></tr>';
            $.each(response.lists, function (list_id, list_name) {
              html += '<tr class="ampp-row-list-id" data-list-id="' + list_id + '"><td>' + list_id + '</td><td>' + list_name + '</td></tr>';
            });
            html += '</table>';
            $wrap.html('<p>' + response.message + '</p>' + html);
          } else {
            $wrap.html('<p>' + response.message + '</p>');
          }
        }
      },
      hide_confirm: true,
      hide_cancel: true,
      wrap_class: 'ampp ampp-wrap-service-lists',
    });
  };

  app.delete_subscriber = function (event) {
    event.preventDefault();
    $.xboxConfirm({
      title: XBOX_JS.text.remove_item_popup.title,
      content: XBOX_JS.text.remove_item_popup.content,
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      onConfirm: function () {
        app._delete_subscriber(event);
      }
    });
    return false;
  };

  app._delete_subscriber = function (event) {
    var $btn = $(event.currentTarget);
    var $tr = $btn.closest('tr');
    var email = $tr.find('td[data-email]').data('email');
    var data = {
      action: 'mpp_delete_subscribers',
      audience_id: app.audience_id,
      emails: [email],
    };

    app.reset_selected_sucribers();

    MPP.ajax({
      data: data,
      beforeSend: function () {
        $btn.find('i').attr('class', '').addClass('mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader xbox-color-dark');
      },
      success: function (response) {
        if (response && response.success) {
          app.subscriberTable.row($tr).remove().draw();
          app.$post_body_audience.find('.ampp-total-subscribers span').text(response.total);
        }
      },
      complete: function (jqXHR, textStatus) {
        $btn.find('i').attr('class', '').addClass('xbox-icon xbox-icon-trash xbox-color-red');
      }
    });
  };

  app.on_click_checkbox_delete_all_subscribers = function(event){
    var $rows = app.$subscriber_table.find('tbody tr');
    var is_checked = $(this).is(':checked');
    var emails = [];
    var $selected_rows = [];
    $rows.each(function(index, element){
      var $td = $(element).find('td[data-email]');
      var $row = $td.closest('tr');
      if( is_checked ){
        $row.addClass('ampp-remove-row');
        var email = $td.data('email');
        if( $td.length && email ){
          emails.push(email);
          $selected_rows.push($row);
        }
      } else {
        $row.removeClass('ampp-remove-row');
      }
    });
    app.selected_emails = emails;
    app.selected_rows = $selected_rows;
  };

  app.delete_all_subscribers = function(event){
    event.preventDefault();
    var $checkbox = app.$subscriber_table.find('.ampp-checkbox-all-subscribers');
    var title = XBOX_JS.text.remove_item_popup.title + ' ' + app.selected_emails.length + ' Subscribers';
    var content = XBOX_JS.text.remove_item_popup.content;
    var hide_cancel = false;
    if( ! $checkbox.is(':checked') ){
      hide_cancel = true;
      title = 'Activate the checkbox';
      content = 'Please select rows';
    } else if( app.selected_emails.length === 0 ){
      hide_cancel = true;
      title = 'No subscribers found';
      content = 'There are no subscribers to delete.';
    }

    $.xboxConfirm({
      title: title,
      content: content,
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      hide_cancel: hide_cancel,
      //hide_confirm: false,
      onConfirm: function () {
        if($checkbox.is(':checked') && app.selected_emails.length > 0 ){
          app._delete_all_subscribers(event);
        }
      }
    });
    return false;
  };

  app._delete_all_subscribers = function (event) {
    var $btn = $(event.currentTarget);
    var data = {
      action: 'mpp_delete_subscribers',
      audience_id: app.audience_id,
      emails: app.selected_emails,
    };
    MPP.ajax({
      data: data,
      beforeSend: function () {
        $btn.find('i').attr('class', '').addClass('mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader xbox-color-dark');
      },
      success: function (response) {
        if (response && response.success) {
          app.selected_rows.forEach(function($tr, index, arr ){
            app.subscriberTable.row($tr).remove().draw();
          });
          app.$post_body_audience.find('.ampp-total-subscribers span').text(response.total);
        }
      },
      complete: function (jqXHR, textStatus) {
        app.reset_selected_sucribers();
        $btn.find('i').attr('class', '').addClass('xbox-icon xbox-icon-trash xbox-color-red');
      }
    });
  };

  app.reset_selected_sucribers = function (event) {
    app.selected_emails = [];
    app.selected_rows = [];
    app.$post_body_audience.find('.ampp-checkbox-all-subscribers').prop('checked', false);
    app.$subscriber_table.find('tbody tr').removeClass('ampp-remove-row');
  };

  app.audience_fields = function () {
    var $service = app.$post_body_audience.find('.xbox-field-id-mpp_service');
    var $helper_id = app.$post_body_audience.find('.xbox-field-id-mpp_helper-id');
    var $account_id = app.$post_body_audience.find('.xbox-field-id-mpp_account-id');
    var $segment_id = app.$post_body_audience.find('.xbox-field-id-mpp_segment-id');
    var $list_id = app.$post_body_audience.find('.xbox-field-id-mpp_list-id');
    var $list_status = app.$post_body_audience.find('.xbox-field-id-mpp_list-status')
    return {
      service: {
        field: $service,
        value: $service.find('.xbox-element:checked').val(),
      },
      helper_id: {
        field: $helper_id,
        value: $helper_id.find('.xbox-element').val()
      },
      account_id: {
        field: $account_id,
        value: $account_id.find('.xbox-element input[type="hidden"]').val()
      },
      list_id: {
        field: $list_id,
        value: $list_id.find('.xbox-element').val(),
      },
      list_status: {
        field: $list_status,
        value: $list_status.find('.xbox-element').val(),
      },
      segment_id: {
        field: $segment_id,
        value: $segment_id.find('.xbox-element input[type="hidden"]').val()
      },
    }
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

})(window, document, jQuery);