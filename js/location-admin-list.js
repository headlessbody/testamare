/**
 * JavaScript for handling featured image functionality in the location admin list view
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initLocationThumbnailButtons();
    });

    /**
     * Initialize the "Add Image" buttons for locations without featured images
     */
    function initLocationThumbnailButtons() {
        // Handle click on "Add Image" buttons
        $('.scb-add-thumbnail').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var postId = button.data('post-id');
            
            // Open the WordPress media uploader
            var frame = wp.media({
                title: scbLocationAdminListVars.selectImageText,
                button: {
                    text: scbLocationAdminListVars.useThisImageText
                },
                multiple: false
            });
            
            // When an image is selected
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                
                // Send AJAX request to update the featured image
                $.ajax({
                    url: scbLocationAdminListVars.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'scb_add_featured_image',
                        post_id: postId,
                        attachment_id: attachment.id,
                        nonce: scbLocationAdminListVars.nonce
                    },
                    beforeSend: function() {
                        // Show loading state
                        button.text('Loading...').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Replace the button with the image
                            button.parent().html(response.data.image);
                        } else {
                            // Show error message
                            alert(response.data || 'Error updating the image');
                            button.text(scbLocationAdminListVars.addImageText).prop('disabled', false);
                        }
                    },
                    error: function() {
                        // Show error message
                        alert('Connection error while updating the image');
                        button.text(scbLocationAdminListVars.addImageText).prop('disabled', false);
                    }
                });
            });
            
            // Open the media uploader
            frame.open();
        });
    }

})(jQuery);