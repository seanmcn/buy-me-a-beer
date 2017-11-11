jQuery.noConflict();
jQuery(document).ready(function($) {
  var body = $('body');

  /**
   * Binds
   */
  body.on('click', '.bmabEditGroup', function(e) {
    e.preventDefault();
    var action = 'bmabEditGroup';
    var id = $(this).attr('id');
    bmabPage(action);
    bmabLoadGroup(action, id);
  });

  body.on('click', '.bmabDeleteGroup', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    bmabDeleteGroup(id);
  });

  body.on('click', '#bmabGroupBulk', function(e) {
    e.preventDefault();
    var option = $('#bmabGroupBulkOption').children(':selected').attr('id');
    var selectedIds = $('.bmabGroupsRow input:checked').map(function(i, el) {
      return el.name;
    }).get();

    console.log('selected ids', selectedIds);
    if (option === 'delete') {
      bmabMultiDeleteGroups(selectedIds);
    }
  });

});

function bmabAddGroup() {
  var name = jQuery('#newGroupName').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'addGroup',
        name: name,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewGroups');
      }, 'JSON'
  );
}

function bmabLoadGroup(action, id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_contentHandler',
        run: action,
        id: id,
      }, function(data) {
      }, 'json'
  ).done(function(data) {
    jQuery('#editGroupName').val(data.name);
    jQuery('#editGroupId').val(data.id);
  });
}

function bmabEditGroup() {
  var name = jQuery('#editGroupName').val();
  var id = jQuery('#editGroupId').val();
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'editGroup',
        id: id,
        name: name,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewGroups');
      }, 'JSON'
  );
}

function bmabMultiDeleteGroups(ids) {
  for (var i = 0; i < ids.length; ++i) {
    var id = ids[i];
    jQuery.post(ajaxurl, {
          action: 'bmab_formHandler',
          run: 'deleteGroup',
          id: id,
        }, function(data) {
        }, 'JSON'
    );
  }
  bmabAlertMessage('Group/s deleted!', 'success');
  bmabPage('bmabViewGroups');
}

function bmabDeleteGroup(id) {
  jQuery.post(ajaxurl, {
        action: 'bmab_formHandler',
        run: 'deleteGroup',
        id: id,
      }, function(data) {
        bmabAlertMessage(data.message, data.type);
        bmabPage('bmabViewGroups');
      }, 'JSON'
  );
}
