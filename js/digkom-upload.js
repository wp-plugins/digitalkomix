jQuery(document).ready(function($) {
    $('#upload_grid_image_button').click(function() {
        tb_show('Upload Image', 'media-upload.php?referer=digkom-setting-admin&type=image&TB_iframe=true&post_id=0', false);
        return false;
    });
    
});


window.send_to_editor = function(html) {
    var image_url = $('img',html).attr('src');
    $('#url').val(image_url);
    tb_remove();
}