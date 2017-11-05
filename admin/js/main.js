jQuery.noConflict();
jQuery(document).ready(function($) {
  var body = $('body');
  body.on('click', '.bmabPage', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabPage(id);
  });
  body.on('click', '.bmabAction', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabAction(id);
  });
  body.on('click', '.bmabEditWidget', function(e) {
    e.preventDefault();
    var action = 'bmabEditWidget';
    var id = $(this).attr('id');
    bmabPage(action);
    bmabLoadWidget(action, id);
  });
  body.on('click', '.bmabDefaultWidget', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabSetDefaultWidget(id);
  });
  body.on('click', '.bmabDeleteWidget', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabDeleteWidget(id);
  });
  body.on('click', '#bmabWidgetBulk', function(e) {
    e.preventDefault();
    //Todo Sean: fix, better jQuery usage
    var option = $('#bmabWidgetBulkOption').children(':selected').attr('id');
    var selectedIds = $('.bmabWidgetsRow input:checked').map(function(i, el) {
      return el.name;
    }).get();
    if (option === 'delete') {
      bmabMultiDeleteWidgets(selectedIds);
    }
  });
  body.on('click', '.bmabEditItem', function(e) {
    e.preventDefault();
    var action = 'bmabEditItem';
    var id = $(this).attr('id');
    bmabPage(action);
    bmabLoadItem(action, id);
  });
  body.on('click', '.bmabDeleteItem', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabDeleteItem(id);
  });
  body.on('click', '#bmabItemBulk', function(e) {
    console.log('click #bmabItemBulk');
    e.preventDefault();
    var option = $('#bmabItemBulkOption').children(':selected').attr('id');
    console.log('Option = ', option);

    var selectedIds = $('.bmabItemsRow input:checked').map(function(i, el) {
      return el.name;
    }).get();

    console.log('selected ids', selectedIds);
    if (option === 'delete') {
      bmabMultiDeleteItems(selectedIds);
    }
  });
  $('tbody').on('hover', 'tr', function() {
    $('tr').removeClass('active');
    $(this).addClass('active');
  });
  body.on('change', '#bmabTitleDescripID', function(e) {
    var id = $(this).children(':selected').attr('id');
    $('.tdPreview').hide();
    $('#' + id + '.tdPreview').show();
  });
  body.on('change', '#bmabActive', function(e) {
    var id = $(this).children(':selected').attr('id');
    if (id === 'on') {
      $('.bmabWrapper').css('display', 'block');
    }
    if (id === 'off') {
      $('.bmabWrapper').css('display', 'none');
    }
    //$('.tdPreview').hide();
    //$('#'+id+'.tdPreview').show();
  });
});

function bmabAction(action) {
  //Todo sean: remove later
  console.log('bmabAction:: ' + action);

  if (action === 'settings') {
    bmabSaveSettings();
  }
  else if (action === 'bmabAddWidget') {
    bmabAddWidget();
  }
  else if (action === 'bmabEditWidget') {
    bmabEditWidget();
  }
  else if (action === 'bmabAddItem') {
    bmabAddItem();
  }
  else if (action === 'bmabEditItem') {
    bmabEditItem();
  }
}

function bmabContent(action) {
  console.log('bmabContent', action);
  jQuery.post(ajaxurl, {action: 'bmab_contentHandler', run: action},
      function(data) {
        bmabContentHandler(action, data);
      }, 'json'
  );
}

