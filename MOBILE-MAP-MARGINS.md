# Mobile Map Margins

## Overview

This document describes the changes made to add 30-pixel margins to the map container on mobile devices.

## Changes Made

The following changes were made to both copies of the CSS file:

1. Added 30px left and right margins to the map container for medium-sized mobile devices (max-width: 768px)
2. Added 30px left and right margins to the map container for small mobile devices (max-width: 480px)
3. Adjusted the width of the map container to account for the margins using `calc(100% - 60px)`

### CSS Changes

```css
/* For medium-sized mobile devices */
@media (max-width: 768px) {
    #scb-servizi-map {
        min-height: 400px;
        margin-left: 30px; /* Add 30px left margin on mobile */
        margin-right: 30px; /* Add 30px right margin on mobile */
        width: calc(100% - 60px); /* Adjust width to account for margins */
    }
}

/* For small mobile devices */
@media (max-width: 480px) {
    #scb-servizi-map {
        min-height: 300px;
        margin-left: 30px; /* Add 30px left margin on small mobile */
        margin-right: 30px; /* Add 30px right margin on small mobile */
        width: calc(100% - 60px); /* Adjust width to account for margins */
    }
}
```

## Files Modified

1. `/Volumes/Mac mini SCB Ext/SCB-m4-EXT/PhpstormProjects/GRAZIANO DEV - PLUGIN/cpt servizi/css/servizi-map.css`
2. `/Volumes/Mac mini SCB Ext/SCB-m4-EXT/PhpstormProjects/GRAZIANO DEV - PLUGIN/cpt servizi/cpt servizi/css/servizi-map.css`

## Purpose

These changes ensure that the map with markers has 30 pixels of margin on the right and left sides when viewed on mobile devices. This improves the mobile user experience by:

1. Preventing the map from extending to the edges of the screen
2. Making it easier to interact with the map on touch devices
3. Creating a more visually appealing layout with consistent spacing

## Testing

To test these changes:

1. View the map on a mobile device or use browser developer tools to simulate mobile screen sizes
2. Verify that the map has 30px margins on both the left and right sides
3. Check that the map is properly centered and doesn't overflow the screen
4. Test on various mobile screen sizes to ensure the margins are consistent

## Related Changes

These changes build upon previous mobile optimizations, including:

1. Auto-scrolling to location details when a marker is clicked
2. Improved touch targets for better mobile interaction
3. Enhanced readability and layout for mobile screens