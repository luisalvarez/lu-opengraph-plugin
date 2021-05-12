jQuery(function($){
    $('body').on('click', '.lu_upload_image_button', function(e){
        e.preventDefault();
  
        var button = $(this),
        aw_uploader = wp.media({
            title: 'Custom image',
            library : {
                type : 'image'
            },
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = aw_uploader.state().get('selection').first().toJSON();
            button.next('input').val(attachment.url);
        })
        .open();
    });
});