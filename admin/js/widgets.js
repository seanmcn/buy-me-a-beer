jQuery.noConflict();
jQuery(document).ready(function($) {
  var body = $('body');
  /**
   * Widget binds
   */
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
    //Todo Sean: fix, better jQuery usage?
    var option = $('#bmabWidgetBulkOption').children(':selected').attr('id');
    var selectedIds = $('.bmabWidgetsRow input:checked').map(function(i, el) {
      return el.name;
    }).get();
    if (option === 'delete') {
      bmabMultiDeleteWidgets(selectedIds);
    }
  });
});

/**
 * Widgets
 */
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
