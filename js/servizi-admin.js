/**
 * SCB Servizi Admin JavaScript
 * 
 * Handles interactive elements on the admin settings page
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Initialize color pickers
        $('.scb-color-picker').wpColorPicker();

        // Handle tabs
        function handleTabs() {
            // Get current tab from URL
            var currentTab = window.location.search.match(/tab=([^&]*)/);
            currentTab = currentTab ? currentTab[1] : 'general';
            
            // Show the current tab content
            $('.scb-tab-content').hide();
            $('#scb-tab-' + currentTab).show();
            
            // Set the active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $('.nav-tab[href*="tab=' + currentTab + '"]').addClass('nav-tab-active');
        }

        // Handle range inputs with live preview
        $('.scb-range-input').each(function() {
            var $range = $(this);
            var $value = $range.siblings('.scb-range-value');
            var $preview = $range.siblings('.scb-range-preview');
            var unit = $range.data('unit') || 'px';
            
            // Update value display and preview on input
            $range.on('input', function() {
                var val = $range.val();
                $value.text(val + unit);
                
                // Update preview if applicable
                if ($preview.length) {
                    var property = $range.data('property');
                    if (property) {
                        $preview.css(property, val + unit);
                    }
                }
            });
            
            // Trigger initial update
            $range.trigger('input');
        });

        // Handle reset buttons
        $('.scb-reset-section').on('click', function(e) {
            e.preventDefault();
            
            if (confirm('Sei sicuro di voler ripristinare le impostazioni predefinite per questa sezione?')) {
                var section = $(this).data('section');
                
                // Reset all inputs in the section
                $('#scb-tab-' + section + ' input').each(function() {
                    var $input = $(this);
                    var defaultVal = $input.data('default');
                    
                    if (typeof defaultVal !== 'undefined') {
                        // Handle different input types
                        if ($input.hasClass('wp-color-picker')) {
                            $input.wpColorPicker('color', defaultVal);
                        } else if ($input.attr('type') === 'range') {
                            $input.val(defaultVal).trigger('input');
                        } else {
                            $input.val(defaultVal);
                        }
                    }
                });
                
                // Reset all selects in the section
                $('#scb-tab-' + section + ' select').each(function() {
                    var $select = $(this);
                    var defaultVal = $select.data('default');
                    
                    if (typeof defaultVal !== 'undefined') {
                        $select.val(defaultVal);
                    }
                });
            }
        });

        // Initialize tabs
        handleTabs();
    });

})(jQuery);