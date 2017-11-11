jQuery.noConflict();
jQuery(document).ready(function($) {
  var body = $('body');
  /**
   * Item binds
   */
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
    e.preventDefault();
    var option = $('#bmabItemBulkOption').children(':selected').attr('id');
    var selectedIds = $('.bmabItemsRow input:checked').map(function(i, el) {
      return el.name;
    }).get();

    if (option === 'delete') {
      bmabMultiDeleteItems(selectedIds);
    }
  });
});

/**
 * Items
 */
function bmabAddItem() {
  var name = jQuery('#newItemName').val();
  var price = jQuery('#newItemPrice').val();
  var groups = jQuery('#newItemGroups').val();

  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'addItem',
        name: name,
        price: price,
    groups: groups,
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
    jQuery('#editItemName').val(data.item.name);
    jQuery('#editItemPrice').val(data.item.price);
    jQuery('#editItemId').val(data.item.id);
    var editItemGroups = jQuery('#editItemGroups');
    editItemGroups.empty();
    for (var i = 0; i < data.groups.length; i++) {
      var group = data.groups[i];
      editItemGroups.append('<option id=\'' + group.id + '\' value=\'' +
          group.id +
          '\'>' + group.name + '</option>');
    }
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