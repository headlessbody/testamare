<?php
/**
 * Test script for CPT Servizi and Location pages
 * 
 * This script helps test if the CPT pages are accessible.
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
    <title>Test CPT Servizi Pages</title>
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
    </style>
</head>
<body>
    <h1>Test CPT Servizi Pages</h1>
    
    <div class="section">
        <h2>Permalink Structure</h2>
        <p>Current permalink structure: <strong><?php echo get_option('permalink_structure'); ?></strong></p>
        <p>
            <?php if (get_option('permalink_structure')) : ?>
                <span class="success">✓ Pretty permalinks are enabled.</span>
            <?php else : ?>
                <span class="error">✗ Pretty permalinks are not enabled. This may cause issues with CPT pages.</span>
                <br>
                <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button">Configure Permalinks</a>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="section">
        <h2>Servizi CPT</h2>
        <?php
        $servizi_args = array(
            'post_type' => 'servizi',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        );
        $servizi_query = new WP_Query($servizi_args);
        ?>
        
        <h3>Archive Page</h3>
        <p>
            <?php 
            $archive_url = get_post_type_archive_link('servizi');
            if ($archive_url) : 
            ?>
                <a href="<?php echo esc_url($archive_url); ?>" target="_blank">View Servizi Archive</a>
            <?php else : ?>
                <span class="error">✗ Could not generate archive URL for Servizi.</span>
            <?php endif; ?>
        </p>
        
        <h3>Recent Servizi (<?php echo $servizi_query->found_posts; ?> total)</h3>
        <?php if ($servizi_query->have_posts()) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Permalink</th>
                    <th>Status</th>
                </tr>
                <?php while ($servizi_query->have_posts()) : $servizi_query->the_post(); ?>
                    <tr>
                        <td><?php the_ID(); ?></td>
                        <td><?php the_title(); ?></td>
                        <td>
                            <a href="<?php the_permalink(); ?>" target="_blank"><?php the_permalink(); ?></a>
                        </td>
                        <td>
                            <?php 
                            $response = wp_remote_head(get_permalink());
                            if (!is_wp_error($response)) {
                                $status_code = wp_remote_retrieve_response_code($response);
                                if ($status_code == 200) {
                                    echo '<span class="success">✓ Page accessible (200 OK)</span>';
                                } else {
                                    echo '<span class="error">✗ Error: ' . $status_code . '</span>';
                                }
                            } else {
                                echo '<span class="error">✗ Error checking page</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p class="warning">No Servizi found. Create some Servizi first.</p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    
    <div class="section">
        <h2>Location CPT</h2>
        <?php
        $location_args = array(
            'post_type' => 'location',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        );
        $location_query = new WP_Query($location_args);
        ?>
        
        <h3>Archive Page</h3>
        <p>
            <?php 
            $archive_url = get_post_type_archive_link('location');
            if ($archive_url) : 
            ?>
                <a href="<?php echo esc_url($archive_url); ?>" target="_blank">View Location Archive</a>
            <?php else : ?>
                <span class="error">✗ Could not generate archive URL for Location.</span>
            <?php endif; ?>
        </p>
        
        <h3>Recent Locations (<?php echo $location_query->found_posts; ?> total)</h3>
        <?php if ($location_query->have_posts()) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Permalink</th>
                    <th>Status</th>
                </tr>
                <?php while ($location_query->have_posts()) : $location_query->the_post(); ?>
                    <tr>
                        <td><?php the_ID(); ?></td>
                        <td><?php the_title(); ?></td>
                        <td>
                            <a href="<?php the_permalink(); ?>" target="_blank"><?php the_permalink(); ?></a>
                        </td>
                        <td>
                            <?php 
                            $response = wp_remote_head(get_permalink());
                            if (!is_wp_error($response)) {
                                $status_code = wp_remote_retrieve_response_code($response);
                                if ($status_code == 200) {
                                    echo '<span class="success">✓ Page accessible (200 OK)</span>';
                                } else {
                                    echo '<span class="error">✗ Error: ' . $status_code . '</span>';
                                }
                            } else {
                                echo '<span class="error">✗ Error checking page</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p class="warning">No Locations found. Create some Locations first.</p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    
    <div class="section">
        <h2>Rewrite Rules</h2>
        <p>
            If you're experiencing issues with CPT pages, try flushing the rewrite rules:
            <a href="<?php echo esc_url(add_query_arg('scb_flush_rules', 'true', admin_url())); ?>" class="button">Flush Rewrite Rules</a>
        </p>
    </div>
    
    <div class="section">
        <h2>Instructions</h2>
        <ol>
            <li>Make sure pretty permalinks are enabled (Settings > Permalinks).</li>
            <li>If CPT pages are not accessible, click the "Flush Rewrite Rules" button above.</li>
            <li>Check if the CPT pages are now accessible by clicking on the permalinks in the tables above.</li>
            <li>If issues persist, check your theme's template files or contact the plugin developer.</li>
        </ol>
        <p><strong>Important:</strong> Delete this test file after use for security reasons.</p>
    </div>
</body>
</html>