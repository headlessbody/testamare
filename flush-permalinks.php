<?php
/**
 * Flush Permalinks Script
 * 
 * This script manually flushes the rewrite rules to fix permalink issues with the servizi post type.
 * It re-registers the post types and taxonomies, then flushes the rewrite rules.
 * 
 * Usage: Include this file in the plugin directory and run it once through the browser.
 * After running, you should see a success message and permalinks should work correctly.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    // Define WordPress path
    define('WP_USE_THEMES', false);
    
    // Find the wp-load.php file
    $wp_load_paths = array(
        '../../../wp-load.php',
        '../../../../wp-load.php',
        '../../../../../wp-load.php',
    );
    
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            break;
        }
    }
    
    // If WordPress isn't loaded, show an error
    if (!function_exists('add_action')) {
        die('WordPress not found. This script must be run within a WordPress environment.');
    }
}

// Check if user is logged in and has permission
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

// Make sure the plugin functions are available
if (!function_exists('scb_register_servizi_post_type')) {
    require_once('cpt-servizi.php');
}

// Re-register the post types and taxonomies
scb_register_servizi_post_type();
scb_register_location_post_type();
scb_register_servizi_categoria_taxonomy();
scb_register_location_zona_taxonomy();
scb_register_servizi_tag_taxonomy();

// Flush the rewrite rules
flush_rewrite_rules();

// Output success message
echo '<div style="background-color: #dff0d8; color: #3c763d; padding: 15px; border: 1px solid #d6e9c6; border-radius: 4px; margin: 20px;">';
echo '<h2>Permalinks Updated Successfully!</h2>';
echo '<p>The permalink structure has been refreshed. Service and location pages should now be accessible.</p>';
echo '<p>You can now close this page and test the service links.</p>';
echo '<p><a href="' . admin_url() . '">Return to Dashboard</a></p>';
echo '</div>';