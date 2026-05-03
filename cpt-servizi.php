<?php

/*
Plugin Name: CPT Services
Plugin URI: https://solferino3.it
Description: Plugin for creating a Custom Post Type "Services" with categories, geographic zones and tags.
Version: 1.2.5
Author: Stefano Callisto Bassi
Author URI: https://solferino3.it
License: GPL2
Text Domain: cpt-servizi
Domain Path: /languages

Versioning Strategy:
- MAJOR version for incompatible API changes (X.0.0)
- MINOR version for added functionality in a backward compatible manner (0.X.0)
- PATCH version for backward compatible bug fixes (0.0.X)
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SCB_SERVIZI_VERSION', '1.2.5');
define('SCB_SERVIZI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCB_SERVIZI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Register activation hook
register_activation_hook(__FILE__, 'scb_servizi_activate');

/**
 * Function to run when the plugin is activated
 */
function scb_servizi_activate() {
    // Register CPTs and taxonomies first
    scb_register_servizi_post_type();
    scb_register_location_post_type();
    scb_register_servizi_categoria_taxonomy();
    scb_register_location_zona_taxonomy();
    scb_register_servizi_tag_taxonomy();
    
    // Then flush rewrite rules to ensure proper permalink structure
    flush_rewrite_rules();
}

/**
 * Load plugin text domain for translations
 */
