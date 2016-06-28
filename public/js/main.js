jQuery.noConflict();
jQuery(document).ready(function ($) {
    // var body = ;
    $('body').on("click", "#bmabBuy", function (e) {
        e.preventDefault();
        var bmabDescriptionId = $('#bmabDescriptionId').val();
        var bmabOption = $('#bmabOption').val();

        console.log("DescriptionId", bmabDescriptionId);
        console.log("Option", bmabOption);

        jQuery.post(BuyMeABeer.ajaxUrl, {
                action: "bmab_publicFormHandler",
                bmabDescriptionId: bmabDescriptionId,
                bmabOption: bmabOption
            }, function (data) {
                console.log(data);
            }, "JSON"
        );

    });
});