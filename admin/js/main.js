jQuery.noConflict();
function tip() {}
jQuery(document).ready(function ($) {

    bmabInit();

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
        var id = 'bmabEditDescription';
        var editId = $(this).attr("id");
        bmabPage(id);
        bmabLoadDescription(id, editId);
    });

    $('tbody').on("hover", "tr", function() {
        $("tr").removeClass("active");
        $(this).addClass("active");
    });

});

function bmabAction(id) {
    console.log("Loading action: "+id);

    if(id == "settings") {
        bmabSaveSettings();
    }
    else if(id == "bmabAddDescription") {
        bmabAddDescription();
    }
    else if(id == "bmabEditDescription") {
        bmabEditDescription();
    }
    else if(id == "bmabAddPQ") {
        bmabAddPQ();
    }
    else if(id == "bambEditPQ") {
        bmabEditPQ();
    }


}
function bmabContent(id) {

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/contentHandler.php', { id: id }, function( data ) {
            console.log("Getting Content for "+id);
            bmabContentHandler(id, data);
        }, "json"
    );
}

function bmabContentHandler(id, content) {
    console.group("ContentHandler for "+id);
    console.log(content);
    console.groupEnd();;

    if(id == 'bmabPQ') {
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

    if(id == 'bmabDescrip') {
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
                                '<a href="" class="bmabEditDescription" id="'+value.id+'">Edit</a>' +
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

function bmabPage(id) {
    console.log("Loading page: "+id);
    bmabContent(id);
    jQuery('.bmabContent').hide();
    jQuery('.bmabPage').removeClass('current');
    jQuery('.bmabPage#'+id).addClass('current');
    jQuery('.bmabContent#'+id).show();
}

function bmabInit() {
    console.log("Initiliazing...");
    var id = "bmabMain";
    bmabPage(id);
}

function bmabSaveSettings() {

    var paypalMode = jQuery('#paypalMode').val();
    var paypalClientId = jQuery('#paypalClientId').val();
    var paypalSecret = jQuery('#paypalSecret').val();
    var currency = jQuery('#bmabCurrency').val();

    console.group("Saving Settings");
    console.log(paypalMode);
    console.log(paypalClientId);
    console.log(paypalSecret);
    console.log(currency);
    console.groupEnd();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "saveSettings", paypalMode : paypalMode, paypalClientId : paypalClientId, paypalSecret : paypalSecret, currency: currency }, function( data ) {
            console.group("Save Settings Post");
            console.log(data);
            bmabAlertMessage("Your settings have been changed!", "success");
            console.groupEnd();;

        }
    );
}

function bmabAddDescription() {
    var title = jQuery('#newDescriptionTitle').val();
    var description = jQuery('#newDescriptionDescription').val();
    var image = jQuery('#newDescriptionImage').val();
    console.group("Adding Description");
    console.log(title);
    console.log(description);
    console.log(image);
    console.groupEnd();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "addDescription", title : title, description : description , image: image}, function( data ) {
            console.group("Save Description Post");
            console.log(data);
            bmabAlertMessage("Your settings have been changed!", "success");
            console.groupEnd();;

        }
    );
}

function bmabLoadDescription(id, editId) {
    jQuery.post( '/wp-content/plugins/buymeabeer/admin/contentHandler.php', { id: id, editId: editId }, function( data ) {}, "json"
    ).done(function(data) {
            jQuery('#editDescriptionTitle').val(data.title);
            jQuery('#editDescriptionDescription').val(data.description);
            jQuery('#bmabEditDescription #descriptionImage').val(data.image);
            jQuery('#editDescriptionId').val(data.id);
        });
}
function bmabEditDescription() {
    var title = jQuery('#title').val();
    var description = jQuery('#description').val();
    var image = jQuery('#image').val();
    var id = jQuery('#id').val();

}
function bmabDeleteDescription() {

}
function bmabAddPQ() {
    var name = jQuery('#newPQName').val();
    var price = jQuery('#newPQPrice').val();
    console.group("Adding Price/Quantity");
    console.log(name);
    console.log(price);
    console.groupEnd();

    jQuery.post( '/wp-content/plugins/buymeabeer/admin/ajax/formHandler.php', { action: "addPQ", name : name, price : price }, function( data ) {
            console.group("Save Price/Quantity Post");
            console.log(data);
            bmabAlertMessage("Your settings have been changed!", "success");
            bmabPage("bmabPQ");
            console.groupEnd();;

        }
    );
}
function bmabLoadPQ() {

}
function bmabEditPQ() {

}
function bmabDeletePQ() {

}
function bmabAlertMessage(message, type) {
    console.log("Has been saved");
}