function scb_servizi_load_textdomain() {
    load_plugin_textdomain(
        'cpt-servizi',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}
add_action('plugins_loaded', 'scb_servizi_load_textdomain');

// Register Custom Post Type "Services"
function scb_register_servizi_post_type() {
    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', 'cpt-servizi'),
        'singular_name'         => _x('Service', 'Post Type Singular Name', 'cpt-servizi'),
        'menu_name'             => __('Services', 'cpt-servizi'),
        'name_admin_bar'        => __('Service', 'cpt-servizi'),
        'archives'              => __('Services Archive', 'cpt-servizi'),
        'attributes'            => __('Service Attributes', 'cpt-servizi'),
        'parent_item_colon'     => __('Parent Service:', 'cpt-servizi'),
        'all_items'             => __('All Services', 'cpt-servizi'),
        'add_new_item'          => __('Add New Service', 'cpt-servizi'),
        'add_new'               => __('Add New', 'cpt-servizi'),
        'new_item'              => __('New Service', 'cpt-servizi'),
        'edit_item'             => __('Edit Service', 'cpt-servizi'),
        'update_item'           => __('Update Service', 'cpt-servizi'),
        'view_item'             => __('View Service', 'cpt-servizi'),
        'view_items'            => __('View Services', 'cpt-servizi'),
        'search_items'          => __('Search Service', 'cpt-servizi'),
        'not_found'             => __('Not found', 'cpt-servizi'),
        'not_found_in_trash'    => __('Not found in Trash', 'cpt-servizi'),
        'featured_image'        => __('Featured Image', 'cpt-servizi'),
        'set_featured_image'    => __('Set featured image', 'cpt-servizi'),
        'remove_featured_image' => __('Remove featured image', 'cpt-servizi'),
        'use_featured_image'    => __('Use as featured image', 'cpt-servizi'),
        'insert_into_item'      => __('Insert into service', 'cpt-servizi'),
        'uploaded_to_this_item' => __('Uploaded to this service', 'cpt-servizi'),
        'items_list'            => __('Services list', 'cpt-servizi'),
        'items_list_navigation' => __('Services list navigation', 'cpt-servizi'),
        'filter_items_list'     => __('Filter services list', 'cpt-servizi'),
    );
    
    $args = array(
        'label'                 => __('Service', 'cpt-servizi'),
        'description'           => __('Offered services', 'cpt-servizi'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies'            => array('servizi_categoria', 'servizi_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-clipboard',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    
    register_post_type('servizi', $args);
}

// Register Custom Post Type "Location"
function scb_register_location_post_type() {
    $labels = array(
        'name'                  => _x('Locations', 'Post Type General Name', 'cpt-servizi'),
        'singular_name'         => _x('Location', 'Post Type Singular Name', 'cpt-servizi'),
        'menu_name'             => __('Locations', 'cpt-servizi'),
        'name_admin_bar'        => __('Location', 'cpt-servizi'),
        'archives'              => __('Locations Archive', 'cpt-servizi'),
        'attributes'            => __('Location Attributes', 'cpt-servizi'),
        'parent_item_colon'     => __('Parent Location:', 'cpt-servizi'),
        'all_items'             => __('All Locations', 'cpt-servizi'),
        'add_new_item'          => __('Add New Location', 'cpt-servizi'),
        'add_new'               => __('Add New', 'cpt-servizi'),
        'new_item'              => __('New Location', 'cpt-servizi'),
        'edit_item'             => __('Edit Location', 'cpt-servizi'),
        'update_item'           => __('Update Location', 'cpt-servizi'),
        'view_item'             => __('View Location', 'cpt-servizi'),
        'view_items'            => __('View Locations', 'cpt-servizi'),
        'search_items'          => __('Search Location', 'cpt-servizi'),
        'not_found'             => __('Not found', 'cpt-servizi'),
        'not_found_in_trash'    => __('Not found in Trash', 'cpt-servizi'),
        'featured_image'        => __('Featured Image', 'cpt-servizi'),
        'set_featured_image'    => __('Set featured image', 'cpt-servizi'),
        'remove_featured_image' => __('Remove featured image', 'cpt-servizi'),
        'use_featured_image'    => __('Use as featured image', 'cpt-servizi'),
        'insert_into_item'      => __('Insert into location', 'cpt-servizi'),
        'uploaded_to_this_item' => __('Uploaded to this location', 'cpt-servizi'),
        'items_list'            => __('Locations list', 'cpt-servizi'),
        'items_list_navigation' => __('Locations list navigation', 'cpt-servizi'),
        'filter_items_list'     => __('Filter locations list', 'cpt-servizi'),
    );
    
    $args = array(
        'label'                 => __('Location', 'cpt-servizi'),
        'description'           => __('Geographic positions for services', 'cpt-servizi'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-location',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    
    register_post_type('location', $args);
}

// Register Custom Taxonomy for Categories
function scb_register_servizi_categoria_taxonomy() {
    $labels = array(
        'name'                       => _x('Service Categories', 'Taxonomy General Name', 'cpt-servizi'),
        'singular_name'              => _x('Service Category', 'Taxonomy Singular Name', 'cpt-servizi'),
        'menu_name'                  => __('Categories', 'cpt-servizi'),
        'all_items'                  => __('All Categories', 'cpt-servizi'),
        'parent_item'                => __('Parent Category', 'cpt-servizi'),
        'parent_item_colon'          => __('Parent Category:', 'cpt-servizi'),
        'new_item_name'              => __('New Category Name', 'cpt-servizi'),
        'add_new_item'               => __('Add New Category', 'cpt-servizi'),
        'edit_item'                  => __('Edit Category', 'cpt-servizi'),
        'update_item'                => __('Update Category', 'cpt-servizi'),
        'view_item'                  => __('View Category', 'cpt-servizi'),
        'separate_items_with_commas' => __('Separate categories with commas', 'cpt-servizi'),
        'add_or_remove_items'        => __('Add or remove categories', 'cpt-servizi'),
        'choose_from_most_used'      => __('Choose from the most used', 'cpt-servizi'),
        'popular_items'              => __('Popular Categories', 'cpt-servizi'),
        'search_items'               => __('Search Categories', 'cpt-servizi'),
        'not_found'                  => __('Not Found', 'cpt-servizi'),
        'no_terms'                   => __('No categories', 'cpt-servizi'),
        'items_list'                 => __('Categories list', 'cpt-servizi'),
        'items_list_navigation'      => __('Categories list navigation', 'cpt-servizi'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('servizi_categoria', array('servizi'), $args);
}

// Register Custom Taxonomy for Geographic Zones for Location CPT
function scb_register_location_zona_taxonomy() {
    $labels = array(
        'name'                       => _x('Geographic Zones', 'Taxonomy General Name', 'cpt-servizi'),
        'singular_name'              => _x('Geographic Zone', 'Taxonomy Singular Name', 'cpt-servizi'),
        'menu_name'                  => __('Geographic Zones', 'cpt-servizi'),
        'all_items'                  => __('All Zones', 'cpt-servizi'),
        'parent_item'                => __('Parent Zone', 'cpt-servizi'),
        'parent_item_colon'          => __('Parent Zone:', 'cpt-servizi'),
        'new_item_name'              => __('New Zone Name', 'cpt-servizi'),
        'add_new_item'               => __('Add New Zone', 'cpt-servizi'),
        'edit_item'                  => __('Edit Zone', 'cpt-servizi'),
        'update_item'                => __('Update Zone', 'cpt-servizi'),
        'view_item'                  => __('View Zone', 'cpt-servizi'),
        'separate_items_with_commas' => __('Separate zones with commas', 'cpt-servizi'),
        'add_or_remove_items'        => __('Add or remove zones', 'cpt-servizi'),
        'choose_from_most_used'      => __('Choose from the most used', 'cpt-servizi'),
        'popular_items'              => __('Popular Zones', 'cpt-servizi'),
        'search_items'               => __('Search Zones', 'cpt-servizi'),
        'not_found'                  => __('Not Found', 'cpt-servizi'),
        'no_terms'                   => __('No zones', 'cpt-servizi'),
        'items_list'                 => __('Zones list', 'cpt-servizi'),
        'items_list_navigation'      => __('Zones list navigation', 'cpt-servizi'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('location_zona', array('location'), $args);
}

// Register Custom Taxonomy for Tags
function scb_register_servizi_tag_taxonomy() {
    $labels = array(
        'name'                       => _x('Service Tags', 'Taxonomy General Name', 'cpt-servizi'),
        'singular_name'              => _x('Service Tag', 'Taxonomy Singular Name', 'cpt-servizi'),
        'menu_name'                  => __('Tags', 'cpt-servizi'),
        'all_items'                  => __('All Tags', 'cpt-servizi'),
        'parent_item'                => __('Parent Tag', 'cpt-servizi'),
        'parent_item_colon'          => __('Parent Tag:', 'cpt-servizi'),
        'new_item_name'              => __('New Tag Name', 'cpt-servizi'),
        'add_new_item'               => __('Add New Tag', 'cpt-servizi'),
        'edit_item'                  => __('Edit Tag', 'cpt-servizi'),
        'update_item'                => __('Update Tag', 'cpt-servizi'),
        'view_item'                  => __('View Tag', 'cpt-servizi'),
        'separate_items_with_commas' => __('Separate tags with commas', 'cpt-servizi'),
        'add_or_remove_items'        => __('Add or remove tags', 'cpt-servizi'),
        'choose_from_most_used'      => __('Choose from the most used', 'cpt-servizi'),
        'popular_items'              => __('Popular Tags', 'cpt-servizi'),
        'search_items'               => __('Search Tags', 'cpt-servizi'),
        'not_found'                  => __('Not Found', 'cpt-servizi'),
        'no_terms'                   => __('No tags', 'cpt-servizi'),
        'items_list'                 => __('Tags list', 'cpt-servizi'),
        'items_list_navigation'      => __('Tags list navigation', 'cpt-servizi'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('servizi_tag', array('servizi'), $args);
}

// Add meta boxes for coordinates and services selection in Location CPT
function scb_location_add_meta_boxes() {
    add_meta_box(
        'scb_location_coordinates',
        __('Geographic Coordinates', 'cpt-servizi'),
        'scb_location_coordinates_callback',
        'location',
        'normal',
        'high'
    );
    
    add_meta_box(
        'scb_location_services',
        __('Connected Services', 'cpt-servizi'),
        'scb_location_services_callback',
        'location',
        'normal',
        'high'
    );
}

// Location services meta box callback function
function scb_location_services_callback($post) {
    wp_nonce_field('scb_location_services_save_meta', 'scb_location_services_meta_nonce');
    
    $selected_services = get_post_meta($post->ID, '_scb_location_services_ids', true);
    if (!is_array($selected_services)) {
        $selected_services = array();
    }
    
    // Get all services
    $services = get_posts(array(
        'post_type' => 'servizi',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    ));
    
    if (empty($services)) {
        echo '<p>' . __('No services available. Create some services first.', 'cpt-servizi') . '</p>';
        return;
    }
    
    echo '<div style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd;">';
    echo '<p>' . __('Select services available at this location:', 'cpt-servizi') . '</p>';
    
    foreach ($services as $service) {
        $checked = in_array($service->ID, $selected_services) ? 'checked="checked"' : '';
        ?>
        <p>
            <label>
                <input type="checkbox" name="scb_location_services_ids[]" value="<?php echo esc_attr($service->ID); ?>" <?php echo $checked; ?> />
                <?php echo esc_html($service->post_title); ?>
            </label>
        </p>
        <?php
    }
    
    echo '</div>';
}

// Location meta box callback function
function scb_location_coordinates_callback($post) {
    wp_nonce_field('scb_location_save_meta', 'scb_location_meta_nonce');
    
    $latitude = get_post_meta($post->ID, '_scb_location_latitude', true);
    $longitude = get_post_meta($post->ID, '_scb_location_longitude', true);
    $nazione = get_post_meta($post->ID, '_scb_location_nazione', true);
    
    ?>
    <p>
        <label for="scb_location_latitude"><?php _e('Latitude:', 'cpt-servizi'); ?></label>
        <input type="text" id="scb_location_latitude" name="scb_location_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" />
    </p>
    <p>
        <label for="scb_location_longitude"><?php _e('Longitude:', 'cpt-servizi'); ?></label>
        <input type="text" id="scb_location_longitude" name="scb_location_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" />
    </p>
    <p>
        <label for="scb_location_nazione"><?php _e('Country:', 'cpt-servizi'); ?></label>
        <input type="text" id="scb_location_nazione" name="scb_location_nazione" value="<?php echo esc_attr($nazione); ?>" class="regular-text" />
    </p>
    <?php
}

// Save location meta box data
function scb_location_save_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['scb_location_meta_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['scb_location_meta_nonce'], 'scb_location_save_meta')) {
        return;
    }
    
    // Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if ('location' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save latitude
    if (isset($_POST['scb_location_latitude'])) {
        // Sanitize and format latitude as a valid number
        $latitude = sanitize_text_field($_POST['scb_location_latitude']);
        // Replace comma with period for decimal point (common in some locales)
        $latitude = str_replace(',', '.', $latitude);
        // Validate as a numeric value
        if (is_numeric($latitude)) {
            update_post_meta($post_id, '_scb_location_latitude', $latitude);
        }
    }
    
    // Save longitude
    if (isset($_POST['scb_location_longitude'])) {
        // Sanitize and format longitude as a valid number
        $longitude = sanitize_text_field($_POST['scb_location_longitude']);
        // Replace comma with period for decimal point (common in some locales)
        $longitude = str_replace(',', '.', $longitude);
        // Validate as a numeric value
        if (is_numeric($longitude)) {
            update_post_meta($post_id, '_scb_location_longitude', $longitude);
        }
    }
    
    // Save nazione (country)
    if (isset($_POST['scb_location_nazione'])) {
        // Sanitize the country name
        $nazione = sanitize_text_field($_POST['scb_location_nazione']);
        update_post_meta($post_id, '_scb_location_nazione', $nazione);
    }
}

// Save location services meta box data
function scb_location_services_save_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['scb_location_services_meta_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['scb_location_services_meta_nonce'], 'scb_location_services_save_meta')) {
        return;
    }
    
    // Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if ('location' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Get previously selected services to handle removed services
    $previous_services = get_post_meta($post_id, '_scb_location_services_ids', true);
    if (!is_array($previous_services)) {
        $previous_services = array();
    }
    
    // Save selected services
    $services_ids = array();
    if (isset($_POST['scb_location_services_ids']) && is_array($_POST['scb_location_services_ids'])) {
        foreach ($_POST['scb_location_services_ids'] as $service_id) {
            // Ensure we're only saving valid service IDs
            $service_id = absint($service_id);
            if ($service_id > 0 && get_post_type($service_id) === 'servizi') {
                $services_ids[] = $service_id;
            }
        }
    }
    
    // Update the meta field with the array of service IDs
    update_post_meta($post_id, '_scb_location_services_ids', $services_ids);
    
    // Update each service's location list to include this location
    foreach ($services_ids as $service_id) {
        // Get the service's current locations
        $service_locations = get_post_meta($service_id, '_scb_servizi_locations_ids', true);
        if (!is_array($service_locations)) {
            $service_locations = array();
        }
        
        // Add this location to the service's locations if not already included
        if (!in_array($post_id, $service_locations)) {
            $service_locations[] = $post_id;
            update_post_meta($service_id, '_scb_servizi_locations_ids', $service_locations);
        }
    }
    
    // Remove this location from services that were unselected
    $removed_services = array_diff($previous_services, $services_ids);
    foreach ($removed_services as $service_id) {
        $service_locations = get_post_meta($service_id, '_scb_servizi_locations_ids', true);
        if (is_array($service_locations)) {
            // Remove this location from the service's locations
            $service_locations = array_diff($service_locations, array($post_id));
            update_post_meta($service_id, '_scb_servizi_locations_ids', $service_locations);
        }
    }
}

/**
 * Enqueue admin scripts for location edit screen
 * 
 * @param string $hook The current admin page
 */
function scb_enqueue_location_admin_scripts($hook) {
    // Only enqueue on location edit screen
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'location' || !in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }
    
    // Enqueue the location admin script
    wp_enqueue_script(
        'scb-location-admin',
        plugins_url('js/location-admin.js', __FILE__),
        array('jquery'),
        SCB_SERVIZI_VERSION,
        true
    );
    
    // Localize the script with necessary data
    wp_localize_script(
        'scb-location-admin',
        'scbLocationAdminVars',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scb_location_coordinates_nonce'),
            'errorText' => __('Error retrieving country. Please try again.', 'cpt-servizi')
        )
    );
}

/**
 * Check if a string contains non-Latin characters
 * 
 * @param string $string The string to check
 * @return bool True if the string contains non-Latin characters, false otherwise
 */
function scb_contains_non_latin_chars($string) {
    // This regex matches any character that is not in the Latin alphabet
    // Including Latin-based accented characters and common punctuation
    return preg_match('/[^\p{Latin}\p{Common}\s]/u', $string);
}

/**
 * Get country name from coordinates using reverse geocoding
 * 
 * @param float $latitude The latitude coordinate
 * @param float $longitude The longitude coordinate
 * @return string|WP_Error Country name or error
 */
function scb_get_country_from_coordinates($latitude, $longitude) {
    // Validate coordinates
    if (empty($latitude) || empty($longitude) || !is_numeric($latitude) || !is_numeric($longitude)) {
        return new WP_Error('invalid_coordinates', __('Invalid coordinates provided', 'cpt-servizi'));
    }

    // Check if we have cached result for these coordinates
    $transient_key = 'scb_country_' . md5($latitude . ',' . $longitude);
    $cached_result = get_transient($transient_key);
    if ($cached_result !== false) {
        return $cached_result;
    }

    // Build common request params and headers according to Nominatim usage policy
    $admin_email = get_option('admin_email');
    $site_url = home_url();
    $ua = sprintf('%s; %s; contact: %s', get_bloginfo('name'), $site_url, $admin_email);
    $accept_lang = str_replace('_', '-', get_locale());

    $base_args = array(
        'format' => 'jsonv2',
        'lat' => $latitude,
        'lon' => $longitude,
        'zoom' => 10,
        'addressdetails' => 1,
    );
    if (!empty($admin_email)) {
        $base_args['email'] = $admin_email;
    }

    $headers = array(
        'Accept' => 'application/json',
        'Accept-Language' => $accept_lang,
        'User-Agent' => $ua,
        'Referer' => $site_url,
    );

    // First request
    $api_url = add_query_arg($base_args, 'https://nominatim.openstreetmap.org/reverse');
    $response = wp_remote_get($api_url, array(
        'timeout' => 15,
        'headers' => $headers,
    ));

    // Helper to parse response safely
    $parse = function($resp) {
        if (is_wp_error($resp)) {
            return $resp;
        }
        $code = wp_remote_retrieve_response_code($resp);
        if ($code !== 200) {
            return new WP_Error('http_error', sprintf(__('Geocoding service returned HTTP %d', 'cpt-servizi'), intval($code)));
        }
        $body = wp_remote_retrieve_body($resp);
        $data = json_decode($body, true);
        if (empty($data) || !is_array($data)) {
            return new WP_Error('invalid_response', __('Invalid response from geocoding service', 'cpt-servizi'));
        }
        return $data;
    };

    $data = $parse($response);

    // Retry once with forced English if address is missing or previous error
    if (is_wp_error($data) || !isset($data['address'])) {
        $args_en = $base_args; unset($args_en['accept-language']);
        $api_url_en = add_query_arg($args_en, 'https://nominatim.openstreetmap.org/reverse');
        $headers_en = $headers; $headers_en['Accept-Language'] = 'en';
        $response_en = wp_remote_get($api_url_en, array(
            'timeout' => 15,
            'headers' => $headers_en,
        ));
        $data_en = $parse($response_en);
        if (!is_wp_error($data_en)) {
            $data = $data_en;
        }
    }

    if (is_wp_error($data) || !isset($data['address'])) {
        return new WP_Error('invalid_response', __('Invalid response from geocoding service', 'cpt-servizi'));
    }

    // Extract country from address data
    $country = isset($data['address']['country']) ? $data['address']['country'] : '';
    if (empty($country)) {
        return new WP_Error('country_not_found', __('Country not found for these coordinates', 'cpt-servizi'));
    }

    // If non-Latin characters, include English name too when available
    if (scb_contains_non_latin_chars($country)) {
        $headers_en = $headers; $headers_en['Accept-Language'] = 'en';
        $response_en = wp_remote_get($api_url, array(
            'timeout' => 15,
            'headers' => $headers_en,
        ));
        $data_en = $parse($response_en);
        if (!is_wp_error($data_en) && isset($data_en['address']['country'])) {
            $country_en = $data_en['address']['country'];
            $country = $country . ' (' . $country_en . ')';
        }
    }

    // Cache result for 30 days (coordinates don't change countries often)
    set_transient($transient_key, $country, 30 * DAY_IN_SECONDS);
    return $country;
}

/**
 * AJAX handler for getting country from coordinates
 */
function scb_ajax_get_country_from_coordinates() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'scb_location_coordinates_nonce')) {
        wp_send_json_error(__('Security check failed', 'cpt-servizi'));
        wp_die();
    }
    
    // Get and validate coordinates
    $latitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : '';
    $longitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : '';
    
    // Replace comma with period for decimal point (common in some locales)
    $latitude = str_replace(',', '.', $latitude);
    $longitude = str_replace(',', '.', $longitude);
    
    // Get country from coordinates
    $country = scb_get_country_from_coordinates($latitude, $longitude);
    
    // Check for errors
    if (is_wp_error($country)) {
        wp_send_json_error($country->get_error_message());
        wp_die();
    }
    
    // Return success with country name
    wp_send_json_success($country);
    wp_die();
}

/**
 * Get map localization variables
 */
function scb_get_map_localization_vars() {
    $styling_options = get_option('scb_servizi_styling', array());
    $disable_cache = isset($styling_options['map_disable_geolocation_cache']) && $styling_options['map_disable_geolocation_cache'] === '1';
    $show_service_filter = get_option('scb_location_map_show_service_filter', '1');
    $closest_highlight_mode = get_option('scb_location_closest_highlight_mode', 'pulse');
    $closest_highlight_color = get_option('scb_location_closest_highlight_color', '#e74c3c');
    $selected_highlight_color = get_option('scb_location_selected_highlight_color', '#1d9bf0');
    $closest_zoom_level = absint(get_option('scb_location_closest_zoom_level', 7));

    if (!in_array($closest_highlight_mode, array('pin', 'ring', 'pulse'), true)) {
        $closest_highlight_mode = 'pulse';
    }

    $closest_highlight_color = sanitize_hex_color($closest_highlight_color);
    if (empty($closest_highlight_color)) {
        $closest_highlight_color = '#e74c3c';
    }

    $selected_highlight_color = sanitize_hex_color($selected_highlight_color);
    if (empty($selected_highlight_color)) {
        $selected_highlight_color = '#1d9bf0';
    }

    if ($closest_zoom_level < 2 || $closest_zoom_level > 12) {
        $closest_zoom_level = 7;
    }

    // Force original admin-ajax.php URL to bypass TranslatePress/Multilingual blocks
    $ajax_url = admin_url('admin-ajax.php');
    
    return array(
        'ajaxurl' => $ajax_url,
        'nonce' => wp_create_nonce('scb_servizi_map_nonce'),
        'site_url' => site_url(),
        'show_services_in_map' => get_option('scb_location_map_show_services', '1'),
        'show_service_filter' => $show_service_filter,
        'closest_highlight_mode' => $closest_highlight_mode,
        'closest_highlight_color' => $closest_highlight_color,
        'selected_highlight_color' => $selected_highlight_color,
        'closest_zoom_level' => $closest_zoom_level,
        'show_details_button' => get_option('scb_location_show_details_button', '1'),
        'require_ctrl_zoom' => get_option('scb_location_map_require_ctrl_zoom', '1'),
        'contact_url' => get_option('scb_location_contact_link', site_url('/contact-us')),
        'contact_url_pass_params' => get_option('scb_location_contact_link_pass_params', '1'),
        'disable_cache' => $disable_cache
    );
}

// Register AJAX handler for getting country from coordinates
add_action('wp_ajax_scb_get_country_from_coordinates', 'scb_ajax_get_country_from_coordinates');
add_action('wp_ajax_nopriv_scb_get_country_from_coordinates', 'scb_ajax_get_country_from_coordinates');

/**
 * AJAX handler for getting user's geolocation
 */
function scb_ajax_get_user_geolocation() {
    // No nonce check here as this is for public users and we want to avoid cache issues with nonces
    // The geolocation itself is based on the request's IP address
    nocache_headers();
    
    $user_location = scb_get_user_geolocation();
    
    wp_send_json_success($user_location);
    wp_die();
}

// Register AJAX handlers for getting user's geolocation
add_action('wp_ajax_scb_get_user_geolocation', 'scb_ajax_get_user_geolocation');
add_action('wp_ajax_nopriv_scb_get_user_geolocation', 'scb_ajax_get_user_geolocation');

// Enqueue admin scripts for location edit screen
add_action('admin_enqueue_scripts', 'scb_enqueue_location_admin_scripts');

// IMPORTANT: The relationship between Servizi and Location has been inverted
// Previously: Each Servizio had one Location (via dropdown)
// Now: Each Location can have multiple Servizi (via checkboxes)
// This change was implemented as per client requirements

// Register shortcode for world map
function scb_servizi_map_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(
        array(
            'height' => '500px',
            'width' => '100%',
        ),
        $atts,
        'servizi_map'
    );
    
    // Enqueue scripts and styles
    wp_enqueue_script('scb-servizi-map');
    wp_enqueue_style('scb-servizi-map');
    
    // Get all location_zona terms
    $zone_terms = get_terms(array(
        'taxonomy' => 'location_zona',
        'hide_empty' => false,
    ));
    
    // Get all servizi_categoria terms
    $categoria_terms = get_terms(array(
        'taxonomy' => 'servizi_categoria',
        'hide_empty' => false,
    ));

    $show_service_filter = get_option('scb_location_map_show_service_filter', '1') === '1';
    
    // Start output buffering
    ob_start();
    
    // Filter dropdowns
    ?>
    <div class="scb-servizi-container">
        <!-- SCB PLUGIN VERSION: 1.2.5 -->
        <div id="scb-debug-info" style="display:none; background:#fff3cd; padding:10px; border:1px solid #ffeeba; margin-bottom:15px; font-family:monospace; font-size:12px;">
            <strong>SCB Debug Info (v1.2.5):</strong><br>
            Server Time: <?php echo date('Y-m-d H:i:s'); ?><br>
            Shortcode executed.
        </div>
        
        <div class="scb-servizi-filters">
            <select id="scb-servizi-zona-filter">
                <option value=""><?php _e('All Geographic Zones', 'cpt-servizi'); ?></option>
                <?php foreach ($zone_terms as $term) : ?>
                    <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
            </select>
            
            <?php if ($show_service_filter) : ?>
                <select id="scb-servizi-categoria-filter">
                    <option value=""><?php _e('All Categories', 'cpt-servizi'); ?></option>
                    <?php foreach ($categoria_terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <button id="scb-servizi-filter-button" class="button"><?php _e('Filter', 'cpt-servizi'); ?></button>
            <button id="scb-servizi-reset-button" class="button" title="<?php _e('Reset the map to its original state', 'cpt-servizi'); ?>"><span class="dashicons dashicons-image-rotate"></span> <?php _e('Reset', 'cpt-servizi'); ?></button>
        </div>
        
        <div class="scb-servizi-map-container">
            <div id="scb-servizi-map" style="width: <?php echo esc_attr($atts['width']); ?>; height: 100%; max-width: 100%;"></div>
            
            <div id="scb-servizi-location-details">
                <div class="scb-servizi-location-placeholder">
                    <p><?php _e('Select a location on the map to view details', 'cpt-servizi'); ?></p>
                </div>
                <div class="scb-servizi-location-content" style="display: none;">
                    <h2 class="scb-servizi-location-title"></h2>
                    <div class="scb-servizi-location-image"></div>
                    <div class="scb-servizi-location-description"></div>
                    <div class="scb-servizi-location-services">
                        <h3><?php _e('Available Services', 'cpt-servizi'); ?></h3>
                        <ul class="scb-servizi-services-list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $map_data = scb_get_servizi_map_data();
    $map_vars = scb_get_map_localization_vars();
    ?>
    <script type="text/javascript">
        console.log('SCB Shortcode Debug: Initializing v1.2.5...');
        window.scbServiziMapData = <?php echo json_encode($map_data); ?>;
        window.scbServiziMapVars = <?php echo json_encode($map_vars); ?>;
        
        // Show debug div if debug mode is active (can be triggered by URL parameter ?scb_debug=1)
        if (window.location.search.indexOf('scb_debug=1') !== -1) {
            var debugDiv = document.getElementById('scb-debug-info');
            if (debugDiv) {
                debugDiv.style.display = 'block';
                debugDiv.innerHTML += '<br>Client Debug Mode: ON';
                debugDiv.innerHTML += '<br>Protocol: ' + window.location.protocol;
                debugDiv.innerHTML += '<br>Locations: ' + (window.scbServiziMapData.locations ? window.scbServiziMapData.locations.length : 0);
                debugDiv.innerHTML += '<br>User Geolocation Setting: ' + (window.scbServiziMapData.user_location ? JSON.stringify(window.scbServiziMapData.user_location) : 'undefined');
                debugDiv.innerHTML += '<br>AJAX URL: ' + window.scbServiziMapVars.ajaxurl;
                
                if (window.location.protocol !== 'https:') {
                    debugDiv.innerHTML += '<br><strong style="color:red;">CRITICAL: Geolocation API requires HTTPS to work!</strong>';
                }
            }
        }

        console.log('SCB Shortcode Debug: Data loaded into window.', {
            version: '1.2.5',
            protocol: window.location.protocol,
            locationsCount: window.scbServiziMapData.locations ? window.scbServiziMapData.locations.length : 0,
            userLocation: window.scbServiziMapData.user_location,
            ajaxUrl: window.scbServiziMapVars.ajaxurl
        });
    </script>
    <?php
    
    // Return the buffered content
    return ob_get_clean();
}

// Get map data for all locations and their services
function scb_get_servizi_map_data($args = array()) {
    // For debugging
    $debug = array(
        'total_locations' => 0,
        'with_services' => 0,
        'without_services' => 0,
        'with_valid_coordinates' => 0,
        'skipped_no_services' => 0,
        'skipped_invalid_coordinates' => 0,
        'total_services_used' => 0,
        'unique_locations' => 0
    );
    
    // Default query is for locations
    $default_args = array(
        'post_type' => 'location',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    $show_service_filter = get_option('scb_location_map_show_service_filter', '1') === '1';

    // Check for service category filter
    $category_filter = null;
    
    // If tax_query_for_services is set, use it for filtering services
    if ($show_service_filter && isset($args['tax_query_for_services']) && !empty($args['tax_query_for_services'])) {
        $category_filter = $args['tax_query_for_services'];
        unset($args['tax_query_for_services']);
    }
    // If regular tax_query is set and we're not filtering locations by zone,
    // assume it's for services (backward compatibility)
    elseif ($show_service_filter && isset($args['tax_query']) && !empty($args['tax_query']) && 
            (!isset($args['post_type']) || $args['post_type'] !== 'location')) {
        $category_filter = $args['tax_query'];
        unset($args['tax_query']);
    }
    
    $args = wp_parse_args($args, $default_args);
    $query = new WP_Query($args);
    
    // Prepare locations data
    $locations_data = array();
    
    // Count total locations
    $debug['total_locations'] = $query->post_count;
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $location_id = get_the_ID();
            
            // Get location coordinates
            $latitude = get_post_meta($location_id, '_scb_location_latitude', true);
            $longitude = get_post_meta($location_id, '_scb_location_longitude', true);
            $nazione = get_post_meta($location_id, '_scb_location_nazione', true);
            
            // Only include locations with valid coordinates
            if (!empty($latitude) && !empty($longitude) && is_numeric($latitude) && is_numeric($longitude)) {
                $debug['with_valid_coordinates']++;
                
                // Get the linked service IDs
                $service_ids = get_post_meta($location_id, '_scb_location_services_ids', true);
                
                // Only proceed if there are services linked to this location
                if (!empty($service_ids) && is_array($service_ids)) {
                    $debug['with_services']++;
                    
                    // Get location data
                    $location_title = get_the_title();
                    $location_content = get_the_content();
                    $location_thumbnail = get_the_post_thumbnail_url($location_id, 'full');
                    $location_zones = wp_get_post_terms($location_id, 'location_zona', array('fields' => 'names'));
                    
                    // Process country name if it contains non-Latin characters
                    if (!empty($nazione) && function_exists('scb_contains_non_latin_chars') && scb_contains_non_latin_chars($nazione)) {
                        // Check if the country name already has an English translation in parentheses
                        if (strpos($nazione, '(') === false) {
                            // Get coordinates to use for reverse geocoding
                            $lat = floatval($latitude);
                            $lon = floatval($longitude);
                            
                            // Make API request to OpenStreetMap Nominatim with English language preference
                            $api_url = add_query_arg(
                                array(
                                    'format' => 'json',
                                    'lat' => $lat,
                                    'lon' => $lon,
                                    'zoom' => 10,
                                    'addressdetails' => 1
                                ),
                                'https://nominatim.openstreetmap.org/reverse'
                            );
                            
                            $response = wp_remote_get($api_url, array(
                                'timeout' => 10,
                                'headers' => array(
                                    'Accept' => 'application/json',
                                    'Accept-Language' => 'en',
                                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                                )
                            ));
                            
                            // Check if request was successful
                            if (!is_wp_error($response)) {
                                $body = wp_remote_retrieve_body($response);
                                $data = json_decode($body, true);
                                
                                if (!empty($data) && isset($data['address']) && isset($data['address']['country'])) {
                                    $country_en = $data['address']['country'];
                                    
                                    // Format the country name with both versions
                                    $nazione = $nazione . ' (' . $country_en . ')';
                                }
                            }
                        }
                    }
                    
                    // Initialize location data
                    $location_data = array(
                        'id' => $location_id,
                        'title' => $location_title,
                        'content' => $location_content,
                        'thumbnail' => $location_thumbnail,
                        'permalink' => get_permalink($location_id),
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'nazione' => $nazione,
                        'zones' => $location_zones,
                        'services' => array(),
                        'all_categories' => array()
                    );
                    
                    // Get services data
                    $services_query_args = array(
                        'post_type' => 'servizi',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'post__in' => $service_ids,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    );
                    
                    // Apply category filter if it exists
                    if (isset($category_filter)) {
                        $services_query_args['tax_query'] = $category_filter;
                    }
                    
                    $services_query = new WP_Query($services_query_args);
                    
                    // Skip this location if no services match the category filter
                    if (isset($category_filter) && $services_query->post_count === 0) {
                        continue;
                    }
                    
                    if ($services_query->have_posts()) {
                        while ($services_query->have_posts()) {
                            $services_query->the_post();
                            $service_id = get_the_ID();
                            
                            // Get service data
                            // Fetch service categories as term objects to allow translation plugins (e.g., TranslatePress) to filter names per language
                            $category_terms = wp_get_post_terms($service_id, 'servizi_categoria', array('fields' => 'all'));
                            $category_names = array();
                            if (!is_wp_error($category_terms) && !empty($category_terms)) {
                                foreach ($category_terms as $cat_term) {
                                    // Use get_term_field with 'display' context so translations/filters can apply
                                    $translated_name = get_term_field('name', $cat_term->term_id, 'servizi_categoria', 'display');
                                    if (!is_wp_error($translated_name) && $translated_name !== '') {
                                        $category_names[] = $translated_name;
                                    }
                                }
                            }

                            $service_data = array(
                                'id' => $service_id,
                                'title' => get_the_title(),
                                'excerpt' => get_the_excerpt(),
                                'permalink' => get_permalink(),
                                'categories' => $category_names,
                                'tags' => wp_get_post_terms($service_id, 'servizi_tag', array('fields' => 'names')),
                                'thumbnail' => get_the_post_thumbnail_url($service_id, 'thumbnail'),
                            );
                            
                            // Add service to this location
                            $location_data['services'][] = $service_data;
                            
                            // Collect all categories for this location
                            $location_data['all_categories'] = array_unique(
                                array_merge($location_data['all_categories'], $service_data['categories'])
                            );
                            
                            $debug['total_services_used']++;
                        }
                        
                        // Only add locations that have services after filtering, unless the service filter is disabled.
                        if (!empty($location_data['services']) || !$show_service_filter) {
                            $locations_data[$location_id] = $location_data;
                        }
                    } elseif (!$show_service_filter) {
                        $locations_data[$location_id] = $location_data;
                    }
                    
                    wp_reset_postdata();
                } else {
                    $debug['without_services']++;

                    // Include locations without services when no category filter is applied
                    if (!isset($category_filter)) {
                        $location_title = get_the_title();
                        $location_content = get_the_content();
                        $location_thumbnail = get_the_post_thumbnail_url($location_id, 'full');
                        $location_zones = wp_get_post_terms($location_id, 'location_zona', array('fields' => 'names'));

                        $location_data = array(
                            'id' => $location_id,
                            'title' => $location_title,
                            'content' => $location_content,
                            'thumbnail' => $location_thumbnail,
                            'permalink' => get_permalink($location_id),
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'nazione' => $nazione,
                            'zones' => $location_zones,
                            'services' => array(),
                            'all_categories' => array()
                        );

                        $locations_data[$location_id] = $location_data;
                    } else {
                        $debug['skipped_no_services']++;
                    }
                }
            } else {
                $debug['skipped_invalid_coordinates']++;
            }
        }
    }
    
    wp_reset_postdata();
    
    // Convert locations data to indexed array
    $map_data = array_values($locations_data);
    $debug['unique_locations'] = count($map_data);
    
    // Separate debug info from map data
    $result = array(
        'locations' => $map_data,
        'debug' => null
    );
    
    // Add debug info if in development environment
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $result['debug'] = $debug;
    }
    
    // Add user's geolocation data
    $styling_options = get_option('scb_servizi_styling', array());
    $disable_geolocation = isset($styling_options['map_disable_geolocation']) && $styling_options['map_disable_geolocation'] === '1';
    
    if ($disable_geolocation) {
        $user_location = array(
            'latitude' => '38.0', // Mediterranean latitude
            'longitude' => '15.0', // Mediterranean longitude
            'is_disabled' => true
        );
    } else {
        // We no longer get geolocation on the server side to avoid caching issues between users.
        // Geolocation is handled via fresh AJAX requests on the client side.
        $user_location = array(
            'latitude' => null,
            'longitude' => null,
            'needs_ajax' => true
        );
    }
    
    $result['user_location'] = $user_location;
    
    return $result;
}

// AJAX handler for filtering map data
function scb_ajax_filter_servizi_map() {
    // Check nonce for security
    check_ajax_referer('scb_servizi_map_nonce', 'nonce');
    
    $zona = isset($_POST['zona']) ? sanitize_text_field($_POST['zona']) : '';
    $categoria = isset($_POST['categoria']) ? sanitize_text_field($_POST['categoria']) : '';
    $show_service_filter = get_option('scb_location_map_show_service_filter', '1') === '1';
    
    // Set up args for locations query
    $args = array(
        'post_type' => 'location',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    // Add zone filter if set
    if (!empty($zona)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'location_zona',
                'field' => 'slug',
                'terms' => $zona,
            )
        );
    }
    
    // Add category filter if set (will be applied to services in scb_get_servizi_map_data)
    if ($show_service_filter && !empty($categoria)) {
        $category_filter = array(
            array(
                'taxonomy' => 'servizi_categoria',
                'field' => 'slug',
                'terms' => $categoria,
            )
        );
        
        // We'll pass this separately to scb_get_servizi_map_data
        $args['tax_query_for_services'] = $category_filter;
    }
    
    // Get filtered map data
    $map_data = scb_get_servizi_map_data($args);
    
    wp_send_json_success($map_data);
    wp_die();
}

