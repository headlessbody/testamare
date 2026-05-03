/**
 * Location Admin JavaScript
 * 
 * Handles automatic country detection based on coordinates
 * for the Location custom post type
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Only run on location edit screen
        if ($('body').hasClass('post-type-location')) {
            initCoordinatesHandlers();
        }
    });

    /**
     * Initialize event handlers for coordinate fields
     */
    function initCoordinatesHandlers() {
        const $latitudeField = $('#scb_location_latitude');
        const $longitudeField = $('#scb_location_longitude');
        const $countryField = $('#scb_location_nazione');
        
        // Add debounced event handlers to coordinate fields
        $latitudeField.on('change', function() {
            lookupCountryFromCoordinates();
        });
        
        $longitudeField.on('change', function() {
            lookupCountryFromCoordinates();
        });
        
        // Function to validate coordinates
        function areCoordinatesValid() {
            const latitude = $latitudeField.val().trim();
            const longitude = $longitudeField.val().trim();
            
            // Basic validation for latitude (-90 to 90)
            if (!latitude || isNaN(parseFloat(latitude)) || 
                parseFloat(latitude) < -90 || parseFloat(latitude) > 90) {
                return false;
            }
            
            // Basic validation for longitude (-180 to 180)
            if (!longitude || isNaN(parseFloat(longitude)) || 
                parseFloat(longitude) < -180 || parseFloat(longitude) > 180) {
                return false;
            }
            
            return true;
        }
        
        // Function to lookup country from coordinates
        function lookupCountryFromCoordinates() {
            // Only proceed if coordinates are valid
            if (!areCoordinatesValid()) {
                return;
            }
            
            // Don't lookup if country field already has a value and coordinates haven't changed
            if ($countryField.val().trim() !== '' && 
                $latitudeField.data('last-value') === $latitudeField.val() && 
                $longitudeField.data('last-value') === $longitudeField.val()) {
                return;
            }
            
            // Store current values to prevent unnecessary lookups
            $latitudeField.data('last-value', $latitudeField.val());
            $longitudeField.data('last-value', $longitudeField.val());
            
            // Show loading indicator
            const $loadingIndicator = $('<span class="scb-loading-indicator" style="margin-left: 10px;">⟳</span>');
            $countryField.after($loadingIndicator);
            
            // Make AJAX request to get country
            $.ajax({
                url: scbLocationAdminVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'scb_get_country_from_coordinates',
                    nonce: scbLocationAdminVars.nonce,
                    latitude: $latitudeField.val(),
                    longitude: $longitudeField.val()
                },
                success: function(response) {
                    // Remove loading indicator
                    $('.scb-loading-indicator').remove();
                    
                    if (response.success && response.data) {
                        // Update country field
                        $countryField.val(response.data);
                        
                        // Add success indicator that fades out
                        const $successIndicator = $('<span class="scb-success-indicator" style="color: green; margin-left: 10px;">✓</span>');
                        $countryField.after($successIndicator);
                        setTimeout(function() {
                            $successIndicator.fadeOut(500, function() {
                                $(this).remove();
                            });
                        }, 2000);
                    } else {
                        // Show error message
                        const errorMessage = response.data || 'Error retrieving country';
                        const $errorIndicator = $('<span class="scb-error-indicator" style="color: red; margin-left: 10px;">✗ ' + errorMessage + '</span>');
                        $countryField.after($errorIndicator);
                        setTimeout(function() {
                            $errorIndicator.fadeOut(2000, function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                },
                error: function() {
                    // Remove loading indicator
                    $('.scb-loading-indicator').remove();
                    
                    // Show error message
                    const $errorIndicator = $('<span class="scb-error-indicator" style="color: red; margin-left: 10px;">✗ ' + scbLocationAdminVars.errorText + '</span>');
                    $countryField.after($errorIndicator);
                    setTimeout(function() {
                        $errorIndicator.fadeOut(2000, function() {
                            $(this).remove();
                        });
                    }, 3000);
                }
            });
        }
    }

})(jQuery);