function bmabContentHandler(action, content) {
  if (action === 'bmabViewItems') {
    jQuery('#bmabItemsContent').empty();
    jQuery.each(content, function(index, value) {
      var bmabHtml = '<tr class="bmabItemsRow">' +
          '<th scope="row" class="check-column">' +
          '<input type="checkbox" class="bmabCheckedItems" name="' + value.id +
          '">' +
          '</th>' +
          '<td class="column-name">' +
          value.name +
          '<div class="row-actions visible">' +
          '<span class="deactivate">' +
          '<a href="#" class="bmabDeleteItem" id="' + value.id +
          '">Delete</a> |' +
          '</span>' +
          '<span class="edit">' +
          '<a href="#" class="bmabEditItem" id="' + value.id + '">Edit</a>' +
          '</span>' +
          '</div>' +
          '</td>' +
          '<td class="column-price">' +
          value.price +
          '</td>' +
          '</tr>';
      jQuery(bmabHtml).appendTo('#bmabItemsContent');
    });
  }
  if (action === 'bmabViewWidgets') {
    jQuery('#bmabWidgetsContent').empty();
    jQuery.each(content, function(index, value) {
      console.log(value);
      var bmabHtml = '<tr class="bmabWidgetsRow">' +
          '<th scope="row" class="check-column">' +
          '<input type="checkbox" class="bmabCheckedWidgets" name="' +
          value.id + '">' +
          '</th>' +
          '<td class="column-title">' +
          value.title +
          '<div class="row-actions visible">' +
          '<span class="delete">' +
          '<a href="" class="bmabDeleteWidget" id="' + value.id +
          '">Delete</a> |' +
          '</span>' +
          '<span class="edit" >' +
          '<a href="" class="bmabEditWidget" id="' + value.id + '">Edit</a> |' +
          '</span>';
      if (value.is_default) {
        bmabHtml = bmabHtml +
            '<span class="default currentDefault" >' +
            'Current Default' +
            '</span>';
      }
      else {
        bmabHtml = bmabHtml +
            '<span class="default" >' +
            '<a href="" class="bmabDefaultWidget" id="' + value.id +
            '">Default</a>' +
            '</span>';
      }
      bmabHtml = bmabHtml +
          '</div>' +
          '</td>' +
          '<td class="column-description">' +
          value.description +
          '</td>' +
          '<td class="column-image">' +
          '<img src="' + value.image + '" height="80px">' +
          '</td>' +
          '</tr>';
      jQuery(bmabHtml).appendTo('#bmabWidgetsContent');
    });
  }
  if (action === 'bmabViewPayments') {
    // Todo Sean: add currency to amount
    jQuery('#bmabPaymentsContent').empty();
    jQuery.each(content, function(index, value) {
      var bmabHtml = '<tr>' +
          '<td>' + value.paypal_id + '</td>' +
          '<td>' + BuyMeABeer.currencyPre + value.amount +
          BuyMeABeer.currencyPost + '</td>' +
          '<td>' + value.email + '</td>' +
          '<td>' + value.first_name + ' ' + value.last_name + '</td>' +
          '<td>' + value.time + '</td>';
      if (value.url !== undefined && !!value.url) {
        bmabHtml += '<td><a href="' + value.url + '" target="_blank">' +
            value.url + '</a></td>';
      }
      else {
        bmabHtml += '<td>Unknown URL</td>';
      }
      // bmabHtml += '<td>' + value.title + '</td>' +
      bmabHtml += '</tr>'
      ;
      jQuery(bmabHtml).appendTo('#bmabPaymentsContent');
    });
  }
}

function bmabPage(action) {
  bmabContent(action);
  jQuery('.bmabContent').hide();
  jQuery('.bmabPage').removeClass('current');
  jQuery('.bmabPage#' + action).addClass('current');
  jQuery('.bmabContent#' + action).show();
}

function bmabInit() {
  var id = 'bmabViewMain';
  bmabPage(id);
}

function bmabSaveSettings() {
  var displayMode = jQuery('#bmabDisplayMode').find('option:selected').val();
  var paypalEmail = jQuery('#paypalEmail').val();
  var paypalMode = jQuery('#paypalMode').val();
  var paypalClientId = jQuery('#paypalClientId').val();
  var paypalSecret = jQuery('#paypalSecret').val();
  var currency = jQuery('#bmabCurrency').find('option:selected').val();
  var successPage = jQuery('#bmabSuccessPage').find('option:selected').val();
  var errorPage = jQuery('#bmabErrorPage').find('option:selected').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'saveSettings',
        paypalEmail: paypalEmail,
        paypalMode: paypalMode,
        paypalClientId: paypalClientId,
        paypalSecret: paypalSecret,
        currency: currency,
        displayMode: displayMode,
        successPage: successPage,
        errorPage: errorPage,

      }, function(data) {
        bmabAlertMessage(data.message, data.type);
      }, 'JSON'
  );
}