/**
 * Resolve the most reliable client IP address behind reverse proxies/CDNs.
 *
 * @return string
 */
function scb_get_client_ip_address() {
    $candidates = array();

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $candidates[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $candidates[] = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidates[] = $_SERVER['REMOTE_ADDR'];
    }

    // First pass: prefer public, routable IPs.
    foreach ($candidates as $candidate) {
        foreach (explode(',', $candidate) as $part) {
            $ip = trim($part);
            if (!$ip) {
                continue;
            }
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    // Fallback: any syntactically valid IP.
    foreach ($candidates as $candidate) {
        foreach (explode(',', $candidate) as $part) {
            $ip = trim($part);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '';
}

/**
 * Get user's geolocation based on IP address
 * Uses ipinfo.io API to get latitude and longitude
 * 
 * @return array Array with 'latitude' and 'longitude' keys, or empty if geolocation fails
 */
function scb_get_user_geolocation() {
    // Initialize empty result
    $result = array(
        'latitude' => null,
        'longitude' => null
    );
    
    // Get client's real IP (Cloudflare / reverse proxy aware).
    $user_ip = scb_get_client_ip_address();
    
    // For development/testing, you can hardcode an IP address
    // Uncomment the line below to test with a specific IP
    // $user_ip = '8.8.8.8'; // Google DNS IP for testing
    
    // Check if we have a valid IP
    if (empty($user_ip) || $user_ip == '127.0.0.1' || $user_ip == '::1') {
        // Local development environment, return empty result
        return $result;
    }
    
    // Intentionally avoid persistent caching to prevent stale/shared locations.
    $styling_options = get_option('scb_servizi_styling', array());
    
    // Get the selected provider
    $provider = isset($styling_options['map_geolocation_provider']) ? $styling_options['map_geolocation_provider'] : 'ipinfo';
    
    if ($provider === 'ipapi') {
        // Make API request to ip-api.com
        // Note: Free version supports only http
        $api_url = 'http://ip-api.com/json/' . $user_ip . '?fields=status,message,lat,lon';
        $response = wp_remote_get($api_url, array(
            'timeout' => 3
        ));
        
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (!empty($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['lat']) && isset($data['lon'])) {
                // Convert coordinates to string for consistency with ipinfo
                $result['latitude'] = (string) $data['lat'];
                $result['longitude'] = (string) $data['lon'];
                $result['provider'] = 'ip-api.com';
                
                return $result;
            }
        }
    }
    
    // Default to ipinfo.io if selected or if ip-api.com failed
    $api_url = 'https://ipinfo.io/' . $user_ip . '/geo';
    $response = wp_remote_get($api_url, array(
        'timeout' => 3,
        'headers' => array(
            'Accept' => 'application/json'
        )
    ));
    
    // Check if request was successful
    if (is_wp_error($response)) {
        return $result;
    }
    
    // Parse response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    // Check if we have valid data
    if (empty($data) || !isset($data['loc'])) {
        return $result;
    }
    
    // Parse coordinates (format: "latitude,longitude")
    $coordinates = explode(',', $data['loc']);
    if (count($coordinates) == 2) {
        $result['latitude'] = (string) $coordinates[0];
        $result['longitude'] = (string) $coordinates[1];
        $result['provider'] = 'ipinfo.io';
        
    }
    
    return $result;
}

// Register scripts and styles
function scb_register_servizi_map_scripts() {
    // Register Leaflet.js
    wp_register_script(
        'leaflet',
        'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js',
        array(),
        '1.7.1',
        true
    );
    
    wp_register_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css',
        array(),
        '1.7.1'
    );
    
    // Register custom map script with timestamp to force refresh
    $js_version = '1.2.5.' . time();
    wp_register_script(
        'scb-servizi-map',
        plugins_url('js/servizi-map.js', __FILE__),
        array('jquery', 'leaflet'),
        $js_version,
        true
    );
    
    // Register custom map styles
    wp_register_style(
        'scb-servizi-map',
        plugins_url('css/servizi-map.css', __FILE__),
        array('leaflet'),
        SCB_SERVIZI_VERSION
    );
    
    // Register custom pages styles
    wp_register_style(
        'scb-servizi-pages',
        plugins_url('css/servizi-pages.css', __FILE__),
        array(),
        SCB_SERVIZI_VERSION
    );
    
    // Localize script with AJAX URL, nonce, and site URL
    wp_localize_script(
        'scb-servizi-map',
        'scbServiziMapVars',
        scb_get_map_localization_vars()
    );
    
    // Localize script with translatable strings
    wp_localize_script(
        'scb-servizi-map',
        'scbServiziMapI18n',
        array(
            'errors' => array(
                'mapContainerNotFound' => __('Map container not found! Make sure the element with ID "scb-servizi-map" exists.', 'cpt-servizi'),
                'leafletNotLoaded' => __('Leaflet library not loaded! Make sure the Leaflet.js script is properly included.', 'cpt-servizi'),
                'mapContainerNotInDOM' => __('Map container element not found in the DOM!', 'cpt-servizi'),
                'markersLayerNotDefined' => __('markersLayer is not defined! Make sure it was created in initMap.', 'cpt-servizi'),
                'noDataForMarkers' => __('No data available for markers', 'cpt-servizi'),
                'noMarkersAdded' => __('No markers were added to the map. Check the location data.', 'cpt-servizi'),
                'invalidLocationObject' => __('Invalid location object provided to displayLocationDetails', 'cpt-servizi'),
                'sidebarElementsNotFound' => __('Sidebar elements not found in the DOM!', 'cpt-servizi'),
                'locationTitleElementNotFound' => __('Location title element not found!', 'cpt-servizi'),
                'locationImageElementNotFound' => __('Location image element not found!', 'cpt-servizi'),
                'locationDescriptionElementNotFound' => __('Location description element not found!', 'cpt-servizi'),
                'locationMetadataElementNotFound' => __('Location metadata element not found!', 'cpt-servizi'),
                'servicesListElementNotFound' => __('Services list element not found!', 'cpt-servizi'),
                'filterElementsNotFound' => __('Filter elements not found in the DOM!', 'cpt-servizi'),
                'mapElementNotFound' => __('Map element not found in the DOM!', 'cpt-servizi'),
                'ajaxVariablesNotDefined' => __('AJAX variables not properly defined!', 'cpt-servizi'),
                'emptyResponse' => __('Empty response received from server', 'cpt-servizi'),
                'serverError' => __('Server returned error:', 'cpt-servizi'),
                'noDataInResponse' => __('No data in response:', 'cpt-servizi'),
                'invalidResponseFormat' => __('Invalid AJAX response format:', 'cpt-servizi'),
                // Additional error messages for console logs
                'invalidUserCoordinates' => __('Invalid user coordinates:', 'cpt-servizi'),
                'noValidLocationsFound' => __('No valid locations found to compare with user location', 'cpt-servizi'),
                'invalidMapDataFormat' => __('Invalid map data format:', 'cpt-servizi'),
                'noLocationDataForMarkers' => __('No location data available for markers!', 'cpt-servizi'),
                'errorInitializingMap' => __('Error initializing map:', 'cpt-servizi'),
                'invalidCoordinatesForLocation' => __('Invalid coordinates for location:', 'cpt-servizi'),
                'errorCreatingMarker' => __('Error creating marker for location:', 'cpt-servizi'),
                'markerForClosestLocationNotFound' => __('Could not find marker for closest location', 'cpt-servizi'),
                'errorFittingBounds' => __('Error fitting bounds to markers:', 'cpt-servizi'),
                'errorInAddMarkers' => __('Error in addMarkers function:', 'cpt-servizi')
            ),
            'ui' => array(
                'zoomTooltip' => __('To zoom, hold <strong>Ctrl</strong> (or <strong>⌘ Cmd</strong> on Mac) while using the mouse wheel', 'cpt-servizi'),
                'categories' => __('Categories:', 'cpt-servizi'),
                'zones' => __('Zones:', 'cpt-servizi'),
                'country' => __('Country:', 'cpt-servizi'),
                'noDescription' => __('No description available', 'cpt-servizi'),
                'details' => __('Details', 'cpt-servizi'),
                'noServicesAvailable' => __('No services available at this location.', 'cpt-servizi'),
                'unnamedLocation' => __('Unnamed Location', 'cpt-servizi'),
                'locationImage' => __('Location Image', 'cpt-servizi'),
                'contactUs' => __('Contact us', 'cpt-servizi')
            )
        )
    );
    
    // Note: The actual map data is localized in the shortcode function
}

// Hook into the 'init' action
add_action('init', 'scb_register_servizi_post_type', 0);
add_action('init', 'scb_register_location_post_type', 0);
add_action('init', 'scb_register_servizi_categoria_taxonomy', 0);
add_action('init', 'scb_register_location_zona_taxonomy', 0);
add_action('init', 'scb_register_servizi_tag_taxonomy', 0);
add_action('init', 'scb_register_servizi_map_scripts');

// Filter the content for single servizi and location pages
add_filter('the_content', 'scb_filter_servizi_location_content');

// Filter the archive page output
add_action('pre_get_posts', 'scb_setup_archive_pages');
add_filter('the_excerpt', 'scb_filter_archive_excerpt', 20);
add_filter('the_content', 'scb_filter_archive_content', 20);
add_action('loop_start', 'scb_archive_loop_start');
add_action('loop_end', 'scb_archive_loop_end');

// Customize the query for archive pages
add_action('pre_get_posts', 'scb_customize_archive_query');

// Enqueue styles for CPT pages
add_action('wp_enqueue_scripts', 'scb_enqueue_cpt_styles');

// Hook into admin actions
add_action('add_meta_boxes', 'scb_location_add_meta_boxes');
add_action('add_meta_boxes', 'scb_servizi_add_meta_boxes');
add_action('save_post', 'scb_location_save_meta');
add_action('save_post', 'scb_location_services_save_meta');
add_action('save_post', 'scb_servizi_locations_save_meta');

/**
 * Register meta boxes for the servizi post type
 */
function scb_servizi_add_meta_boxes() {
    add_meta_box(
        'scb_servizi_locations',
        __('Connected Locations', 'cpt-servizi'),
        'scb_servizi_locations_callback',
        'servizi',
        'normal',
        'high'
    );
}

/**
 * Servizi locations meta box callback function
 */
function scb_servizi_locations_callback($post) {
    wp_nonce_field('scb_servizi_locations_save_meta', 'scb_servizi_locations_meta_nonce');
    
    $selected_locations = get_post_meta($post->ID, '_scb_servizi_locations_ids', true);
    if (!is_array($selected_locations)) {
        $selected_locations = array();
    }
    
    // Get all locations
    $locations = get_posts(array(
        'post_type' => 'location',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    ));
    
    if (empty($locations)) {
        echo '<p>' . __('No locations available. Create some locations first.', 'cpt-servizi') . '</p>';
        return;
    }
    
    echo '<div style="max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd;">';
    echo '<p>' . __('Select locations where this service is available:', 'cpt-servizi') . '</p>';
    
    foreach ($locations as $location) {
        $checked = in_array($location->ID, $selected_locations) ? 'checked="checked"' : '';
        ?>
        <p>
            <label>
                <input type="checkbox" name="scb_servizi_locations_ids[]" value="<?php echo esc_attr($location->ID); ?>" <?php echo $checked; ?> />
                <?php echo esc_html($location->post_title); ?>
            </label>
        </p>
        <?php
    }
    
    echo '</div>';
}

/**
 * Save servizi locations meta box data
 */
function scb_servizi_locations_save_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['scb_servizi_locations_meta_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['scb_servizi_locations_meta_nonce'], 'scb_servizi_locations_save_meta')) {
        return;
    }
    
    // Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if ('servizi' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Get previously selected locations to handle removed locations
    $previous_locations = get_post_meta($post_id, '_scb_servizi_locations_ids', true);
    if (!is_array($previous_locations)) {
        $previous_locations = array();
    }
    
    // Save selected locations
    $locations_ids = array();
    if (isset($_POST['scb_servizi_locations_ids']) && is_array($_POST['scb_servizi_locations_ids'])) {
        foreach ($_POST['scb_servizi_locations_ids'] as $location_id) {
            // Ensure we're only saving valid location IDs
            $location_id = absint($location_id);
            if ($location_id > 0 && get_post_type($location_id) === 'location') {
                $locations_ids[] = $location_id;
            }
        }
    }
    
    // Update the meta field with the array of location IDs
    update_post_meta($post_id, '_scb_servizi_locations_ids', $locations_ids);
    
    // Update each location's service list to include this service
    foreach ($locations_ids as $location_id) {
        // Get the location's current services
        $location_services = get_post_meta($location_id, '_scb_location_services_ids', true);
        if (!is_array($location_services)) {
            $location_services = array();
        }
        
        // Add this service to the location's services if not already included
        if (!in_array($post_id, $location_services)) {
            $location_services[] = $post_id;
            update_post_meta($location_id, '_scb_location_services_ids', $location_services);
        }
    }
    
    // Remove this service from locations that were unselected
    $removed_locations = array_diff($previous_locations, $locations_ids);
    foreach ($removed_locations as $location_id) {
        $location_services = get_post_meta($location_id, '_scb_location_services_ids', true);
        if (is_array($location_services)) {
            // Remove this service from the location's services
            $location_services = array_diff($location_services, array($post_id));
            update_post_meta($location_id, '_scb_location_services_ids', $location_services);
        }
    }
}

// Register shortcode
add_shortcode('servizi_map', 'scb_servizi_map_shortcode');

// Register AJAX handlers
add_action('wp_ajax_scb_filter_servizi_map', 'scb_ajax_filter_servizi_map');
add_action('wp_ajax_nopriv_scb_filter_servizi_map', 'scb_ajax_filter_servizi_map');
add_action('wp_ajax_scb_add_featured_image', 'scb_ajax_add_featured_image');

/**
 * Add featured image column to location admin list
 *
 * @param array $columns The existing columns
 * @return array The modified columns
 */
function scb_location_add_thumbnail_column($columns) {
    $new_columns = array();
    
    // Add featured image column after checkbox column
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'cb') {
            $new_columns['thumbnail'] = __('Immagine in evidenza', 'cpt-servizi');
        }
    }
    
    return $new_columns;
}

