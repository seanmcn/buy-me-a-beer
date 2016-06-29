jQuery.noConflict();
jQuery(document).ready(function ($) {
    // var body = ;
    $('body').on("click", "#bmabBuy", function (e) {
        e.preventDefault();
        var bmabDescriptionId = $('#bmabDescriptionId').val();
        var bmabOption = $('#bmabOption').val();

        jQuery.post(BuyMeABeer.ajaxUrl, {
                action: "bmab_publicFormHandler",
                bmabDescriptionId: bmabDescriptionId,
                bmabOption: bmabOption,
                bmabLocation: window.location.href
            }, function (data) {
                window.location = data;
                console.log(data);
            }, "JSON"
        );

    });
});