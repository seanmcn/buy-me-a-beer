jQuery.noConflict();
jQuery(document).ready(function ($) {
    $('body').on("click", ".bmabPage", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabPage(id);
    });

    $('body').on("click", ".bmabAction", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabAction(id);
    });

    $('body').on("click", ".bmabEditDescription", function(e) {
        e.preventDefault();
        var action = 'bmabEditDescription';
        var id = $(this).attr("id");
        bmabPage(action);
        bmabLoadDescription(action, id);
    });

    $('body').on("click", ".bmabDeleteDescription", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabDeleteDescription(id);
    });

    $('body').on("click", ".bmabEditPQ", function(e) {
        e.preventDefault();
        var action = 'bmabEditPQ';
        var id = $(this).attr("id");
        bmabPage(action);
        bmabLoadPQ(action, id);
    });

    $('body').on("click", ".bmabDeletePQ", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabDeletePQ(id);
    });

    $('tbody').on("hover", "tr", function() {
        $("tr").removeClass("active");
        $(this).addClass("active");
    });

    $('body').on("change", "#bmabTitleDescripID", function (e) {
        var id = $(this).children(":selected").attr("id");
        $('.tdPreview').hide();
        $('#'+id+'.tdPreview').show();
    });

});

function bmabAction(action) {
    console.log("Loading action: "+action);

    if(action == "settings") {
        bmabSaveSettings();
    }
    else if(action == "bmabAddDescription") {
        bmabAddDescription();
    }
    else if(action == "bmabEditDescription") {
        bmabEditDescription();
    }
    else if(action == "bmabAddPQ") {
        bmabAddPQ();
    }
    else if(action == "bmabEditPQ") {
        bmabEditPQ();
    }
}

function bmabContent(action) {

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/contentHandler.php', { action: action }, function( data ) {
            bmabContentHandler(action, data);
        }, "json"
    );
}

function bmabContentHandler(action, content) {
    console.group("ContentHandler for "+action);
    console.log(content);
    console.groupEnd();;

    if(action == 'bmabPQ') {
        jQuery("#bmabPQContent").empty();
        jQuery.each(content, function( index, value ) {
            var bmabHtml =  '<tr id="buymeabeer">' +
                                '<th scope="row" class="check-column">' +
                                    '<input type="checkbox" name="checked[]" value="" id="'+value.id+'">' +
                                '</th>' +
                                '<td class="column-name">'+
                                    value.name +
                                    '<div class="row-actions visible">' +
                                        '<span class="deactivate">' +
                                            '<a href="#" class="bmabDeletePQ" id="'+value.id+'">Delete</a> |' +
                                        '</span>' +
                                        '<span class="edit">' +
                                            '<a href="#" class="bmabEditPQ" id="'+value.id+'">Edit</a>' +
                                        '</span>' +
                                    '</div>' +
                                '</td>' +
                                '<td class="column-price">' +
                                    value.price +
                                '</td>' +
                            '</tr>';
            jQuery( bmabHtml).appendTo( "#bmabPQContent" );
        });

    }

    if(action == 'bmabDescriptions') {
        jQuery("#bmabDescripContent").empty();
        jQuery.each(content, function( index, value ) {

            var bmabHtml =  '<tr id="buymeabeer">' +
                            '<th scope="row" class="check-column">' +
                            '<input type="checkbox" name="checked[]" value="" id="'+value.id+'">' +
                            '</th>' +
                            '<td class="column-title">'+
                            value.title +
                            '<div class="row-actions visible">' +
                                '<span class="delete">' +
                                '<a href="" class="bmabDeleteDescription" id="'+value.id+'">Delete</a> |' +
                                '</span>' +
                                '<span class="edit" >' +
                                '<a href="" class="bmabEditDescription" id="'+value.id+'">Edit</a> |' +
                                '</span>' +
                                '<span class="default" >' +
                                '<a href="" class="bmabDefaultDescription" id="'+value.id+'">Default</a>' +
                                '</span>' +
                            '</div>' +
                            '</td>' +
                            '<td class="column-description">' +
                            value.description +
                            '</td>' +
                            '<td class="column-image">' +
                            value.image +
                            '</td>' +
                            '</tr>';
            jQuery( bmabHtml).appendTo( "#bmabDescripContent" );
        });
    }
}

function bmabPage(action) {
    console.log("Loading page: "+action);
    bmabContent(action);
    jQuery('.bmabContent').hide();
    jQuery('.bmabPage').removeClass('current');
    jQuery('.bmabPage#'+action).addClass('current');
    jQuery('.bmabContent#'+action).show();
}

function bmabInit() {
    console.log("Initialising...");
    var id = "bmabMain";
    bmabPage(id);
}

function bmabSaveSettings() {

    var paypalEmail = jQuery('#paypalEmail').val();
    var paypalMode = jQuery('#paypalMode').val();
    var paypalClientId = jQuery('#paypalClientId').val();
    var paypalSecret = jQuery('#paypalSecret').val();
    var currency = jQuery('#bmabCurrency').find('option:selected').attr('id');;

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "saveSettings", paypalEmail: paypalEmail, paypalMode : paypalMode, paypalClientId : paypalClientId, paypalSecret : paypalSecret, currency: currency }, function( data ) {
            bmabAlertMessage(data.message, data.type);
        }
    );
}

function bmabAddDescription() {
    var title = jQuery('#newDescriptionTitle').val();
    var description = jQuery('#newDescriptionDescription').val();
    var image = jQuery('#newDescriptionImage').val();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "addDescription", title : title, description : description , image: image}, function( data ) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }
    );
}

function bmabLoadDescription(action, id) {
    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/contentHandler.php', { action: action, id: id }, function( data ) {}, "json"
    ).done(function(data) {
            jQuery('#editDescriptionTitle').val(data.title);
            jQuery('#editDescriptionDescription').val(data.description);
            jQuery('#bmabEditDescription #descriptionImage').val(data.image);
            jQuery('#editDescriptionId').val(data.id);
        });
}

function bmabEditDescription() {
    var title = jQuery('#editDescriptionTitle').val();
    var description = jQuery('#editDescriptionDescription').val();
    var image = jQuery('#bmabEditDescription #descriptionImage').val();
    var id = jQuery('#editDescriptionId').val();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "editDescription", id: id, title : title, description : description , image: image}, function( data ) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }
    );
}

function bmabDeleteDescription(id) {
    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "deleteDescription", id: id }, function( data ) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }
    );
}

function bmabAddPQ() {
    var name = jQuery('#newPQName').val();
    var price = jQuery('#newPQPrice').val();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "addPQ", name : name, price : price }, function( data ) {
           bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }
    );
}

function bmabLoadPQ(action, id) {
    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/contentHandler.php', { action: action, id: id }, function( data ) {}, "json"
    ).done(function(data) {
            jQuery('#editPQName').val(data.name);
            jQuery('#editPQPrice').val(data.price);
            jQuery('#editPQId').val(data.id);
        });
}

function bmabEditPQ() {
    var id = jQuery("#editPQId").val();
    var name = jQuery('#editPQName').val();
    var price = jQuery('#editPQPrice').val();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "editPQ", id : id, name : name, price : price }, function( data ) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }
    );
}

function bmabDeletePQ(id) {

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "deletePQ", id : id }, function( data ) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }
    );
}

function bmabAlertMessage(message, type) {
    console.group("Alert Message");
    console.log(message);
    console.log(type);
    console.groupEnd();
}