/**
 * Display featured image in the custom column
 *
 * @param string $column The column name
 * @param int $post_id The post ID
 */
function scb_location_display_thumbnail_column($column, $post_id) {
    if ($column !== 'thumbnail') {
        return;
    }
    
    $thumbnail_id = get_post_thumbnail_id($post_id);
    
    if ($thumbnail_id) {
        // Display the featured image
        $image = wp_get_attachment_image($thumbnail_id, array(50, 50), true);
        echo $image;
    } else {
        // Display a placeholder with an "Add Image" button
        echo '<div class="scb-location-no-thumbnail">';
        echo '<button type="button" class="button scb-add-thumbnail" data-post-id="' . esc_attr($post_id) . '">';
        echo __('Add Image', 'cpt-servizi');
        echo '</button>';
        echo '</div>';
    }
}

/**
 * Make the location thumbnail column sortable.
 *
 * @param array $columns The sortable columns.
 * @return array
 */
function scb_location_sortable_columns($columns) {
    $columns['thumbnail'] = 'thumbnail_presence';

    return $columns;
}

/**
 * Apply custom ordering for location list sorting by featured image presence.
 *
 * @param WP_Query $query The current query.
 * @return void
 */
function scb_location_handle_thumbnail_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-location') {
        return;
    }

    if ($query->get('orderby') !== 'thumbnail_presence') {
        return;
    }

    $query->set('scb_thumbnail_presence_sort', true);
}

