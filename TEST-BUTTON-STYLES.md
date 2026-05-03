# Testing Button Style Consistency

## Overview

This document provides instructions for testing the changes made to ensure the service details button has the same style as the location details button in the info box next to the map.

## Testing Environment

To test these changes, you'll need:

1. A web browser (Chrome, Firefox, Safari, or Edge)
2. A WordPress installation with the plugin activated
3. A page with the map shortcode `[servizi_map]` embedded

## Testing Steps

### 1. Visual Comparison

1. Navigate to a page with the map shortcode
2. Click on a location marker to display the location details in the info box
3. Visually compare the location details button and the service details button
4. Verify that both buttons have:
   - The same padding (5px 10px)
   - The same font size (14px)
   - The same background color (#0073aa)
   - The same text color (white)
   - The same border radius (3px)

### 2. Hover Effects

1. Hover over the location details button and observe the hover effect
2. Hover over the service details button and observe the hover effect
3. Verify that both buttons have the same hover effect:
   - Background color changes to #005177
   - Text color remains white
   - No text decoration (underline) appears

### 3. Mobile Testing

1. Use browser developer tools to simulate mobile devices (or test on actual mobile devices)
2. Test on tablet size (768px - 992px width):
   - Verify both buttons have increased padding (10px 16px)
   - Verify both buttons have the same appearance
3. Test on mobile size (below 480px width):
   - Verify both buttons are full width (display: block)
   - Verify both buttons are center-aligned
   - Verify both buttons have the same appearance

### 4. Cross-Browser Testing

Test the buttons in different browsers to ensure consistent appearance:
- Chrome
- Firefox
- Safari
- Edge

## Expected Results

- Both buttons should have identical styling in all scenarios
- The service details button should now match the location details button style
- Both buttons should respond to hover events in the same way
- Both buttons should adapt to mobile views consistently

## Reporting Issues

If any inconsistencies are found during testing, please document:
1. The specific difference observed
2. The browser and device used for testing
3. Steps to reproduce the issue
4. Screenshots showing the inconsistency

## Conclusion

These tests will verify that the service details button now has the same style as the location details button, providing a more consistent and professional user interface in the info box next to the map.