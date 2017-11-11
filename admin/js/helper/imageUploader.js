jQuery(document).ready(function ($) {
    jQuery('body').on("click", "#wordpressUploader", function (e) {
        e.preventDefault();
      var inputId = $(this).data('input-id');
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
          jQuery('input#' + inputId).val(image_url);
            });
    });
});