/**
 * Add SQL clauses to sort locations by featured image presence.
 *
 * @param array    $clauses The query clauses.
 * @param WP_Query $query   The current query.
 * @return array
 */
function scb_location_thumbnail_posts_clauses($clauses, $query) {
    if (!is_admin() || !$query->get('scb_thumbnail_presence_sort')) {
        return $clauses;
    }

    global $wpdb;

    $order = strtoupper($query->get('order')) === 'DESC' ? 'DESC' : 'ASC';
    $thumbnail_join_alias = 'scb_thumbnail_sort_pm';

    if (strpos($clauses['join'], $thumbnail_join_alias) === false) {
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS {$thumbnail_join_alias} ON ({$wpdb->posts}.ID = {$thumbnail_join_alias}.post_id AND {$thumbnail_join_alias}.meta_key = '_thumbnail_id')";
    }

    $presence_order = $order === 'ASC'
        ? "CASE WHEN {$thumbnail_join_alias}.meta_value IS NULL OR {$thumbnail_join_alias}.meta_value = '' THEN 1 ELSE 0 END ASC"
        : "CASE WHEN {$thumbnail_join_alias}.meta_value IS NULL OR {$thumbnail_join_alias}.meta_value = '' THEN 1 ELSE 0 END DESC";

    $clauses['groupby'] = "{$wpdb->posts}.ID";
    $clauses['orderby'] = $presence_order . ", {$wpdb->posts}.post_title ASC";

    return $clauses;
}

