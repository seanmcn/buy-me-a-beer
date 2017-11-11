jQuery.noConflict();
jQuery(document).ready(function($) {
  var body = $('body');

  /**
   * Page / Action binds
   */
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

  /**
   * Misc binds
   */

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
  });
});

function bmabAction(action) {
  //Todo sean: remove later
  console.log('bmabAction:: ' + action);

  if (action === 'settings') {
    bmabSaveSettings();
  }

  else if (action === 'bmabAddGroup') {
    bmabAddGroup();
  }
  else if (action === 'bmabEditGroup') {
    bmabEditGroup();
  }
  else if (action === 'bmabAddItem') {
    bmabAddItem();
  }
  else if (action === 'bmabEditItem') {
    bmabEditItem();
  }
  else if (action === 'bmabAddWidget') {
    bmabAddWidget();
  }
  else if (action === 'bmabEditWidget') {
    bmabEditWidget();
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
  if (action === 'bmabViewGroups') {
    jQuery('#bmabGroupsContent').empty();
    jQuery.each(content, function(index, value) {
      var bmabHtml = '<tr class="bmabGroupsRow">' +
          '<th scope="row" class="check-column">' +
          '<input type="checkbox" class="bmabCheckedGroups" name="' + value.id +
          '">' +
          '</th>' +
          '<td class="column-name">' +
          value.name +
          '<div class="row-actions visible">' +
          '<span class="deactivate">' +
          '<a href="#" class="bmabDeleteGroup" id="' + value.id +
          '">Delete</a> |' +
          '</span>' +
          '<span class="edit">' +
          '<a href="#" class="bmabEditGroup" id="' + value.id + '">Edit</a>' +
          '</span>' +
          '</div>' +
          '</td>' +
          '</tr>';
      jQuery(bmabHtml).appendTo('#bmabGroupsContent');
    });
  }

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
          BuyMeABeer.currencyPre + value.price + BuyMeABeer.currencyPost +
          '</td>' +
          '</tr>';
      jQuery(bmabHtml).appendTo('#bmabItemsContent');
    });
  }

  if (action === 'bmabViewPayments') {
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

  if (action === 'bmabAddItem') {
    var addItemGroups = jQuery('#addItemGroups');
    addItemGroups.empty();
    for (var i = 0; i < content.length; i++) {
      var group = content[i];
      addItemGroups.append('<option id=\'' + group.id + '\' value=\'' +
          group.id +
          '\'>' + group.name + '</option>');
    }
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

/**
 * Settings
 */
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

/**
 * Misc
 */
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