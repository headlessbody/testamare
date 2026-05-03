<?php
/**
 * Test script for CPT Servizi Styling
 * 
 * This script helps test if the styling settings are working correctly.
 * Upload this file to your WordPress root directory and access it via browser.
 * 
 * IMPORTANT: Delete this file after testing for security reasons.
 */

// Load WordPress
require_once('wp-load.php');

// Set up the header
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test CPT Servizi Styling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .warning {
            color: orange;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 10px;
        }
        .button:hover {
            background: #005177;
        }
        .color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 1px solid #ddd;
            vertical-align: middle;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>Test CPT Servizi Styling</h1>
    
    <div class="section">
        <h2>Styling Settings</h2>
        <?php
        // Get styling settings
        $settings = get_option('scb_servizi_styling', array());
        
        // Check if settings exist
        if (empty($settings)) {
            echo '<p class="warning">No styling settings found. Please configure the styling settings in the WordPress admin.</p>';
            echo '<a href="' . admin_url('edit.php?post_type=servizi&page=scb-servizi-styling') . '" class="button">Go to Styling Settings</a>';
        } else {
            echo '<p class="success">Styling settings found.</p>';
            echo '<a href="' . admin_url('edit.php?post_type=servizi&page=scb-servizi-styling') . '" class="button">Edit Styling Settings</a>';
            
            // Display settings
            echo '<h3>Current Settings</h3>';
            echo '<table>';
            echo '<tr><th>Setting</th><th>Value</th></tr>';
            
            // General settings
            echo '<tr><td colspan="2"><strong>General Settings</strong></td></tr>';
            if (isset($settings['primary_color'])) {
                echo '<tr><td>Primary Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['primary_color']) . '"></span>' . esc_html($settings['primary_color']) . '</td></tr>';
            }
            if (isset($settings['secondary_color'])) {
                echo '<tr><td>Secondary Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['secondary_color']) . '"></span>' . esc_html($settings['secondary_color']) . '</td></tr>';
            }
            if (isset($settings['text_color'])) {
                echo '<tr><td>Text Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['text_color']) . '"></span>' . esc_html($settings['text_color']) . '</td></tr>';
            }
            if (isset($settings['background_color'])) {
                echo '<tr><td>Background Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['background_color']) . '"></span>' . esc_html($settings['background_color']) . '</td></tr>';
            }
            if (isset($settings['border_color'])) {
                echo '<tr><td>Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['border_color']) . '"></span>' . esc_html($settings['border_color']) . '</td></tr>';
            }
            if (isset($settings['border_radius'])) {
                echo '<tr><td>Border Radius</td><td>' . esc_html($settings['border_radius']) . 'px</td></tr>';
            }
            if (isset($settings['font_size'])) {
                echo '<tr><td>Font Size</td><td>' . esc_html($settings['font_size']) . 'px</td></tr>';
            }
            if (isset($settings['line_height'])) {
                echo '<tr><td>Line Height</td><td>' . esc_html($settings['line_height']) . '</td></tr>';
            }
            
            // Archive settings
            echo '<tr><td colspan="2"><strong>Archive Settings</strong></td></tr>';
            if (isset($settings['archive_grid_gap'])) {
                echo '<tr><td>Grid Gap</td><td>' . esc_html($settings['archive_grid_gap']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_border_width'])) {
                echo '<tr><td>Border Width</td><td>' . esc_html($settings['archive_item_border_width']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_border_radius'])) {
                echo '<tr><td>Border Radius</td><td>' . esc_html($settings['archive_item_border_radius']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_box_shadow'])) {
                echo '<tr><td>Box Shadow</td><td>' . esc_html($settings['archive_item_box_shadow']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_image_height'])) {
                echo '<tr><td>Image Height</td><td>' . esc_html($settings['archive_item_image_height']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_title_font_size'])) {
                echo '<tr><td>Title Font Size</td><td>' . esc_html($settings['archive_item_title_font_size']) . 'px</td></tr>';
            }
            if (isset($settings['archive_item_title_color'])) {
                echo '<tr><td>Title Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['archive_item_title_color']) . '"></span>' . esc_html($settings['archive_item_title_color']) . '</td></tr>';
            }
            if (isset($settings['archive_item_meta_color'])) {
                echo '<tr><td>Meta Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['archive_item_meta_color']) . '"></span>' . esc_html($settings['archive_item_meta_color']) . '</td></tr>';
            }
            if (isset($settings['archive_item_button_bg'])) {
                echo '<tr><td>Button Background</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['archive_item_button_bg']) . '"></span>' . esc_html($settings['archive_item_button_bg']) . '</td></tr>';
            }
            if (isset($settings['archive_item_button_text'])) {
                echo '<tr><td>Button Text Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['archive_item_button_text']) . '"></span>' . esc_html($settings['archive_item_button_text']) . '</td></tr>';
            }
            
            // Single settings
            echo '<tr><td colspan="2"><strong>Single Page Settings</strong></td></tr>';
            if (isset($settings['single_top_padding'])) {
                echo '<tr><td>Top Padding</td><td>' . esc_html($settings['single_top_padding']) . 'px</td></tr>';
            }
            if (isset($settings['single_title_font_size'])) {
                echo '<tr><td>Title Font Size</td><td>' . esc_html($settings['single_title_font_size']) . 'px</td></tr>';
            }
            if (isset($settings['single_title_color'])) {
                echo '<tr><td>Title Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_title_color']) . '"></span>' . esc_html($settings['single_title_color']) . '</td></tr>';
            }
            if (isset($settings['single_content_font_size'])) {
                echo '<tr><td>Content Font Size</td><td>' . esc_html($settings['single_content_font_size']) . 'px</td></tr>';
            }
            if (isset($settings['single_content_line_height'])) {
                echo '<tr><td>Content Line Height</td><td>' . esc_html($settings['single_content_line_height']) . '</td></tr>';
            }
            if (isset($settings['single_sidebar_bg'])) {
                echo '<tr><td>Sidebar Background</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_sidebar_bg']) . '"></span>' . esc_html($settings['single_sidebar_bg']) . '</td></tr>';
            }
            if (isset($settings['single_sidebar_border'])) {
                echo '<tr><td>Sidebar Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_sidebar_border']) . '"></span>' . esc_html($settings['single_sidebar_border']) . '</td></tr>';
            }
            if (isset($settings['single_sidebar_radius'])) {
                echo '<tr><td>Sidebar Border Radius</td><td>' . esc_html($settings['single_sidebar_radius']) . 'px</td></tr>';
            }
            if (isset($settings['single_sidebar_heading'])) {
                echo '<tr><td>Sidebar Heading Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_sidebar_heading']) . '"></span>' . esc_html($settings['single_sidebar_heading']) . '</td></tr>';
            }
            if (isset($settings['single_link_color'])) {
                echo '<tr><td>Link Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_link_color']) . '"></span>' . esc_html($settings['single_link_color']) . '</td></tr>';
            }
            if (isset($settings['single_link_hover'])) {
                echo '<tr><td>Link Hover Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['single_link_hover']) . '"></span>' . esc_html($settings['single_link_hover']) . '</td></tr>';
            }
            if (isset($settings['single_image_radius'])) {
                echo '<tr><td>Image Border Radius</td><td>' . esc_html($settings['single_image_radius']) . 'px</td></tr>';
            }
            
            // Map settings
            echo '<tr><td colspan="2"><strong>Map Settings</strong></td></tr>';
            if (isset($settings['map_height'])) {
                echo '<tr><td>Map Height</td><td>' . esc_html($settings['map_height']) . 'px</td></tr>';
            }
            if (isset($settings['map_width_ratio'])) {
                echo '<tr><td>Map Width Ratio</td><td>' . esc_html($settings['map_width_ratio']) . '%</td></tr>';
            }
            if (isset($settings['map_border_color'])) {
                echo '<tr><td>Map Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_border_color']) . '"></span>' . esc_html($settings['map_border_color']) . '</td></tr>';
            }
            if (isset($settings['map_border_radius'])) {
                echo '<tr><td>Map Border Radius</td><td>' . esc_html($settings['map_border_radius']) . 'px</td></tr>';
            }
            if (isset($settings['map_sidebar_bg'])) {
                echo '<tr><td>Sidebar Background</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_sidebar_bg']) . '"></span>' . esc_html($settings['map_sidebar_bg']) . '</td></tr>';
            }
            if (isset($settings['map_sidebar_border'])) {
                echo '<tr><td>Sidebar Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_sidebar_border']) . '"></span>' . esc_html($settings['map_sidebar_border']) . '</td></tr>';
            }
            if (isset($settings['map_sidebar_radius'])) {
                echo '<tr><td>Sidebar Border Radius</td><td>' . esc_html($settings['map_sidebar_radius']) . 'px</td></tr>';
            }
            if (isset($settings['map_location_title_color'])) {
                echo '<tr><td>Location Title Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_location_title_color']) . '"></span>' . esc_html($settings['map_location_title_color']) . '</td></tr>';
            }
            if (isset($settings['map_location_title_border'])) {
                echo '<tr><td>Location Title Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_location_title_border']) . '"></span>' . esc_html($settings['map_location_title_border']) . '</td></tr>';
            }
            if (isset($settings['map_service_title_color'])) {
                echo '<tr><td>Service Title Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_service_title_color']) . '"></span>' . esc_html($settings['map_service_title_color']) . '</td></tr>';
            }
            if (isset($settings['map_button_bg'])) {
                echo '<tr><td>Button Background</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_button_bg']) . '"></span>' . esc_html($settings['map_button_bg']) . '</td></tr>';
            }
            if (isset($settings['map_button_text'])) {
                echo '<tr><td>Button Text Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_button_text']) . '"></span>' . esc_html($settings['map_button_text']) . '</td></tr>';
            }
            if (isset($settings['map_filter_border'])) {
                echo '<tr><td>Filter Border Color</td><td><span class="color-preview" style="background-color: ' . esc_attr($settings['map_filter_border']) . '"></span>' . esc_html($settings['map_filter_border']) . '</td></tr>';
            }
            
            echo '</table>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Test Pages</h2>
        <p>Visit these pages to see the styling in action:</p>
        <ul>
            <?php
            // Get archive URLs
            $servizi_archive_url = get_post_type_archive_link('servizi');
            $location_archive_url = get_post_type_archive_link('location');
            
            // Get a single servizi and location
            $servizi_query = new WP_Query(array(
                'post_type' => 'servizi',
                'posts_per_page' => 1,
                'post_status' => 'publish'
            ));
            
            $location_query = new WP_Query(array(
                'post_type' => 'location',
                'posts_per_page' => 1,
                'post_status' => 'publish'
            ));
            
            // Display links
            if ($servizi_archive_url) {
                echo '<li><a href="' . esc_url($servizi_archive_url) . '" target="_blank">Servizi Archive</a></li>';
            }
            
            if ($location_archive_url) {
                echo '<li><a href="' . esc_url($location_archive_url) . '" target="_blank">Location Archive</a></li>';
            }
            
            if ($servizi_query->have_posts()) {
                $servizi_query->the_post();
                echo '<li><a href="' . get_permalink() . '" target="_blank">Single Servizio: ' . get_the_title() . '</a></li>';
                wp_reset_postdata();
            }
            
            if ($location_query->have_posts()) {
                $location_query->the_post();
                echo '<li><a href="' . get_permalink() . '" target="_blank">Single Location: ' . get_the_title() . '</a></li>';
                wp_reset_postdata();
            }
            
            // Get a page with the map shortcode
            $map_query = new WP_Query(array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                's' => '[servizi_map'
            ));
            
            if ($map_query->have_posts()) {
                while ($map_query->have_posts()) {
                    $map_query->the_post();
                    echo '<li><a href="' . get_permalink() . '" target="_blank">Map Page: ' . get_the_title() . '</a></li>';
                }
                wp_reset_postdata();
            } else {
                echo '<li class="warning">No pages with [servizi_map] shortcode found. Create a page with this shortcode to test map styling.</li>';
            }
            ?>
        </ul>
    </div>
    
    <div class="section">
        <h2>Instructions</h2>
        <ol>
            <li>Go to the <a href="<?php echo admin_url('edit.php?post_type=servizi&page=scb-servizi-styling'); ?>">Styling Settings</a> page to customize the appearance of the plugin.</li>
            <li>After saving your settings, visit the test pages listed above to see the changes in action.</li>
            <li>If you don't see any changes, try clearing your browser cache or using a private/incognito window.</li>
            <li>If you still don't see any changes, check your browser's developer tools to see if there are any errors.</li>
        </ol>
        <p><strong>Important:</strong> Delete this test file after use for security reasons.</p>
    </div>
</body>
</html>