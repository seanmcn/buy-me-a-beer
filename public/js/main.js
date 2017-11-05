jQuery.noConflict();
jQuery(document).ready(function ($) {
    // var body = ;
  $('body').on('click', '#bmabBuyButton', function(e) {
        e.preventDefault();
    var bmabWidgetId = $('#bmabWidgetId').val();
    var bmabItemId = $('#bmabItemSelect').val();

        jQuery.post(BuyMeABeer.ajaxUrl, {
                action: "bmab_publicFormHandler",
          bmabWidgetId: bmabWidgetId,
          bmabItemId: bmabItemId,
                bmabLocation: window.location.href
            }, function (data) {
                window.location = data;
                console.log(data);
            }, "JSON"
        );

    });
});