/**
 * Enqueue scripts for the location admin list page
 *
 * @param string $hook The current admin page
 */
function scb_location_admin_list_scripts($hook) {
    // Only enqueue on the location list page
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'location' || $hook !== 'edit.php') {
        return;
    }
    
    // Enqueue media scripts
    wp_enqueue_media();
    
    // Enqueue custom script
    wp_enqueue_script(
        'scb-location-admin-list',
        plugins_url('js/location-admin-list.js', __FILE__),
        array('jquery'),
        SCB_SERVIZI_VERSION,
        true
    );
    
    // Localize the script with necessary data
    wp_localize_script(
        'scb-location-admin-list',
        'scbLocationAdminListVars',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scb_location_thumbnail_nonce'),
            'addImageText' => __('Add Featured Image', 'cpt-servizi'),
            'selectImageText' => __('Select or Upload an Image', 'cpt-servizi'),
            'useThisImageText' => __('Use This Image', 'cpt-servizi')
        )
    );
    
    // Add inline styles
    wp_add_inline_style('wp-admin', '
        .scb-location-no-thumbnail {
            text-align: center;
        }
        .column-thumbnail {
            width: 80px;
        }
        .column-thumbnail img {
            max-width: 50px;
            max-height: 50px;
            height: auto;
            width: auto;
        }
    ');
}

/**
 * AJAX handler for adding a featured image to a location
 */
function scb_ajax_add_featured_image() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'scb_location_thumbnail_nonce')) {
        wp_send_json_error(__('Security check failed', 'cpt-servizi'));
        wp_die();
    }
    
    // Get and validate post ID and attachment ID
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $attachment_id = isset($_POST['attachment_id']) ? absint($_POST['attachment_id']) : 0;
    
    // Check if post exists and is a location
    if (!$post_id || get_post_type($post_id) !== 'location') {
        wp_send_json_error(__('Invalid location', 'cpt-servizi'));
        wp_die();
    }
    
    // Check if attachment exists
    if (!$attachment_id || !wp_get_attachment_url($attachment_id)) {
        wp_send_json_error(__('Invalid attachment', 'cpt-servizi'));
        wp_die();
    }
    
    // Set the featured image
    $result = set_post_thumbnail($post_id, $attachment_id);
    
    if ($result) {
        // Get the new thumbnail HTML
        $image = wp_get_attachment_image($attachment_id, array(50, 50), true);
        wp_send_json_success(array(
            'message' => __('Featured image updated successfully', 'cpt-servizi'),
            'image' => $image
        ));
    } else {
        wp_send_json_error(__('Error updating featured image', 'cpt-servizi'));
    }
    
    wp_die();
}

// Add admin notice and admin bar button for flushing rewrite rules
add_action('admin_notices', 'scb_servizi_admin_notice');
add_action('admin_bar_menu', 'scb_servizi_admin_bar_button', 100);
add_action('admin_init', 'scb_servizi_flush_rewrite_rules');

// Add featured image column to location admin list
add_filter('manage_location_posts_columns', 'scb_location_add_thumbnail_column');
add_action('manage_location_posts_custom_column', 'scb_location_display_thumbnail_column', 10, 2);
add_action('admin_enqueue_scripts', 'scb_location_admin_list_scripts');
add_filter('manage_edit-location_sortable_columns', 'scb_location_sortable_columns');
add_action('pre_get_posts', 'scb_location_handle_thumbnail_sorting');
add_filter('posts_clauses', 'scb_location_thumbnail_posts_clauses', 10, 2);

/**
 * Display an admin notice about flushing rewrite rules
 */
function scb_servizi_admin_notice() {
    // Only show this notice to administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we should show the notice
    $show_notice = get_option('scb_servizi_show_rewrite_notice', true);
    
    if ($show_notice) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php _e('CPT Services: Update Required', 'cpt-servizi'); ?></strong>
                <br>
                <?php _e('If you are having trouble viewing service or location pages, try updating the permalink structure.', 'cpt-servizi'); ?>
            </p>
            <p>
                <a href="<?php echo esc_url(add_query_arg('scb_flush_rules', 'true')); ?>" class="button button-primary">
                    <?php _e('Update Permalinks', 'cpt-servizi'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('scb_dismiss_notice', 'true')); ?>" class="button">
                    <?php _e('Hide this message', 'cpt-servizi'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}

/**
 * Add a button to the admin bar for flushing rewrite rules
 * 
 * @param WP_Admin_Bar $wp_admin_bar The admin bar object
 */
function scb_servizi_admin_bar_button($wp_admin_bar) {
    // Only show this to administrators
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Add the parent menu
    $wp_admin_bar->add_node(array(
        'id'    => 'scb-servizi-menu',
        'title' => __('CPT Servizi', 'cpt-servizi'),
        'href'  => admin_url('edit.php?post_type=servizi'),
    ));
    
    // Add the flush rules submenu
    $wp_admin_bar->add_node(array(
        'id'     => 'scb-servizi-flush-rules',
        'parent' => 'scb-servizi-menu',
        'title'  => __('Update Permalinks', 'cpt-servizi'),
        'href'   => add_query_arg('scb_flush_rules', 'true'),
    ));
}

/**
 * Handle flushing rewrite rules and dismissing the admin notice
 */
function scb_servizi_flush_rewrite_rules() {
    // Only allow administrators to perform these actions
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Handle dismissing the notice
    if (isset($_GET['scb_dismiss_notice'])) {
        update_option('scb_servizi_show_rewrite_notice', false);
        wp_redirect(remove_query_arg('scb_dismiss_notice'));
        exit;
    }
    
    // Handle flushing rewrite rules
    if (isset($_GET['scb_flush_rules'])) {
        // Re-register the post types
        scb_register_servizi_post_type();
        scb_register_location_post_type();
        scb_register_servizi_categoria_taxonomy();
        scb_register_location_zona_taxonomy();
        scb_register_servizi_tag_taxonomy();
        
        // Flush the rewrite rules
        flush_rewrite_rules();
        
        // Set a transient to show a success message
        set_transient('scb_servizi_rules_flushed', true, 60);
        
        // Redirect back to remove the query arg
        wp_redirect(remove_query_arg('scb_flush_rules'));
        exit;
    }
    
    // Show success message if rules were just flushed
    if (get_transient('scb_servizi_rules_flushed')) {
        delete_transient('scb_servizi_rules_flushed');
        add_action('admin_notices', 'scb_servizi_flush_success_notice');
    }
}

/**
 * Display a success notice after flushing rewrite rules
 */
function scb_servizi_flush_success_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>
            <strong><?php _e('Permalinks updated successfully!', 'cpt-servizi'); ?></strong>
            <br>
            <?php _e('Service and location pages should now be accessible.', 'cpt-servizi'); ?>
        </p>
    </div>
    <?php
}

/**
 * Set up archive pages for our custom post types
 * 
 * @param WP_Query $query The WordPress query object
 */
function scb_setup_archive_pages($query) {
    // Only modify main queries on the frontend
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Check if we're on an archive page for our CPTs
    if ($query->is_post_type_archive(array('servizi', 'location')) || 
        $query->is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona'))) {
        
        // Set excerpt length to 20 words
        add_filter('excerpt_length', function($length) {
            return 20;
        }, 999);
        
        // Change excerpt more text
        add_filter('excerpt_more', function($more) {
            return '...';
        }, 999);
        
        // Add post class for our styling
        add_filter('post_class', function($classes) {
            $classes[] = 'scb-archive-item';
            return $classes;
        });
    }
}

/**
 * Filter the excerpt for archive pages
 * 
 * @param string $excerpt The post excerpt
 * @return string The filtered excerpt
 */
function scb_filter_archive_excerpt($excerpt) {
    // Only modify excerpts on our CPT archive pages
    if (!is_post_type_archive(array('servizi', 'location')) && 
        !is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona'))) {
        return $excerpt;
    }
    
    // Wrap the excerpt in our styling
    $output = '<div class="scb-archive-item-excerpt">';
    $output .= $excerpt;
    $output .= '</div>';
    
    // Add a "Read more" link
    $output .= '<a href="' . get_permalink() . '" class="scb-archive-item-link">';
    $output .= __('Leggi di più', 'cpt-servizi');
    $output .= '</a>';
    
    return $output;
}

