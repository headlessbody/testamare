# Mobile Optimization for Map and Location Details

## Overview

This document describes the mobile optimization changes made to improve the user experience on mobile devices when using the map and location details functionality.

## Changes Made

### 1. Auto-Scrolling to Location Details

When a user clicks on a location marker on the map while on a mobile device (screen width ≤ 992px), the page now automatically scrolls to the location details section. This ensures that users can immediately see the location information without having to manually scroll down.

**Implementation:**
- Added code in the `displayLocationDetails` function in both `servizi-map.js` files to detect mobile devices and scroll to the location details section
- Used jQuery's animate function for smooth scrolling behavior
- Added a small delay to ensure content is fully rendered before scrolling

### 2. Improved Touch Targets

Increased the size of interactive elements to make them easier to tap on touch screens:

- Larger form elements (select dropdowns, buttons)
- Increased padding for buttons and links
- Full-width buttons on mobile screens
- Improved spacing between interactive elements

### 3. Enhanced Readability

Optimized text and content display for better readability on small screens:

- Adjusted font sizes for different screen sizes
- Improved spacing and padding around content
- Optimized the location image aspect ratio for better visibility on mobile
- Adjusted padding for title and metadata overlays

### 4. Responsive Layout Improvements

Enhanced the responsive layout for different screen sizes:

- **Tablets and Large Mobile (≤ 992px):**
  - Stack layout (map above, details below)
  - Full width for map and details
  - Adjusted heights for better viewing

- **Medium Mobile (≤ 768px):**
  - Vertical layout for filters
  - Larger touch targets
  - Improved service item display

- **Small Mobile (≤ 480px):**
  - Further optimized spacing and sizing
  - Full-width buttons with centered text
  - Compact metadata display

## Breakpoints Used

The mobile optimizations use the following breakpoints:

- **992px:** Tablet and mobile devices (stacked layout)
- **768px:** Medium-sized mobile devices
- **480px:** Small mobile devices

## Testing

To test the mobile optimizations:

1. Use browser developer tools to simulate different mobile device sizes
2. Verify that auto-scrolling works when clicking on a marker in mobile view
3. Check that all interactive elements are easy to tap
4. Ensure text is readable and content is well-spaced
5. Test on actual mobile devices if possible

## Future Maintenance

When making changes to the map or location details functionality:

1. Maintain the mobile detection logic in the `displayLocationDetails` function
2. Keep the breakpoints consistent across CSS and JavaScript
3. Ensure touch targets remain large enough for comfortable interaction
4. Test any changes on multiple screen sizes