function bmabAddWidget() {
  var title = jQuery('#newWidgetTitle').val();
  var description = jQuery('#newWidgetDescription').val();
  var image = jQuery('#newWidgetImage').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'addWidget',
        title: title,
        description: description,
        image: image,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewWidgets');
      }, 'JSON'
  );
}

function bmabLoadWidget(action, id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_contentHandler',
        run: action,
        id: id,
      }, function(data) {
      }, 'json'
  ).done(function(data) {
    jQuery('#editWidgetTitle').val(data.title);
    jQuery('#editWidgetDescription').val(data.description);
    jQuery('#editWidgetImage').val(data.image);
    jQuery('#editWidgetId').val(data.id);
  });
}

function bmabEditWidget() {
  var title = jQuery('#editWidgetTitle').val();
  var description = jQuery('#editWidgetDescription').val();
  var image = jQuery('#editWidgetImage').val();
  var id = jQuery('#editWidgetId').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'editWidget',
        id: id,
        title: title,
        description: description,
        image: image,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewWidgets');
      }, 'JSON'
  );
}

function bmabSetDefaultWidget(id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'defaultWidget',
        id: id,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewWidgets');
      }, 'JSON'
  );
}

function bmabMultiDeleteWidgets(ids) {
  for (index = 0; index < ids.length; ++index) {
    id = ids[index];
    jQuery.post(ajaxurl, {
          action: 'bmab_formHandler',
          run: 'deleteWidget',
          id: id,
        }, function(data) {
        }, 'JSON'
    );
  }
  bmabAlertMessage('Widgets deleted!', 'success');
  bmabPage('bmabViewWidgets');
}

function bmabDeleteWidget(id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'deleteWidget',
        id: id,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewWidgets');
      }, 'JSON'
  );
}

function bmabAddItem() {
  var name = jQuery('#newItemName').val();
  var price = jQuery('#newItemPrice').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'addItem',
        name: name,
        price: price,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewItems');
      }, 'JSON'
  );
}

function bmabLoadItem(action, id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_contentHandler',
        run: action,
        id: id,
      }, function(data) {
      }, 'json'
  ).done(function(data) {
    jQuery('#editItemName').val(data.name);
    jQuery('#editItemPrice').val(data.price);
    jQuery('#editItemId').val(data.id);
  });
}

function bmabEditItem() {
  var id = jQuery('#editItemId').val();
  var name = jQuery('#editItemName').val();
  var price = jQuery('#editItemPrice').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'editItem',
        id: id,
        name: name,
        price: price,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewItems');
      }, 'JSON'
  );
}

function bmabMultiDeleteItems(ids) {
  console.log('Deleting ids', ids);
  var index;
  for (index = 0; index < ids.length; ++index) {
    var id = ids[index];
    console.log('Hitting delete item for ', id);
    jQuery.post(ajaxurl, {
          action: 'bmab_formHandler',
          run: 'deleteItem',
          id: id,
        }, function(data) {
        }, 'JSON'
    );
  }
  bmabAlertMessage('Items deleted!', 'success');
  bmabPage('bmabViewItems');
}

function bmabDeleteItem(id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'deleteItem',
        id: id,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewItems');
      }, 'JSON'
  );
}

function bmabAlertMessage(message, type) {
  console.log(message, type);
  jQuery('#alertArea').noty({
    type: type,
    theme: 'relax',
    text: message,
    animation: {
      open: {height: 'toggle'}, // jQuery animate function property object
      close: {height: 'toggle'}, // jQuery animate function property object
      easing: 'swing', // easing
      speed: 500 // opening & closing animation speed
    },
    autoHide: true,
    killer: true,
    timeout: 3000,
  });
}