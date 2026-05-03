# Translation Changes

## Overview
This document summarizes the changes made to translate Italian phrases to English in the single service page.

## Changes Made

### Main Plugin Directory (cpt-servizi.php)

1. Line 1767: Changed `__('Categorie: ', 'cpt-servizi')` to `__('Categories: ', 'cpt-servizi')`
2. Line 1975: Changed `__('Categorie', 'cpt-servizi')` to `__('Categories', 'cpt-servizi')`
3. Line 2012: Changed `__('Disponibile in queste location', 'cpt-servizi')` to `__('Available in these locations', 'cpt-servizi')`

### "cpt servizi" Subdirectory (cpt servizi/cpt-servizi.php)

1. Line 1407: Changed `__('Categorie: ', 'cpt-servizi')` to `__('Categories: ', 'cpt-servizi')`
2. Line 1615: Changed `__('Categorie', 'cpt-servizi')` to `__('Categories', 'cpt-servizi')`
3. Line 1652: Changed `__('Disponibile in queste location', 'cpt-servizi')` to `__('Available in these locations', 'cpt-servizi')`

## How WordPress Translation Functions Work

The changes leverage WordPress's translation functions to provide English text for the single service page:

- `__()` is a WordPress function that allows for text translation
- The first parameter is the text to be translated
- The second parameter ('cpt-servizi') is the text domain, which identifies the plugin or theme for translation purposes

By changing the default text from Italian to English, we ensure that the phrases appear in English on the single service page. If a translation file for another language is available, WordPress will use that translation instead.

## Additional Notes

- These changes only affect the display text; they do not change any functionality
- The changes have been made in both plugin directories to ensure consistency
- If additional translations are needed in the future, the same approach can be used