/**
 * Filter the content for archive pages
 * 
 * @param string $content The post content
 * @return string The filtered content
 */
function scb_filter_archive_content($content) {
    // Only modify content on our CPT archive pages and not on single pages
    if (is_singular() || 
        (!is_post_type_archive(array('servizi', 'location')) && 
         !is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona')))) {
        return $content;
    }
    
    global $post;
    $output = '';
    
    // Add featured image if available
    if (has_post_thumbnail()) {
        $output .= '<div class="scb-archive-item-image">';
        $output .= '<a href="' . get_permalink() . '">';
        $output .= get_the_post_thumbnail($post->ID, 'medium');
        $output .= '</a>';
        $output .= '</div>';
    }
    
    // Add item content wrapper
    $output .= '<div class="scb-archive-item-content">';
    
    // Add title
    $output .= '<h2 class="scb-archive-item-title">';
    $output .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
    $output .= '</h2>';
    
    // Add meta information based on post type
    $output .= '<div class="scb-archive-item-meta">';
    
    if ($post->post_type === 'servizi') {
        // Add categories for servizi
        $categories = get_the_terms($post->ID, 'servizi_categoria');
        if ($categories && !is_wp_error($categories)) {
            $category_names = array();
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
            $output .= __('Categories: ', 'cpt-servizi') . implode(', ', $category_names);
        }
    } elseif ($post->post_type === 'location') {
        // Add zones for location
        $zones = get_the_terms($post->ID, 'location_zona');
        if ($zones && !is_wp_error($zones)) {
            $zone_names = array();
            foreach ($zones as $zone) {
                $zone_names[] = $zone->name;
            }
            $output .= __('Zone: ', 'cpt-servizi') . implode(', ', $zone_names);
        }
        
        // Add country if available
        $nazione = get_post_meta($post->ID, '_scb_location_nazione', true);
        if ($nazione) {
            if (!empty($zone_names)) {
                $output .= ' | ';
            }
            $output .= __('Nazione: ', 'cpt-servizi') . $nazione;
        }
    }
    
    $output .= '</div>'; // Close .scb-archive-item-meta
    
    // The excerpt will be added by the excerpt filter
    // We're returning an empty string here to prevent the content from being displayed
    // as the excerpt filter will handle the display of the excerpt and "Read more" link
    
    $output .= '</div>'; // Close .scb-archive-item-content
    
    return $output;
}

/**
 * Add opening HTML before the loop on archive pages
 * 
 * @param WP_Query $query The WordPress query object
 */
function scb_archive_loop_start($query) {
    // Only modify main query on our CPT archive pages
    if (!$query->is_main_query() || 
        (!is_post_type_archive(array('servizi', 'location')) && 
         !is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona')))) {
        return;
    }
    
    // Add archive header
    echo '<div class="scb-archive-header">';
    
    // Add title based on the current page
    echo '<h1>';
    if (is_post_type_archive('servizi')) {
        echo __('Tutti i Servizi', 'cpt-servizi');
    } elseif (is_post_type_archive('location')) {
        echo __('Tutte le Locations', 'cpt-servizi');
    } elseif (is_tax()) {
        single_term_title();
    }
    echo '</h1>';
    
    // Add description if available
    if (is_tax()) {
        $term_description = term_description();
        if (!empty($term_description)) {
            echo '<div class="scb-archive-description">' . $term_description . '</div>';
        }
    }
    
    echo '</div>'; // Close .scb-archive-header
    
    // Open the grid container
    echo '<div class="scb-archive-grid">';
}

/**
 * Add closing HTML after the loop on archive pages
 * 
 * @param WP_Query $query The WordPress query object
 */
function scb_archive_loop_end($query) {
    // Only modify main query on our CPT archive pages
    if (!$query->is_main_query() || 
        (!is_post_type_archive(array('servizi', 'location')) && 
         !is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona')))) {
        return;
    }
    
    // Close the grid container
    echo '</div>'; // Close .scb-archive-grid
}

/**
 * Enqueue styles for CPT single and archive pages
 */
function scb_enqueue_cpt_styles() {
    // Check if we're on a single or archive page for our CPTs
    if (is_singular(array('servizi', 'location')) || 
        is_post_type_archive(array('servizi', 'location')) ||
        is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona'))) {
        
        // Enqueue the styles
        wp_enqueue_style('scb-servizi-pages');
    }
}

/**
 * Customize the query for archive pages of our custom post types
 * 
 * @param WP_Query $query The WordPress query object
 */
function scb_customize_archive_query($query) {
    // Only modify main queries on the frontend
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Customize Servizi archive
    if ($query->is_post_type_archive('servizi')) {
        // Set posts per page
        $query->set('posts_per_page', 12);
        
        // Set order by title
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        
        // If a category is selected, don't modify the query further
        if ($query->get('servizi_categoria')) {
            return;
        }
        
        // If a tag is selected, don't modify the query further
        if ($query->get('servizi_tag')) {
            return;
        }
    }
    
    // Customize Location archive
    if ($query->is_post_type_archive('location')) {
        // Set posts per page
        $query->set('posts_per_page', 12);
        
        // Set order by title
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        
        // If a zone is selected, don't modify the query further
        if ($query->get('location_zona')) {
            return;
        }
    }
}

/**
 * Include required files
 */
function scb_servizi_include_files() {
    // Include styling class
    require_once SCB_SERVIZI_PLUGIN_DIR . 'includes/class-scb-servizi-styling.php';
}
add_action('plugins_loaded', 'scb_servizi_include_files');

/**
 * Filter the content for single servizi and location pages
 * 
 * @param string $content The original content
 * @return string The filtered content
 */
function scb_filter_servizi_location_content($content) {
    // Only modify content for single servizi and location pages
    if (!is_singular(array('servizi', 'location'))) {
        return $content;
    }
    
    global $post;
    $post_type = get_post_type();
    $output = '';
    
    // Common elements for both post types
    $output .= '<div class="scb-single-' . $post_type . '">';
    
    // For location post type, add the title in h1 before the featured image
    if ($post_type === 'location') {
        $output .= '<h1 class="scb-location-title">' . get_the_title() . '</h1>';
    }
    // For servizi post type, add the title in h1 before the featured image
    elseif ($post_type === 'servizi') {
        $output .= '<h1 class="scb-servizi-title">' . get_the_title() . '</h1>';
    }
    
    // Add featured image if available
    if (has_post_thumbnail()) {
        $output .= '<div class="scb-featured-image">';
        $output .= get_the_post_thumbnail($post->ID, 'large');
        $output .= '</div>';
    }
    
    // Add the original content
    $output .= '<div class="scb-content">';
    $output .= $content;
    $output .= '</div>';
    
    // Post type specific content
    if ($post_type === 'servizi') {
        // Add categories
        $categories = get_the_terms($post->ID, 'servizi_categoria');
        if ($categories && !is_wp_error($categories)) {
            $output .= '<div class="scb-categories">';
            $output .= '<h3>' . __('Categories', 'cpt-servizi') . '</h3>';
            $output .= '<ul>';
            foreach ($categories as $category) {
                $output .= '<li><a href="' . get_term_link($category) . '">' . $category->name . '</a></li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }
        
        // Add tags
        $tags = get_the_terms($post->ID, 'servizi_tag');
        if ($tags && !is_wp_error($tags)) {
            $output .= '<div class="scb-tags">';
            $output .= '<h3>' . __('Tag', 'cpt-servizi') . '</h3>';
            $output .= '<ul>';
            foreach ($tags as $tag) {
                $output .= '<li><a href="' . get_term_link($tag) . '">' . $tag->name . '</a></li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }
        
        // Add locations where this service is available
        $locations_query = new WP_Query(array(
            'post_type' => 'location',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_scb_location_services_ids',
                    'value' => $post->ID,
                    'compare' => 'LIKE'
                )
            )
        ));
        
        if ($locations_query->have_posts()) {
            $output .= '<div class="scb-service-locations">';
            $output .= '<h3>' . __('Available in these locations', 'cpt-servizi') . '</h3>';
            $output .= '<ul>';
            while ($locations_query->have_posts()) {
                $locations_query->the_post();
                $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
            wp_reset_postdata();
        }
    } elseif ($post_type === 'location') {
        // Add geographic coordinates
        $latitude = get_post_meta($post->ID, '_scb_location_latitude', true);
        $longitude = get_post_meta($post->ID, '_scb_location_longitude', true);
        $nazione = get_post_meta($post->ID, '_scb_location_nazione', true);
        
        if ($latitude && $longitude) {
            $output .= '<div class="scb-location-coordinates">';
            $output .= '<h3>' . __('Coordinate Geografiche', 'cpt-servizi') . '</h3>';
            $output .= '<p><strong>' . __('Latitudine:', 'cpt-servizi') . '</strong> ' . esc_html($latitude) . '</p>';
            $output .= '<p><strong>' . __('Longitudine:', 'cpt-servizi') . '</strong> ' . esc_html($longitude) . '</p>';
            if ($nazione) {
                $output .= '<p><strong>' . __('Nazione:', 'cpt-servizi') . '</strong> ' . esc_html($nazione) . '</p>';
            }
            $output .= '</div>';
        }
        
        // Add zones
        $zones = get_the_terms($post->ID, 'location_zona');
        if ($zones && !is_wp_error($zones)) {
            $output .= '<div class="scb-location-zones">';
            $output .= '<h3>' . __('Zone Geografiche', 'cpt-servizi') . '</h3>';
            $output .= '<ul>';
            foreach ($zones as $zone) {
                $output .= '<li><a href="' . get_term_link($zone) . '">' . $zone->name . '</a></li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }
        
        // Add services available at this location (toggle via settings)
        $show_services = get_option('scb_location_show_services', '1');
        if ($show_services === '1') {
            $service_ids = get_post_meta($post->ID, '_scb_location_services_ids', true);
            if (!empty($service_ids) && is_array($service_ids)) {
                $services_query = new WP_Query(array(
                    'post_type' => 'servizi',
                    'posts_per_page' => -1,
                    'post__in' => $service_ids,
                    'orderby' => 'title',
                    'order' => 'ASC'
                ));
                
                if ($services_query->have_posts()) {
                    $output .= '<div class="scb-location-services">';
                    $output .= '<h3>' . __('Servizi disponibili', 'cpt-servizi') . '</h3>';
                    $output .= '<ul>';
                    while ($services_query->have_posts()) {
                        $services_query->the_post();
                        $output .= '<li>';
                        $output .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                        $output .= '<div><a href="' . get_permalink() . '" class="scb-servizi-service-link">details</a></div>';
                        $output .= '<hr class="scb-service-divider">';
                        // Build Contact Us URL based on settings
                        $base_contact_url = get_option('scb_location_contact_link', site_url('/contact-us'));
                        $pass_params = get_option('scb_location_contact_link_pass_params', '1') === '1';
                        $contact_url = $base_contact_url;
                        if ($pass_params) {
                            $location_id = $post->ID;
                            $location_name = get_the_title($post->ID);
                            $contact_url = add_query_arg(
                                array(
                                    'location_id' => $location_id,
                                    'location_name' => $location_name,
                                ),
                                $base_contact_url
                            );
                        }
                        $output .= '<div><a href="' . esc_url($contact_url) . '" class="scb-contact-button">contact us</a></div>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                    $output .= '</div>';
                    wp_reset_postdata();
                }
            }
        }
        
        // Add a small map showing the location
        if ($latitude && $longitude) {
            $output .= '<div class="scb-location-map">';
            $output .= '<h3>' . __('Mappa', 'cpt-servizi') . '</h3>';
            $output .= '<div id="scb-single-location-map" style="height: 300px; width: 100%;"></div>';
            
            // Add inline script to initialize the map
            $output .= '<script type="text/javascript">
                jQuery(document).ready(function($) {
                    if (typeof L !== "undefined") {
                        var map = L.map("scb-single-location-map").setView([' . esc_js($latitude) . ', ' . esc_js($longitude) . '], 12);
                        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                            attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors\'
                        }).addTo(map);
                        L.marker([' . esc_js($latitude) . ', ' . esc_js($longitude) . ']).addTo(map)
                            .bindPopup("' . esc_js(get_the_title()) . '");
                    }
                });
            </script>';
            $output .= '</div>';
            
            // Enqueue Leaflet scripts and styles
            wp_enqueue_script('leaflet');
            wp_enqueue_style('leaflet');
        }
    }
    
    $output .= '</div>'; // Close .scb-single-{post_type}
    
    return $output;
}
// ===== Location Settings Page =====
add_action('admin_menu', 'scb_location_settings_menu');

/**
 * Add a settings submenu under the Location CPT menu
 */
function scb_location_settings_menu() {
    add_submenu_page(
        'edit.php?post_type=location',
        __('Impostazioni Location', 'cpt-servizi'),
        __('Impostazioni', 'cpt-servizi'),
        'manage_options',
        'scb-location-settings',
        'scb_render_location_settings_page'
    );
}

/**
 * Render the settings page for Location CPT
 */
function scb_render_location_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission
    if (isset($_POST['scb_location_settings_nonce']) && wp_verify_nonce($_POST['scb_location_settings_nonce'], 'scb_save_location_settings')) {
        $show_services = isset($_POST['scb_location_show_services']) ? '1' : '0';
        update_option('scb_location_show_services', $show_services);
        
        $map_show_services = isset($_POST['scb_location_map_show_services']) ? '1' : '0';
        update_option('scb_location_map_show_services', $map_show_services);

        $map_show_service_filter = isset($_POST['scb_location_map_show_service_filter']) ? '1' : '0';
        update_option('scb_location_map_show_service_filter', $map_show_service_filter);

        $closest_highlight_mode = isset($_POST['scb_location_closest_highlight_mode']) ? sanitize_text_field($_POST['scb_location_closest_highlight_mode']) : 'pulse';
        if (!in_array($closest_highlight_mode, array('pin', 'ring', 'pulse'), true)) {
            $closest_highlight_mode = 'pulse';
        }
        update_option('scb_location_closest_highlight_mode', $closest_highlight_mode);

        $closest_highlight_color = isset($_POST['scb_location_closest_highlight_color']) ? sanitize_hex_color($_POST['scb_location_closest_highlight_color']) : '';
        if (empty($closest_highlight_color)) {
            $closest_highlight_color = '#e74c3c';
        }
        update_option('scb_location_closest_highlight_color', $closest_highlight_color);

        $selected_highlight_color = isset($_POST['scb_location_selected_highlight_color']) ? sanitize_hex_color($_POST['scb_location_selected_highlight_color']) : '';
        if (empty($selected_highlight_color)) {
            $selected_highlight_color = '#1d9bf0';
        }
        update_option('scb_location_selected_highlight_color', $selected_highlight_color);

        $closest_zoom_level = isset($_POST['scb_location_closest_zoom_level']) ? absint($_POST['scb_location_closest_zoom_level']) : 7;
        if ($closest_zoom_level < 2 || $closest_zoom_level > 12) {
            $closest_zoom_level = 7;
        }
        update_option('scb_location_closest_zoom_level', $closest_zoom_level);
        
        $show_details_button = isset($_POST['scb_location_show_details_button']) ? '1' : '0';
        update_option('scb_location_show_details_button', $show_details_button);
        
        $require_ctrl_zoom = isset($_POST['scb_location_map_require_ctrl_zoom']) ? '1' : '0';
        update_option('scb_location_map_require_ctrl_zoom', $require_ctrl_zoom);
        
        // Save Contact Us link and whether to pass params
        if (isset($_POST['scb_location_contact_link'])) {
            $contact_link = esc_url_raw(trim($_POST['scb_location_contact_link']));
            // Fallback to default if empty
            if ($contact_link === '' || $contact_link === false) {
                $contact_link = site_url('/contact-us');
            }
            update_option('scb_location_contact_link', $contact_link);
        }
        $contact_link_pass_params = isset($_POST['scb_location_contact_link_pass_params']) ? '1' : '0';
        update_option('scb_location_contact_link_pass_params', $contact_link_pass_params);
        
        echo '<div class="updated notice"><p>' . esc_html__('Impostazioni aggiornate.', 'cpt-servizi') . '</p></div>';
    }

    $current_value = get_option('scb_location_show_services', '1');
    $current_map_value = get_option('scb_location_map_show_services', '1');
    $current_map_service_filter = get_option('scb_location_map_show_service_filter', '1');
    $current_closest_highlight_mode = get_option('scb_location_closest_highlight_mode', 'pulse');
    $current_closest_highlight_color = get_option('scb_location_closest_highlight_color', '#e74c3c');
    $current_selected_highlight_color = get_option('scb_location_selected_highlight_color', '#1d9bf0');
    $current_closest_zoom_level = absint(get_option('scb_location_closest_zoom_level', 7));
    $current_details_button = get_option('scb_location_show_details_button', '1');
    $current_require_ctrl_zoom = get_option('scb_location_map_require_ctrl_zoom', '1');
    $current_contact_link = get_option('scb_location_contact_link', site_url('/contact-us'));
    $current_contact_link_pass_params = get_option('scb_location_contact_link_pass_params', '1');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Impostazioni Location', 'cpt-servizi'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('scb_save_location_settings', 'scb_location_settings_nonce'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="scb_location_show_services"><?php echo esc_html__('Mostra servizi nelle pagine Location', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_show_services" name="scb_location_show_services" value="1" <?php checked($current_value, '1'); ?> />
                            <?php echo esc_html__('Mostra l\'elenco dei servizi disponibili nelle pagine della Location.', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_map_show_services"><?php echo esc_html__('Mostra servizi nella mappa', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_map_show_services" name="scb_location_map_show_services" value="1" <?php checked($current_map_value, '1'); ?> />
                            <?php echo esc_html__('Mostra la sezione con i servizi disponibili nel pannello dettagli quando selezioni una Location sulla mappa.', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_map_show_service_filter"><?php echo esc_html__('Mostra tendina servizi disponibili nella mappa', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_map_show_service_filter" name="scb_location_map_show_service_filter" value="1" <?php checked($current_map_service_filter, '1'); ?> />
                            <?php echo esc_html__('Mostra la tendina per filtrare la mappa per categoria servizio. Se disattivata, la mappa mostra tutte le location indipendentemente dai servizi offerti.', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_closest_highlight_mode"><?php echo esc_html__('Modalita evidenziazione location piu vicina', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <select id="scb_location_closest_highlight_mode" name="scb_location_closest_highlight_mode">
                            <option value="pulse" <?php selected($current_closest_highlight_mode, 'pulse'); ?>><?php echo esc_html__('Pulse', 'cpt-servizi'); ?></option>
                            <option value="ring" <?php selected($current_closest_highlight_mode, 'ring'); ?>><?php echo esc_html__('Anello', 'cpt-servizi'); ?></option>
                            <option value="pin" <?php selected($current_closest_highlight_mode, 'pin'); ?>><?php echo esc_html__('Marker standard colorato', 'cpt-servizi'); ?></option>
                        </select>
                        <p class="description"><?php echo esc_html__('Scegli come evidenziare sulla mappa la location piu vicina all utente. La modalita Marker standard colorato usa un pin dello stesso stile della mappa e non pulsa.', 'cpt-servizi'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_closest_highlight_color"><?php echo esc_html__('Colore evidenziazione location piu vicina', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="scb_location_closest_highlight_color" name="scb_location_closest_highlight_color" value="<?php echo esc_attr($current_closest_highlight_color); ?>" />
                        <p class="description"><?php echo esc_html__('Imposta il colore usato per evidenziare il marker della location piu vicina.', 'cpt-servizi'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_selected_highlight_color"><?php echo esc_html__('Colore evidenziazione marker cliccato', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="scb_location_selected_highlight_color" name="scb_location_selected_highlight_color" value="<?php echo esc_attr($current_selected_highlight_color); ?>" />
                        <p class="description"><?php echo esc_html__('Imposta il colore usato per evidenziare il marker della location selezionata con click.', 'cpt-servizi'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_closest_zoom_level"><?php echo esc_html__('Zoom iniziale del porto piu vicino', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <input type="number" min="2" max="12" step="1" id="scb_location_closest_zoom_level" name="scb_location_closest_zoom_level" value="<?php echo esc_attr($current_closest_zoom_level); ?>" class="small-text" />
                        <p class="description"><?php echo esc_html__('Controlla quanto la mappa deve avvicinarsi al porto piu vicino al caricamento pagina o dopo la geolocalizzazione.', 'cpt-servizi'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_show_details_button"><?php echo esc_html__('Mostra pulsante "Dettaglio" nella mappa', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_show_details_button" name="scb_location_show_details_button" value="1" <?php checked($current_details_button, '1'); ?> />
                            <?php echo esc_html__('Mostra il pulsante Dettaglio che apre la pagina della Location nel pannello dettagli della mappa.', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_map_require_ctrl_zoom"><?php echo esc_html__('Richiedi Ctrl per zoom nella mappa', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_map_require_ctrl_zoom" name="scb_location_map_require_ctrl_zoom" value="1" <?php checked($current_require_ctrl_zoom, '1'); ?> />
                            <?php echo esc_html__('Se attivo, per zoomare con la rotellina del mouse è necessario tenere premuto Ctrl (o ⌘ su Mac).', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_contact_link"><?php echo esc_html__('URL pulsante "Contact us"', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <input type="url" class="regular-text" id="scb_location_contact_link" name="scb_location_contact_link" value="<?php echo esc_attr($current_contact_link); ?>" placeholder="<?php echo esc_attr(site_url('/contact-us')); ?>" />
                        <p class="description"><?php echo esc_html__('Imposta l\'URL da aprire quando si clicca il pulsante "Contact us" nel pannello dettagli della mappa.', 'cpt-servizi'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="scb_location_contact_link_pass_params"><?php echo esc_html__('Passa location_id e location_name', 'cpt-servizi'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="scb_location_contact_link_pass_params" name="scb_location_contact_link_pass_params" value="1" <?php checked($current_contact_link_pass_params, '1'); ?> />
                            <?php echo esc_html__('Se attivo, l\'URL riceverà i parametri location_id e location_name come query string.', 'cpt-servizi'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Salva modifiche', 'cpt-servizi')); ?>
        </form>
    </div>
    <?php
}
