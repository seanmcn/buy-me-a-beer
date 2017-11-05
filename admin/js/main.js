jQuery.noConflict();
jQuery(document).ready(function ($) {
    var body = $('body');
    body.on("click", ".bmabPage", function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabPage(id);
    });
    body.on("click", ".bmabAction", function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabAction(id);
    });
    body.on("click", ".bmabEditDescription", function (e) {
        e.preventDefault();
        var action = 'bmabEditDescription';
        var id = $(this).attr("id");
        bmabPage(action);
        bmabLoadDescription(action, id);
    });
    body.on('click', '.bmabDefaultDescription', function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabSetDefaultDescription(id);
    });
    body.on("click", ".bmabDeleteDescription", function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabDeleteDescription(id);
    });
    body.on('click', '#bmabDescripBulk', function (e) {
        e.preventDefault();
        //Todo Sean: fix, better jQuery usage
        var option = $('#bmabDescripBulkOption').children(":selected").attr("id");
        var selectedIds = $("#bmabDescriptions input:checked").map(function (i, el) {
            return el.name;
        }).get();
        if (option == "delete") {
            bmabMultiDeleteDescrips(selectedIds);
        }
    });
    body.on("click", ".bmabEditPQ", function (e) {
        e.preventDefault();
        var action = 'bmabEditPQ';
        var id = $(this).attr("id");
        bmabPage(action);
        bmabLoadPQ(action, id);
    });
    body.on("click", ".bmabDeletePQ", function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        bmabDeletePQ(id);
    });
    body.on('click', '#bmabPQBulk', function (e) {
        e.preventDefault();
        var option = $('#bmabPQBulkOption').children(":selected").attr("id");
        var selectedIds = $("#bmabPQ input:checked").map(function (i, el) {
            return el.name;
        }).get();
        if (option == "delete") {
            bmabMultiDeletePQs(selectedIds);
        }
    });
    $('tbody').on("hover", "tr", function () {
        $("tr").removeClass("active");
        $(this).addClass("active");
    });
    body.on("change", "#bmabTitleDescripID", function (e) {
        var id = $(this).children(":selected").attr("id");
        $('.tdPreview').hide();
        $('#' + id + '.tdPreview').show();
    });
    body.on("change", "#bmabActive", function (e) {
        var id = $(this).children(":selected").attr("id");
        if (id == 'on') {
            $('.bmabWrapper').css("display", "block");
        }
        if (id == 'off') {
            $('.bmabWrapper').css("display", "none");
        }
        //$('.tdPreview').hide();
        //$('#'+id+'.tdPreview').show();
    });
});
function bmabAction(action) {
    console.log("bmabAction:: " + action);
    if (action == "settings") {
        bmabSaveSettings();
    }
    else if (action == "bmabAddDescription") {
        bmabAddDescription();
    }
    else if (action == "bmabEditDescription") {
        bmabEditDescription();
    }
    else if (action == "bmabAddPQ") {
        bmabAddPQ();
    }
    else if (action == "bmabEditPQ") {
        bmabEditPQ();
    }
}
function bmabContent(action) {
    jQuery.post(ajaxurl, {action: "bmab_contentHandler", run: action}, function (data) {
            bmabContentHandler(action, data);
        }, "json"
    );
}
function bmabContentHandler(action, content) {
    if (action == 'bmabPQ') {
        jQuery("#bmabPQContent").empty();
        jQuery.each(content, function (index, value) {
            var bmabHtml = '<tr class="bmabPqRow">' +
                '<th scope="row" class="check-column">' +
                '<input type="checkbox" class="bmabCheckedPQs" name="' + value.id + '">' +
                '</th>' +
                '<td class="column-name">' +
                value.name +
                '<div class="row-actions visible">' +
                '<span class="deactivate">' +
                '<a href="#" class="bmabDeletePQ" id="' + value.id + '">Delete</a> |' +
                '</span>' +
                '<span class="edit">' +
                '<a href="#" class="bmabEditPQ" id="' + value.id + '">Edit</a>' +
                '</span>' +
                '</div>' +
                '</td>' +
                '<td class="column-price">' +
                value.price +
                '</td>' +
                '</tr>';
            jQuery(bmabHtml).appendTo("#bmabPQContent");
        });
    }
    if (action == 'bmabDescriptions') {
        jQuery("#bmabDescripContent").empty();
        jQuery.each(content, function (index, value) {
            var bmabHtml = '<tr class="bmabDescripRow">' +
                '<th scope="row" class="check-column">' +
                '<input type="checkbox" class="bmabCheckedDescrips" name="' + value.id + '">' +
                '</th>' +
                '<td class="column-title">' +
                value.title +
                '<div class="row-actions visible">' +
                '<span class="delete">' +
                '<a href="" class="bmabDeleteDescription" id="' + value.id + '">Delete</a> |' +
                '</span>' +
                '<span class="edit" >' +
                '<a href="" class="bmabEditDescription" id="' + value.id + '">Edit</a> |' +
                '</span>';
            if (value.default_option == 1) {
                bmabHtml = bmabHtml +
                    '<span class="default currentDefault" >' +
                    'Current Default' +
                    '</span>';
            }
            else {
                bmabHtml = bmabHtml +
                    '<span class="default" >' +
                    '<a href="" class="bmabDefaultDescription" id="' + value.id + '">Default</a>' +
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
            jQuery(bmabHtml).appendTo("#bmabDescripContent");
        });
    }
    if (action == 'bmabPayments') {
        // Todo Sean: add currency to amount
        jQuery("#bmabPaymentsContent").empty();
        jQuery.each(content, function (index, value) {
            var bmabHtml = '<tr>' +
                '<td>' + value.paypal_id + '</td>' +
                '<td>' + BuyMeABeer.currencyPre + value.amount + BuyMeABeer.currencyPost + '</td>' +
                '<td>' + value.email + '</td>' +
                '<td>' + value.first_name + ' ' + value.last_name + '</td>' +
                '<td>' + value.time + '</td>';
            if(value.url !== undefined && !!value.url) {
                bmabHtml += '<td><a href="' + value.url + '" target="_blank">' + value.url + '</a></td>';
            } else {
                bmabHtml += '<td>Unknown URL</td>';
            }
            // bmabHtml += '<td>' + value.title + '</td>' +
                bmabHtml += '</tr>'
            ;
            jQuery(bmabHtml).appendTo("#bmabPaymentsContent");
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
    var id = "bmabMain";
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
            action: "bmab_formHandler",
            run: "saveSettings",
            paypalEmail: paypalEmail,
            paypalMode: paypalMode,
            paypalClientId: paypalClientId,
            paypalSecret: paypalSecret,
            currency: currency,
      displayMode: displayMode,
      successPage: successPage,
      errorPage: errorPage,

        }, function (data) {
            bmabAlertMessage(data.message, data.type);
        }, "JSON"
    );
}
function bmabAddDescription() {
    var title = jQuery('#newDescriptionTitle').val();
    var description = jQuery('#newDescriptionDescription').val();
    var image = jQuery('#descriptionImage').val();
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "addDescription",
            title: title,
            description: description,
            image: image
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }, "JSON"
    );
}
function bmabLoadDescription(action, id) {
    jQuery.post(ajaxurl, {
            action: "bmab_contentHandler",
            run: action,
            id: id
        }, function (data) {
        }, "json"
    ).done(function (data) {
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
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "editDescription",
            id: id,
            title: title,
            description: description,
            image: image
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }, "JSON"
    );
}
function bmabSetDefaultDescription(id) {
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "defaultDescription",
            id: id
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }, "JSON"
    );
}
function bmabMultiDeleteDescrips(ids) {
    for (index = 0; index < ids.length; ++index) {
        id = ids[index];
        jQuery.post(ajaxurl, {
                action: "bmab_formHandler",
                run: "deleteDescription",
                id: id
            }, function (data) {
            }, "JSON"
        );
    }
    bmabAlertMessage("Descriptions deleted!", "success");
    bmabPage("bmabDescriptions");
}
function bmabDeleteDescription(id) {
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "deleteDescription",
            id: id
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabDescriptions");
        }, "JSON"
    );
}
function bmabAddPQ() {
    var name = jQuery('#newPQName').val();
    var price = jQuery('#newPQPrice').val();
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "addPQ",
            name: name,
            price: price
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }, "JSON"
    );
}
function bmabLoadPQ(action, id) {
    jQuery.post(ajaxurl, {
            action: "bmab_contentHandler",
            run: action,
            id: id
        }, function (data) {
        }, "json"
    ).done(function (data) {
        jQuery('#editPQName').val(data.name);
        jQuery('#editPQPrice').val(data.price);
        jQuery('#editPQId').val(data.id);
    });
}
function bmabEditPQ() {
    var id = jQuery("#editPQId").val();
    var name = jQuery('#editPQName').val();
    var price = jQuery('#editPQPrice').val();
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "editPQ",
            id: id,
            name: name,
            price: price
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }, "JSON"
    );
}
function bmabMultiDeletePQs(ids) {
    var index;
    for (index = 0; index < ids.length; ++index) {
        id = ids[index];
        jQuery.post(ajaxurl, {
                action: "bmab_formHandler",
                run: "deletePQ",
                id: id
            }, function (data) {
            }, "JSON"
        );
    }
    bmabAlertMessage("Prices deleted!", "success");
    bmabPage("bmabPQ");
}
function bmabDeletePQ(id) {
    jQuery.post(ajaxurl, {
            action: "bmab_formHandler",
            run: "deletePQ",
            id: id
        }, function (data) {
            bmabAlertMessage(data.message, data.type);
            bmabPage("bmabPQ");
        }, "JSON"
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
        timeout: 3000
    });
}