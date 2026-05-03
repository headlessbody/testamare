# Permalink Fix for Service Detail Links

## Issue Description

The "details" button that should link to the service detail page doesn't work correctly. Even though the service exists, the link doesn't work (e.g., https://francescoc571.sg-host.com/servizi/servizio-1/).

## Cause of the Issue

This issue is related to WordPress permalink structure and rewrite rules. When custom post types are registered, WordPress needs to update its internal rewrite rules to properly handle the URLs for these custom post types. Sometimes these rules don't get updated correctly, which can cause links to custom post type pages to break.

## Solution

A script has been created to manually flush the rewrite rules and fix the permalink structure. This script:

1. Re-registers the post types and taxonomies
2. Flushes the rewrite rules
3. Displays a success message

## How to Use the Fix

1. Upload the `flush-permalinks.php` script to your server in the plugin directory
2. Access the script through your browser by navigating to:
   ```
   https://your-website.com/wp-content/plugins/cpt-servizi/flush-permalinks.php
   ```
3. You should see a success message indicating that the permalinks have been updated
4. Test the service detail links to verify they now work correctly

## Alternative Methods

If the script doesn't work, you can also try:

1. **Using the Admin Menu**: Navigate to Settings > Permalinks in your WordPress admin dashboard and click "Save Changes" without making any changes. This will also flush the rewrite rules.

2. **Using the Plugin's Built-in Tool**: The plugin includes a built-in tool to update permalinks. Look for "Update Permalinks" in the plugin's admin menu.

## Prevention

To prevent this issue from occurring again:

1. Always flush rewrite rules after activating or updating the plugin
2. If you make changes to the permalink structure in WordPress settings, make sure to update the permalinks

## Technical Details

The issue occurs because WordPress caches rewrite rules for performance reasons. When custom post types are registered, these rules need to be regenerated. The `flush_rewrite_rules()` function in WordPress handles this regeneration.

The script we've created calls this function after ensuring all post types and taxonomies are properly registered, which ensures that WordPress knows how to properly handle URLs for the custom post types.