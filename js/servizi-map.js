/**
 * Servizi Map JavaScript
 * Handles the map initialization, marker display, and AJAX filtering
 * 
 * Map zoom constraints:
 * - minZoom: 2 - Prevents zooming out too far to avoid seeing duplicated continents
 * - Enforced via both initialization options and zoomend event listener
 */

(function($) {
    'use strict';

    const VERSION = '1.2.5';
    console.log('%c SCB Servizi Map JS v' + VERSION + ' LOADING... ', 'background: #007bff; color: #ffffff; font-size: 24px; font-weight: bold;');
    window.scb_map_js_loaded = VERSION;

    // Check for debug div
    const debugDiv = document.getElementById('scb-debug-info');
    if (debugDiv) {
        debugDiv.innerHTML += '<br>JS File Executing (v' + VERSION + ')';
    }

    // Map instance
    let map;
    
    // Markers layer group
    let markersLayer;
    
    // Currently selected marker
    let selectedMarker;
    
    // Currently selected location ID
    let selectedLocationId;

    function getDefaultMarkerIcon() {
        return new L.Icon.Default();
    }

    function getClosestZoomLevel() {
        const vars = (typeof scbServiziMapVars !== 'undefined' && scbServiziMapVars) ? scbServiziMapVars : {};
        const zoom = parseInt(vars.closest_zoom_level, 10);

        if (!isNaN(zoom) && zoom >= 2 && zoom <= 12) {
            return zoom;
        }

        return 7;
    }

    function getMarkerHighlightSettings(type) {
        const vars = (typeof scbServiziMapVars !== 'undefined' && scbServiziMapVars) ? scbServiziMapVars : {};
        const allowedModes = ['pin', 'ring', 'pulse'];
        const mode = allowedModes.indexOf(vars.closest_highlight_mode) !== -1 ? vars.closest_highlight_mode : 'pulse';
        let color = vars.closest_highlight_color || '#e74c3c';

        if (type === 'selected') {
            color = vars.selected_highlight_color || '#1d9bf0';
        }

        return {
            mode: mode,
            color: color
        };
    }

    function getHighlightMarkerIcon(type) {
        const settings = getMarkerHighlightSettings(type);
        const isPinMode = settings.mode === 'pin';
        const html = isPinMode
            ? '<div class="scb-closest-marker scb-closest-marker--pin scb-closest-marker--' + type + '" style="--scb-closest-marker-color:' + settings.color + '; color:' + settings.color + ';"><svg class="scb-closest-marker-pin" viewBox="0 0 25 41" aria-hidden="true"><path class="scb-closest-marker-pin-body" d="M12.5 0C5.6 0 0 5.6 0 12.5c0 9.2 12.5 28.5 12.5 28.5S25 21.7 25 12.5C25 5.6 19.4 0 12.5 0z"></path><circle class="scb-closest-marker-pin-core" cx="12.5" cy="12.5" r="5.5"></circle></svg></div>'
            : '<div class="scb-closest-marker scb-closest-marker--' + settings.mode + ' scb-closest-marker--' + type + '" style="--scb-closest-marker-color:' + settings.color + ';"><span class="scb-closest-marker-core"></span></div>';

        return L.divIcon({
            className: 'scb-closest-marker-wrapper',
            html: html,
            iconSize: isPinMode ? [25, 41] : [28, 28],
            iconAnchor: isPinMode ? [12, 41] : [14, 14],
            popupAnchor: isPinMode ? [1, -34] : [0, -14]
        });
    }

    function applyBaseMarkerIcon(marker) {
        if (!marker) {
            return;
        }

        if (marker.isSelectedMarker === true) {
            marker.setIcon(getHighlightMarkerIcon('selected'));
        } else if (marker.isClosestMarker === true) {
            marker.setIcon(getHighlightMarkerIcon('closest'));
        } else {
            marker.setIcon(getDefaultMarkerIcon());
        }
    }

    function isMobileDeviceContext() {
        if (typeof window !== 'undefined' && typeof window.matchMedia === 'function') {
            if (window.matchMedia('(max-width: 992px)').matches || window.matchMedia('(pointer: coarse)').matches) {
                return true;
            }
        }

        const ua = (typeof navigator !== 'undefined' && navigator.userAgent) ? navigator.userAgent : '';
        return /Android|iPhone|iPad|iPod|Mobile/i.test(ua);
    }

    function isCellularConnection() {
        if (typeof navigator === 'undefined') {
            return false;
        }

        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (!connection) {
            return false;
        }

        return connection.type === 'cellular';
    }

    function shouldPreferDeviceGeolocation() {
        return isMobileDeviceContext() || isCellularConnection();
    }

    function buildBrowserLocation(position, strategy) {
        return {
            latitude: position.coords.latitude.toString(),
            longitude: position.coords.longitude.toString(),
            accuracy: typeof position.coords.accuracy === 'number' ? position.coords.accuracy : null,
            provider: 'browser-gps',
            strategy: strategy
        };
    }

    function requestBrowserPosition(options, strategy, onSuccess, onError) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                console.log(
                    'SUCCESS: Browser geolocation obtained via',
                    strategy + ':',
                    position.coords.latitude,
                    position.coords.longitude,
                    '(accuracy:',
                    position.coords.accuracy,
                    'meters)'
                );
                onSuccess(buildBrowserLocation(position, strategy));
            },
            function(error) {
                console.warn('Browser geolocation failed via', strategy + ':', error.code, error.message);
                onError(error);
            },
            options
        );
    }

    // Hard-disable any legacy client cache entry to avoid stale cross-network behavior.
    sessionStorage.removeItem('scb_user_geolocation');
    
    /**
     * Handle AJAX geolocation to avoid caching issues
     */
    function handleAJAXGeolocation() {
        console.group('SCB Geolocation Debug');
        console.log('Starting Geolocation Process (v1.2.0)...');
        
        // 0. Check Protocol (Security)
        if (window.location.protocol !== 'https:') {
            console.error('CRITICAL: Geolocation API (GPS) is disabled by the browser on non-HTTPS sites.');
            console.log('Falling back to IP Geolocation immediately.');
            performIPGeolocation();
            return;
        }

        // 1. Try Browser Geolocation API first (GPS/WiFi)
        if ("geolocation" in navigator) {
            const preferDeviceGeolocation = shouldPreferDeviceGeolocation();
            const highAccuracyTimeout = preferDeviceGeolocation ? 20000 : 12000;
            const balancedTimeout = preferDeviceGeolocation ? 12000 : 8000;

            console.log('ALERT: REQUESTING DEVICE GEOLOCATION FROM BROWSER...');
            console.log('Mobile/cellular context detected:', preferDeviceGeolocation);

            requestBrowserPosition(
                {
                    enableHighAccuracy: true,
                    timeout: highAccuracyTimeout,
                    maxAge: 0
                },
                'high-accuracy',
                function(gpsLocation) {
                    if (scbServiziMapData) {
                        scbServiziMapData.user_location = gpsLocation;
                    }

                    processUserLocation(gpsLocation);
                    console.groupEnd();
                },
                function(error) {
                    if (error.code === error.PERMISSION_DENIED) {
                        console.warn('Browser geolocation permission denied.');
                        console.log('Falling back to IP Geolocation as last resort...');
                        performIPGeolocation();
                        return;
                    }

                    console.log('Retrying browser geolocation with balanced accuracy settings...');
                    requestBrowserPosition(
                        {
                            enableHighAccuracy: false,
                            timeout: balancedTimeout,
                            maxAge: 300000
                        },
                        'balanced-accuracy',
                        function(gpsLocation) {
                            if (scbServiziMapData) {
                                scbServiziMapData.user_location = gpsLocation;
                            }

                            processUserLocation(gpsLocation);
                            console.groupEnd();
                        },
                        function(secondError) {
                            console.warn('Second browser geolocation attempt failed:', secondError.code, secondError.message);
                            console.log('Falling back to IP Geolocation as last resort...');
                            performIPGeolocation();
                        }
                    );
                }
            );
        } else {
            console.log('Browser Geolocation API not supported. Using IP Geolocation...');
            performIPGeolocation();
        }
    }

    /**
     * Perform IP-based Geolocation (Existing AJAX logic)
     */
    function performIPGeolocation() {
        // Define a clean AJAX URL, bypassing potential TranslatePress interference
        let cleanAjaxUrl = window.location.origin + '/wp-admin/admin-ajax.php';
        
        if (typeof scbServiziMapVars !== 'undefined' && scbServiziMapVars.ajaxurl) {
            const originalUrl = scbServiziMapVars.ajaxurl;
            if (originalUrl.indexOf('trp-ajax.php') !== -1) {
                console.warn('TranslatePress interference detected in original AJAX URL:', originalUrl);
                console.log('Forcing clean AJAX URL:', cleanAjaxUrl);
            } else {
                cleanAjaxUrl = originalUrl;
            }
        }
        
        // Add cache busting and versioning
        const cacheBuster = 'v=1.1.9&t=' + new Date().getTime();
        cleanAjaxUrl += (cleanAjaxUrl.indexOf('?') !== -1 ? '&' : '?') + cacheBuster;
        
        console.log('DEBUG FINAL AJAX URL:', cleanAjaxUrl);

        // Never use browser-side geolocation cache.
        sessionStorage.removeItem('scb_user_geolocation');

        // Fetch current IP geolocation via AJAX on every page load.
        if (typeof scbServiziMapVars === 'undefined' || !scbServiziMapVars.ajaxurl) {
            console.error('CRITICAL: AJAX URL not defined for geolocation. scbServiziMapVars is:', typeof scbServiziMapVars);
            console.groupEnd();
            return;
        }
        
        console.log('Making AJAX request using native FETCH API to bypass jQuery filters...');
        
        const params = new URLSearchParams();
        params.append('action', 'scb_get_user_geolocation');

        fetch(cleanAjaxUrl, {
            method: 'POST',
            cache: 'no-store',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
            body: params
        })
        .then(response => {
            console.log('Fetch response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(response => {
            console.log('Fetch Success Response:', response);
            if (response.success && response.data) {
                const data = response.data;
                if (data.latitude && data.longitude) {
                    data.is_approximate = true;
                    console.log('Valid geolocation received:', data.latitude, data.longitude, 'via provider:', data.provider || 'unknown');
                    
                    // Update global data object
                    if (scbServiziMapData) {
                        scbServiziMapData.user_location = data;
                    }
                    
                    processUserLocation(data);
                } else {
                    console.warn('Geolocation response missing coordinates:', data);
                }
            } else {
                console.error('Server returned success:false or empty data:', response);
            }
        })
        .catch(error => {
            console.error('AJAX Geolocation FETCH FAILED:', error);
        })
        .finally(() => {
            console.groupEnd();
        });
    }

    /**
     * Process the user location (find closest and update map)
     */
    function processUserLocation(userLocation) {
        if (!userLocation || !userLocation.latitude || !userLocation.longitude) {
            console.warn('processUserLocation called with invalid data:', userLocation);
            return;
        }
        
        console.log('Processing User Location:', userLocation.latitude, userLocation.longitude);
        
        if (!window.scbLocationsData || window.scbLocationsData.length === 0) {
            console.warn('No location data available to compare (window.scbLocationsData is empty)');
            return;
        }
        
        const foundClosest = findClosestLocation(window.scbLocationsData, userLocation);
        
        if (foundClosest) {
            console.log('WINNER: Closest location identified as:', foundClosest.title, '(ID: ' + foundClosest.id + ')');
            
            // Wait for markers to be ready
            let attempts = 0;
            const maxAttempts = 10;
            const trySelectingMarker = function() {
                attempts++;
                console.log('Attempting to find marker for winner (Attempt ' + attempts + '/' + maxAttempts + ')...');
                
                if (markersLayer) {
                    let closestMarker = null;
                    markersLayer.eachLayer(function(marker) {
                        if (marker.locationData && marker.locationData.id === foundClosest.id) {
                            closestMarker = marker;
                        }
                    });
                    
                    if (closestMarker) {
                        console.log('SUCCESS: Marker found, triggering click/view.');
                        closestMarker.fire('click', { autoSelected: true });
                        
                        // Center the map on it
                        const lat = parseFloat(foundClosest.latitude);
                        const lng = parseFloat(foundClosest.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            map.setView([lat, lng], getClosestZoomLevel());
                        }
                        return;
                    }
                }
                
                if (attempts < maxAttempts) {
                    setTimeout(trySelectingMarker, 300);
                } else {
                    console.error('FAILED: Marker for ' + foundClosest.title + ' not found after ' + maxAttempts + ' attempts.');
                }
            };
            
            trySelectingMarker();
        } else {
            console.log('NO WINNER: findClosestLocation could not determine a closest point.');
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param {number} lat1 - Latitude of first point in degrees
     * @param {number} lon1 - Longitude of first point in degrees
     * @param {number} lat2 - Latitude of second point in degrees
     * @param {number} lon2 - Longitude of second point in degrees
     * @return {number} Distance in kilometers
     */
    function calculateDistance(lat1, lon1, lat2, lon2) {
        // Convert degrees to radians
        const toRad = function(deg) {
            return deg * Math.PI / 180;
        };
        
        const R = 6371; // Earth's radius in km
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        
        return distance;
    }
    
    /**
     * Find the closest location to the user's position
     * 
     * @param {Array} locations - Array of location objects
     * @param {Object} userLocation - Object with user's latitude and longitude
     * @return {Object|null} The closest location object or null if none found
     */
    function findClosestLocation(locations, userLocation) {
        if (!locations || locations.length === 0 || !userLocation || 
            !userLocation.latitude || !userLocation.longitude) {
            console.log('findClosestLocation: Missing locations or user coordinates.');
            return null;
        }
        
        let closestLocation = null;
        let minDistance = Infinity;
        
        // Convert user coordinates to numbers
        const userLat = parseFloat(userLocation.latitude);
        const userLng = parseFloat(userLocation.longitude);
        
        // Check if user coordinates are valid
        if (isNaN(userLat) || isNaN(userLng)) {
            console.warn(scbServiziMapI18n.errors.invalidUserCoordinates, userLocation);
            return null;
        }
        
        console.log('COMPARING DISTANCES to user at:', userLat, userLng);
        
        // Find the closest location
        locations.forEach(function(location) {
            // Skip invalid locations
            if (!location.latitude || !location.longitude) {
                return;
            }
            
            // Convert location coordinates to numbers
            const locationLat = parseFloat(location.latitude);
            const locationLng = parseFloat(location.longitude);
            
            // Skip locations with invalid coordinates
            if (isNaN(locationLat) || isNaN(locationLng)) {
                return;
            }
            
            // Calculate distance
            const distance = calculateDistance(userLat, userLng, locationLat, locationLng);
            console.log(' - ' + location.title + ': ' + distance.toFixed(2) + ' km');
            
            // Update closest location if this one is closer
            if (distance < minDistance) {
                minDistance = distance;
                closestLocation = location;
            }
        });
        
        if (closestLocation) {
            console.log('RESULT: Closest is', closestLocation.title, 'at', minDistance.toFixed(2), 'km');
        } else {
            console.warn(scbServiziMapI18n.errors.noValidLocationsFound);
        }
        
        return closestLocation;
    }

    // Initialize the map when the document is ready
    $(document).ready(function() {
        // Debug: Log the map data to console
        console.log('Map Data:', scbServiziMapData);
        
        // Check if the map container exists
        const mapContainer = $('#scb-servizi-map');
        if (mapContainer.length === 0) {
            console.error(scbServiziMapI18n.errors.mapContainerNotFound);
            return;
        }
        
        // Log the map container dimensions
        console.log('Map container dimensions:', {
            width: mapContainer.width(),
            height: mapContainer.height(),
            offsetWidth: mapContainer[0].offsetWidth,
            offsetHeight: mapContainer[0].offsetHeight,
            style: {
                width: mapContainer.css('width'),
                height: mapContainer.css('height')
            }
        });
        
        // Initialize locations data
        let locationsData = [];
        
        // Check if we have the new data structure with locations and debug properties
        if (scbServiziMapData && typeof scbServiziMapData === 'object' && scbServiziMapData.locations) {
            // New data structure
            locationsData = scbServiziMapData.locations;
            
            // Log debug information if available
            if (scbServiziMapData.debug) {
                console.log('Debug Info:', scbServiziMapData.debug);
                
                // Log detailed statistics
                console.log('Total servizi posts:', scbServiziMapData.debug.total_servizi);
                console.log('Posts with location:', scbServiziMapData.debug.with_location);
                console.log('Posts with valid coordinates:', scbServiziMapData.debug.with_valid_coordinates);
                console.log('Posts skipped (no location):', scbServiziMapData.debug.skipped_no_location);
                console.log('Posts skipped (invalid coordinates):', scbServiziMapData.debug.skipped_invalid_coordinates);
                console.log('Unique locations:', scbServiziMapData.debug.unique_locations);
                
                // Log location details
                console.log('Locations used:', scbServiziMapData.debug.locations);
            }
        } else if (Array.isArray(scbServiziMapData)) {
            // Old data structure (for backward compatibility)
            locationsData = scbServiziMapData;
            
            // Check for debug information in the old format
            if (locationsData.length > 0 && locationsData[0].debug) {
                console.log('Debug Info (old format):', locationsData[0].debug);
                
                // If this is a debug-only item, remove it from the array
                if (locationsData[0].is_debug_only) {
                    console.log('Debug-only item detected, no valid map data available');
                    locationsData = [];
                }
            }
        } else {
            console.error(scbServiziMapI18n.errors.invalidMapDataFormat, scbServiziMapData);
        }
        
        // Store the locations data for use in other functions
        window.scbLocationsData = locationsData;
        
        // Defer map initialization to improve perceived page load speed
        const deferInit = function(){
            initMap();
            setupEventListeners();
        };
        if (typeof window.requestIdleCallback === 'function') {
            requestIdleCallback(deferInit, { timeout: 500 });
        } else {
            setTimeout(deferInit, 150);
        }
    });

    /**
     * Initialize the Leaflet map
     */
    function initMap() {
        try {
            console.log('Initializing map...');
            
            // Check if Leaflet is available
            if (typeof L === 'undefined') {
                console.error(scbServiziMapI18n.errors.leafletNotLoaded);
                return;
            }
            
            // Check if the map container exists and is visible
            const mapContainer = document.getElementById('scb-servizi-map');
            if (!mapContainer) {
                console.error(scbServiziMapI18n.errors.mapContainerNotInDOM);
                return;
            }
            
            // Log container dimensions again right before map initialization
            console.log('Map container dimensions before initialization:', {
                clientWidth: mapContainer.clientWidth,
                clientHeight: mapContainer.clientHeight,
                offsetWidth: mapContainer.offsetWidth,
                offsetHeight: mapContainer.offsetHeight,
                style: {
                    width: mapContainer.style.width,
                    height: mapContainer.style.height
                }
            });
            
            // Initial view defaults
            let initialView = [20, 0]; // Default center (world view)
            let initialZoom = 4; // Default zoom level
            let closestLocation = null;
            
            // Auto-center logic
            if (scbServiziMapData && scbServiziMapData.user_location && window.scbLocationsData && window.scbLocationsData.length > 0) {
                // If geolocation is disabled, we set the view to the Mediterranean coordinates provided
                if (scbServiziMapData.user_location.is_disabled) {
                    console.log('Geolocation is disabled, showing Mediterranean area');
                    const lat = parseFloat(scbServiziMapData.user_location.latitude);
                    const lng = parseFloat(scbServiziMapData.user_location.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        initialView = [lat, lng];
                        initialZoom = 5; 
                    }
                } else if (scbServiziMapData.user_location.needs_ajax) {
                    console.log('Geolocation needs AJAX, will process after map load');
                    // We'll handle this in a separate function to not block map initialization
                } else {
                    const foundClosest = findClosestLocation(window.scbLocationsData, scbServiziMapData.user_location);
                    if (foundClosest) {
                        const lat = parseFloat(foundClosest.latitude);
                        const lng = parseFloat(foundClosest.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            console.log('Closest location identified for initial selection:', foundClosest.title);
                            closestLocation = foundClosest;
                            initialView = [lat, lng];
                            initialZoom = getClosestZoomLevel();
                        }
                    }
                }
            }
            
            // Create the map with the determined initial view
            console.log('Creating Leaflet map instance...');
            const requireCtrlZoom = (typeof window !== 'undefined' && window.scbServiziMapVars) ? (String(window.scbServiziMapVars.require_ctrl_zoom) === '1') : true;
            map = L.map('scb-servizi-map', {
                scrollWheelZoom: requireCtrlZoom ? false : true, // If requiring Ctrl, disable by default; otherwise enable
                minZoom: 2, // Prevent zooming out too far (to avoid duplicated continents)
                preferCanvas: true, // Faster rendering for many markers
                updateWhenIdle: true // Defer updates until interaction stops
            }).setView(initialView, initialZoom); // Center on closest location or world view
            
            if (requireCtrlZoom) {
                // Add keyboard event listeners to control scroll wheel zoom
                console.log('Setting up keyboard event listeners for scroll wheel zoom control...');
                document.addEventListener('keydown', function(e) {
                    // Enable scroll wheel zoom when Ctrl (or Command on Mac) is pressed
                    if (e.ctrlKey || e.metaKey) {
                        console.log('Ctrl/Command key pressed, enabling scroll wheel zoom');
                        map.scrollWheelZoom.enable();
                    }
                });
                
                document.addEventListener('keyup', function(e) {
                    // Check if neither Ctrl nor Command is pressed
                    if (!e.ctrlKey && !e.metaKey) {
                        console.log('Ctrl/Command key released, disabling scroll wheel zoom');
                        map.scrollWheelZoom.disable();
                    }
                });
                
                // Ensure scroll wheel zoom is disabled when window loses focus
                window.addEventListener('blur', function() {
                    console.log('Window lost focus, disabling scroll wheel zoom');
                    map.scrollWheelZoom.disable();
                });
            } else {
                // If Ctrl is not required, ensure scrollWheelZoom stays enabled
                map.scrollWheelZoom.enable();
            }
            
            console.log('Map instance created:', map);
            
            // Add OpenStreetMap tile layer
            console.log('Adding tile layer...');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                updateWhenIdle: true
            }).addTo(map);
            
            // Create a layer group for markers
            console.log('Creating markers layer...');
            markersLayer = L.layerGroup().addTo(map);
            
            // Check if we have location data
            if (!window.scbLocationsData || window.scbLocationsData.length === 0) {
                console.warn(scbServiziMapI18n.errors.noLocationDataForMarkers);
            } else {
                console.log('Adding markers for ' + window.scbLocationsData.length + ' locations...');
            }
            
            // Add zoom tooltip control only on non-mobile devices and only if Ctrl is required to zoom
            console.log('Checking if zoom tooltip should be added...');
            // Check if we're in mobile/tablet view (stacked layout)
            // The breakpoint should match the CSS media query (992px)
            if (requireCtrlZoom && window.innerWidth > 992) {
                console.log('Adding zoom tooltip control for desktop view...');
                const zoomTooltip = L.control({position: 'bottomright'});
                zoomTooltip.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'scb-zoom-tooltip');
                    div.innerHTML = '<div class="scb-zoom-tooltip-content">' + scbServiziMapI18n.ui.zoomTooltip + '</div>';
                    return div;
                };
                zoomTooltip.addTo(map);
            } else {
                console.log('Skipping zoom tooltip (mobile view or Ctrl not required)');
            }
            
            // Add initial markers using the processed locations data
            addMarkers(window.scbLocationsData, {
                closestLocation: closestLocation,
                fitAll: !closestLocation
            });

            // Handle AJAX geolocation if needed
            if (scbServiziMapData && scbServiziMapData.user_location && scbServiziMapData.user_location.needs_ajax) {
                handleAJAXGeolocation();
            }

            // Ensure map fills its container on initial load
            if (map) {
                setTimeout(function(){
                    map.invalidateSize();
                    adjustMapHeight();
                }, 0);
            }
            // Also adjust once the window fully loads (fonts/layout settled)
            window.addEventListener('load', function(){
                if (map) {
                    map.invalidateSize();
                    adjustMapHeight();
                }
            });
            
            // Add event listener to enforce minimum zoom level after any zoom operation
            map.on('zoomend', function() {
                // If the current zoom is less than the minimum allowed, reset to minimum
                if (map.getZoom() < 2) {
                    console.log('Enforcing minimum zoom level');
                    map.setZoom(2);
                }
            });
            
            console.log('Map initialization complete.');
        } catch (error) {
            console.error(scbServiziMapI18n.errors.errorInitializingMap, error);
        }
    }
    
    /**
     * Add markers to the map
     * 
     * @param {Array} data - Array of location data objects, each containing services
     * @param {Object} options - Additional options for marker display
     */
    function addMarkers(data, options = {}) {
        try {
            console.log('addMarkers called with data:', {
                dataType: typeof data,
                isArray: Array.isArray(data),
                length: data ? (Array.isArray(data) ? data.length : 'not an array') : 'no data',
                options: options
            });
            
            // Check if markersLayer exists
            if (!markersLayer) {
                console.error(scbServiziMapI18n.errors.markersLayerNotDefined);
                return;
            }
            
            // Clear existing markers
            console.log('Clearing existing markers');
            markersLayer.clearLayers();
            
            // Reset selected marker and location
            selectedMarker = null;
            selectedLocationId = null;
            
            // Hide location details and show placeholder
            console.log('Updating sidebar display');
            $('.scb-servizi-location-content').hide();
            $('.scb-servizi-location-placeholder').show();
        
            // If no data, return
            if (!data || data.length === 0) {
                console.warn(scbServiziMapI18n.errors.noDataForMarkers);
                return;
            }
        
            console.log('Adding markers for ' + data.length + ' locations');
        
            // Count of successfully added markers
            let markersAdded = 0;
            
            // Add markers for each location
            data.forEach(function(location, index) {
                try {
                    console.log('Processing location #' + index + ':', location.title || 'Unnamed');
                    
                    // Skip debug-only items or items with debug information
                    if (location.is_debug_only || location.debug) {
                        console.log('Skipping debug item:', location.title);
                        return; // Skip this location
                    }
                
                    // Convert coordinates to numbers and validate
                    const lat = parseFloat(location.latitude);
                    const lng = parseFloat(location.longitude);
                
                    // Debug coordinates
                    console.log('Location #' + index + ' coordinates:', lat, lng, 'Original:', location.latitude, location.longitude);
                
                    // Check if coordinates are valid numbers
                    if (isNaN(lat) || isNaN(lng)) {
                        console.error(scbServiziMapI18n.errors.invalidCoordinatesForLocation, location.title, location);
                        return; // Skip this location
                    }
                
                    // Create marker
                    console.log('Creating marker for location:', location.title);
                    const isClosestMarker = !!(options.closestLocation && options.closestLocation.id && location.id === options.closestLocation.id);
                    const marker = L.marker([lat, lng], {
                        icon: isClosestMarker ? getHighlightMarkerIcon('closest') : getDefaultMarkerIcon()
                    }).addTo(markersLayer);
                    markersAdded++;
                    
                    // Store location data in marker
                    marker.locationData = location;
                    marker.isClosestMarker = isClosestMarker;
                    marker.isSelectedMarker = false;
                    
                    // Add click event to marker
                    marker.on('click', function(event) {
                        console.log('Marker clicked for location:', location.title);
                        const isAutoSelected = !!(event && event.autoSelected);
                        
                        // Update selected marker
                        if (selectedMarker && selectedMarker !== marker) {
                            // Reset icon for previously selected marker
                            selectedMarker.isSelectedMarker = false;
                            applyBaseMarkerIcon(selectedMarker);
                        }
                        
                        if (isAutoSelected) {
                            selectedMarker = null;
                            marker.isSelectedMarker = false;
                            applyBaseMarkerIcon(marker);
                        } else {
                            // Set this as the selected marker
                            selectedMarker = marker;
                            marker.isSelectedMarker = true;
                            applyBaseMarkerIcon(marker);
                        }

                        selectedLocationId = location.id;
                        
                        // Display location details in sidebar
                        displayLocationDetails(location);
                    });
                } catch (error) {
                    console.error(scbServiziMapI18n.errors.errorCreatingMarker, location.title || scbServiziMapI18n.ui.unnamedLocation, error);
                }
            });
            
            console.log('Successfully added ' + markersAdded + ' markers out of ' + data.length + ' locations');
            
            const shouldShowClosestLocationDetails = options.closestLocation && options.closestLocation.id;
            const shouldFocusClosestLocation = !options.isZoneSelected && shouldShowClosestLocationDetails;

            // If we have a closest location, find and click its marker
            if (shouldShowClosestLocationDetails) {
                console.log('Looking for marker of closest location:', options.closestLocation.title);
                
                // Find the marker for the closest location
                let closestMarker = null;
                markersLayer.eachLayer(function(marker) {
                    if (marker.locationData && marker.locationData.id === options.closestLocation.id) {
                        closestMarker = marker;
                    }
                });
                
                // If found, trigger a click on the marker to show its details
                if (closestMarker) {
                    console.log('Found marker for closest location, triggering click');
                    closestMarker.fire('click', { autoSelected: true });
                } else {
                    console.warn(scbServiziMapI18n.errors.markerForClosestLocationNotFound);
                }
            }
            
            // If we have markers, fit the map to show all markers (skip when focusing on a specific closestLocation)
            if (markersAdded > 0 && ((options && options.fitAll === true) || !shouldFocusClosestLocation)) {
                // If geolocation is disabled, we do NOT fit bounds on initial load to preserve the Mediterranean view
                if (options.fitAll === true && scbServiziMapData && scbServiziMapData.user_location && scbServiziMapData.user_location.is_disabled) {
                    console.log('Geolocation disabled: skipping fitBounds to preserve Mediterranean view');
                    return;
                }

                try {
                    console.log('Fitting map bounds to markers');
                    const markerLayers = markersLayer.getLayers();
                    console.log('Marker layers count:', markerLayers.length);
                    
                    const bounds = L.featureGroup(markerLayers).getBounds();
                    console.log('Calculated bounds:', bounds);
                    
                    // Use larger padding when a geographic zone is selected to ensure all locations are visible
                    const padding = options.isZoneSelected ? [100, 100] : [50, 50];

                    // If there's only one marker, set a sensible zoom centered on it
                    if (markerLayers.length === 1) {
                        const centerLatLng = markerLayers[0].getLatLng();
                        console.log('Single marker detected, centering with fixed zoom');
                        map.setView(centerLatLng, 12);
                    } else if (options.isZoneSelected) {
                        console.log('Zone selected, using enhanced bounds with padding:', padding);
                        // Fit bounds with more padding for zones
                        map.fitBounds(bounds, { padding: padding });
                        // If markers are very close, ensure we don't zoom in too much
                        if (bounds.getNorth() - bounds.getSouth() < 0.1 && bounds.getEast() - bounds.getWest() < 0.1) {
                            console.log('Small area detected, limiting maximum zoom');
                            const currentZoom = map.getZoom();
                            const maxZoomForZone = 10; // Maximum zoom level for a small area
                            if (currentZoom > maxZoomForZone) {
                                map.setZoom(maxZoomForZone);
                            }
                        }
                    } else {
                        // Standard bounds fitting for non-zone filtering
                        map.fitBounds(bounds, { padding: padding });
                    }

                    // Invalidate size after fitting to ensure full rendering within container
                    if (map) {
                        setTimeout(function(){ map.invalidateSize(); }, 0);
                    }

                    console.log('Map bounds set successfully');
                } catch (error) {
                    console.error(scbServiziMapI18n.errors.errorFittingBounds, error);
                }
            } else {
                console.warn(scbServiziMapI18n.errors.noMarkersAdded);
            }
        } catch (error) {
            console.error(scbServiziMapI18n.errors.errorInAddMarkers, error);
        }
    }
    
    /**
     * Display location details in the sidebar
     * 
     * @param {Object} location - Location data object
     */
    function displayLocationDetails(location) {
        try {
            console.log('Displaying details for location:', location.title || 'Unnamed');
            
            // Check if location object is valid
            if (!location) {
                console.error('Invalid location object provided to displayLocationDetails');
                return;
            }
            
            // Check if sidebar elements exist
            const placeholderElement = $('.scb-servizi-location-placeholder');
            const contentElement = $('.scb-servizi-location-content');
            const titleElement = $('.scb-servizi-location-title');
            const imageElement = $('.scb-servizi-location-image');
            const descriptionElement = $('.scb-servizi-location-description');
            const servicesSectionElement = $('.scb-servizi-location-services');
            const servicesListElement = $('.scb-servizi-services-list');
            
            if (placeholderElement.length === 0 || contentElement.length === 0) {
                console.error('Sidebar elements not found in the DOM!');
                return;
            }
            
            console.log('Updating sidebar content...');
            
            // Hide placeholder and show content
            placeholderElement.hide();
            
            // Hide the original title element since we'll put the title in the image
            if (titleElement.length > 0) {
                titleElement.hide();
            } else {
                console.error('Location title element not found!');
            }
            
            // Set location image as background and include title and metadata inside it
            if (imageElement.length > 0) {
                let metaHTML = '';
                let titleHTML = '';
                
                // Create title HTML with details button
                titleHTML = '<div class="scb-servizi-location-title-overlay">';
                titleHTML += '<h2>' + (location.title || scbServiziMapI18n.ui.unnamedLocation) + '</h2>';
                
                // Add details button if enabled in settings and permalink is available
                let showDetailsButton = true;
                try {
                    if (typeof scbServiziMapVars !== 'undefined' && typeof scbServiziMapVars.show_details_button !== 'undefined') {
                        showDetailsButton = (scbServiziMapVars.show_details_button === '1' || scbServiziMapVars.show_details_button === 1 || scbServiziMapVars.show_details_button === true);
                    }
                } catch (e) {
                    console.warn('Could not read show_details_button flag:', e);
                }
                if (showDetailsButton && location.permalink) {
                    titleHTML += '<a href="' + location.permalink + '" class="scb-servizi-location-details-button">' + 
                                 scbServiziMapI18n.ui.details + '</a>';
                }
                
                titleHTML += '</div>';
                
                if (location.all_categories && location.all_categories.length > 0) {
                    console.log('Setting location categories:', location.all_categories);
                    metaHTML += '<p><i class="scb-icon scb-icon-category">&#9776;</i> ' + location.all_categories.join(', ') + '</p>';
                }
                
                if (location.zones && location.zones.length > 0) {
                    console.log('Setting location zones:', location.zones);
                    metaHTML += '<p><i class="scb-icon scb-icon-zone">&#9737;</i> ' + location.zones.join(', ') + '</p>';
                }
                
                if (location.nazione) {
                    console.log('Setting location country:', location.nazione);
                    metaHTML += '<p><i class="scb-icon scb-icon-country">&#9873;</i> ' + location.nazione + '</p>';
                }
                
                if (location.thumbnail) {
                    console.log('Setting location thumbnail as background:', location.thumbnail);
                    // Set the background image and add the title and metadata inside the div
                    imageElement.css({
                        'background-image': 'url(' + location.thumbnail + ')',
                        'background-size': 'cover',
                        'background-position': 'center top'
                    }).html(titleHTML + '<div class="scb-servizi-location-meta-overlay">' + metaHTML + '</div>');
                } else {
                    console.log('No thumbnail available for this location');
                    // If no thumbnail, add the title and metadata directly to the image element
                    imageElement.css('background-image', 'none')
                               .html(titleHTML + (metaHTML ? '<div class="scb-servizi-location-meta-overlay">' + metaHTML + '</div>' : ''));
                }
            } else {
                console.error('Location image element not found!');
            }
            
            // Set location description if available
            if (descriptionElement.length > 0) {
                if (location.content) {
                    console.log('Setting location description (length: ' + location.content.length + ' chars)');
                    descriptionElement.html(location.content);
                } else {
                    console.log('No content available for this location');
                    descriptionElement.empty();
                }
            } else {
                console.error('Location description element not found!');
            }
            
            
            // Determine if services should be shown in map details
            let showServicesInMap = true;
            try {
                if (typeof scbServiziMapVars !== 'undefined' && typeof scbServiziMapVars.show_services_in_map !== 'undefined') {
                    showServicesInMap = (scbServiziMapVars.show_services_in_map === '1' || scbServiziMapVars.show_services_in_map === 1 || scbServiziMapVars.show_services_in_map === true);
                }
            } catch (e) {
                // Keep default true if anything goes wrong
                console.warn('Could not read show_services_in_map flag:', e);
            }
            
            // Show/hide services section container accordingly
            if (servicesSectionElement && servicesSectionElement.length > 0) {
                if (showServicesInMap) {
                    servicesSectionElement.show();
                } else {
                    servicesSectionElement.hide();
                }
            }
            
            // Always render Contact Us button container
            (function(){
                let contactUrl = '/contact-us';
                try {
                    if (typeof scbServiziMapVars !== 'undefined') {
                        if (scbServiziMapVars.contact_url) {
                            contactUrl = scbServiziMapVars.contact_url;
                        } else if (scbServiziMapVars.site_url) {
                            contactUrl = scbServiziMapVars.site_url + '/contact-us';
                        }
                        const passParams = (String(scbServiziMapVars.contact_url_pass_params) === '1');
                        if (passParams && location && location.id) {
                            contactUrl += (contactUrl.indexOf('?') === -1 ? '?' : '&') + 'location_id=' + encodeURIComponent(location.id);
                            if (location.title) {
                                contactUrl += '&location_name=' + encodeURIComponent(location.title);
                            }
                        }
                    }
                } catch(e) {}
                if (contentElement && contentElement.length > 0) {
                    let contactContainer = contentElement.find('.scb-servizi-contact-fixed');
                    const buttonLabel = (scbServiziMapI18n && scbServiziMapI18n.ui && scbServiziMapI18n.ui.contactUs) ? scbServiziMapI18n.ui.contactUs : 'Contact us';
                    const buttonHTML = '<a href="' + contactUrl + '" class="scb-contact-button">' + buttonLabel + '</a>';
                    if (contactContainer.length === 0) {
                        // Create container after services section if present, otherwise append at end
                        const containerDiv = $('<div class="scb-servizi-contact-fixed"></div>').html(buttonHTML);
                        if (servicesSectionElement && servicesSectionElement.length > 0) {
                            servicesSectionElement.after(containerDiv);
                        } else {
                            contentElement.append(containerDiv);
                        }
                    } else {
                        // Update href/text on re-render
                        const btn = contactContainer.find('a.scb-contact-button');
                        if (btn.length > 0) {
                            btn.attr('href', contactUrl).text(buttonLabel);
                        } else {
                            contactContainer.html(buttonHTML);
                        }
                    }
                }
            })();
            
            // Set services list
            if (showServicesInMap && servicesListElement.length > 0) {
                let servicesHTML = '';
                
                if (location.services && location.services.length > 0) {
                    console.log('Setting ' + location.services.length + ' services for this location');
                    
                    // Build Contact Us URL once per location
                    let contactUrl = '/contact-us';
                    if (typeof scbServiziMapVars !== 'undefined') {
                        if (scbServiziMapVars.contact_url) {
                            contactUrl = scbServiziMapVars.contact_url;
                        } else if (scbServiziMapVars.site_url) {
                            contactUrl = scbServiziMapVars.site_url + '/contact-us';
                        }
                        const passParams = (String(scbServiziMapVars.contact_url_pass_params) === '1');
                        if (passParams && location && location.id) {
                            contactUrl += (contactUrl.indexOf('?') === -1 ? '?' : '&') + 'location_id=' + encodeURIComponent(location.id);
                            if (location.title) {
                                contactUrl += '&location_name=' + encodeURIComponent(location.title);
                            }
                        }
                    }
                    
                    // Render each service item
                    location.services.forEach(function(service, index) {
                        console.log('Processing service #' + index + ':', service.title || 'Unnamed Service');
                        
                        servicesHTML += '<li class="scb-servizi-service-item">';
                        servicesHTML += '<h4 class="scb-servizi-service-title"><a href="' + service.permalink + '">' + service.title + '</a></h4>';
                        
                        // Add service categories if available
                        if (service.categories && service.categories.length > 0) {
                            servicesHTML += '<p class="scb-servizi-service-categories"><small><i class="scb-icon scb-icon-category">&#9776;</i>' + service.categories.join(', ') + '</small></p>';
                        }
                        
                        // Add brief excerpt (shortened)
                        const shortExcerpt = service.excerpt && service.excerpt.length > 100 ? 
                            service.excerpt.substring(0, 100) + '...' : 
                            (service.excerpt || scbServiziMapI18n.ui.noDescription);
                        
                        servicesHTML += '<p class="scb-servizi-service-excerpt">' + shortExcerpt + '</p>';
                        servicesHTML += '<a href="' + service.permalink + '" class="scb-servizi-service-link">' + scbServiziMapI18n.ui.details + '</a>';
                        servicesHTML += '<hr class="scb-service-divider">';
                        servicesHTML += '</li>';
                    });
                    
                } else {
                    console.log('No services available for this location');
                    servicesHTML = '<li>' + scbServiziMapI18n.ui.noServicesAvailable + '</li>';
                }
                
                servicesListElement.html(servicesHTML);
            } else if (showServicesInMap) {
                console.error('Services list element not found!');
            }
            
            // Show the location content
            contentElement.show();
            
            // Previously: auto-scroll page to the location details on mobile when a marker was clicked.
            // Requirement: do NOT auto-scroll the page on marker tap/click. Keeping content visible without forcing scroll.
            // (Feature disabled intentionally)
            
            // Adjust map height to match the details panel
            setTimeout(function() {
                adjustMapHeight();
            }, 100); // Small delay to ensure content is fully rendered
            
            console.log('Location details displayed successfully');
        } catch (error) {
            console.error('Error displaying location details:', error);
        }
    }

    /**
     * Adjust map height to match location details panel height
     * Only adjusts height in desktop (side-by-side) layout
     */
    function adjustMapHeight() {
        try {
            // Check if we're in mobile/tablet view (stacked layout)
            // The breakpoint should match the CSS media query (992px)
            if (window.innerWidth <= 992) {
                console.log('In stacked layout, skipping height adjustment');
                return;
            }
            
            const mapElement = $('#scb-servizi-map');
            const detailsElement = $('#scb-servizi-location-details');
            
            if (mapElement.length === 0 || detailsElement.length === 0) {
                console.error('Map or details elements not found for height adjustment');
                return;
            }
            
            // Get the current height of the details panel
            const detailsHeight = detailsElement.outerHeight();
            
            // Only adjust if details panel is taller than the map's current height
            if (detailsHeight > mapElement.outerHeight()) {
                console.log('Adjusting map height to match details panel:', detailsHeight + 'px');
                mapElement.css('height', detailsHeight + 'px');
                
                // If map is initialized, invalidate size to ensure proper rendering
                if (map) {
                    map.invalidateSize();
                }
            }
        } catch (error) {
            console.error('Error adjusting map height:', error);
        }
    }

    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        // Filter button click
        $('#scb-servizi-filter-button').on('click', function(e) {
            e.preventDefault();
            filterMap();
        });
        
        // Reset button click
        $('#scb-servizi-reset-button').on('click', function(e) {
            e.preventDefault();
            console.log('Reset button clicked, resetting map to original state');
            resetMap();
        });
        
        // Dropdown change events - automatically filter when selection changes
        $('#scb-servizi-zona-filter, #scb-servizi-categoria-filter').on('change', function() {
            console.log('Dropdown selection changed, triggering automatic filtering');
            filterMap();
        });
        
        // Window resize event - adjust map height when window is resized
        $(window).on('resize', function() {
            console.log('Window resized, adjusting map height');
            adjustMapHeight();
        });
    }
    
    /**
     * Filter the map based on selected values
     */
    function filterMap() {
        try {
            console.log('filterMap function called');
            
            // Check if filter elements exist
            const zonaFilter = $('#scb-servizi-zona-filter');
            const categoriaFilter = $('#scb-servizi-categoria-filter');
            const mapElement = $('#scb-servizi-map');
            const showServiceFilter = (
                typeof scbServiziMapVars !== 'undefined' &&
                (
                    scbServiziMapVars.show_service_filter === '1' ||
                    scbServiziMapVars.show_service_filter === 1 ||
                    scbServiziMapVars.show_service_filter === true
                )
            );
            
            if (zonaFilter.length === 0) {
                console.error('Zone filter element not found in the DOM!');
                return;
            }
            
            if (mapElement.length === 0) {
                console.error('Map element not found in the DOM!');
                return;
            }
            
            // Get filter values
            const zona = zonaFilter.val();
            const categoria = (showServiceFilter && categoriaFilter.length > 0) ? categoriaFilter.val() : '';
            
            console.log('Filter values:', {
                zona: zona || '(none)',
                categoria: categoria || '(none)'
            });
            
            // Check if AJAX variables are defined
            if (typeof scbServiziMapVars === 'undefined' || !scbServiziMapVars.ajaxurl || !scbServiziMapVars.nonce) {
                console.error('AJAX variables not properly defined!', {
                    scbServiziMapVars: scbServiziMapVars || 'undefined',
                    ajaxurl: scbServiziMapVars ? scbServiziMapVars.ajaxurl : 'undefined',
                    nonce: scbServiziMapVars ? scbServiziMapVars.nonce : 'undefined'
                });
                return;
            }
            
            // Show loading indicator
            console.log('Showing loading indicator');
            mapElement.addClass('loading');
            
            // Log filter selections
            console.log('Filtering map with zona:', zona, 'categoria:', categoria);
            
            // Prepare AJAX data
            const params = new URLSearchParams();
            params.append('action', 'scb_filter_servizi_map');
            params.append('nonce', scbServiziMapVars.nonce);
            params.append('zona', zona);
            params.append('categoria', categoria);
            
            // Define clean AJAX URL
            let cleanAjaxUrl = window.location.origin + '/wp-admin/admin-ajax.php';
            if (scbServiziMapVars.ajaxurl && scbServiziMapVars.ajaxurl.indexOf('trp-ajax.php') === -1) {
                cleanAjaxUrl = scbServiziMapVars.ajaxurl;
            }
            cleanAjaxUrl += (cleanAjaxUrl.indexOf('?') !== -1 ? '&' : '?') + 'v=' + VERSION + '&t=' + new Date().getTime();

            console.log('Making FILTER request using native FETCH API to bypass jQuery filters...', cleanAjaxUrl);
            
            fetch(cleanAjaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(response => {
                console.log('Filter Success Response:', response);
                
                if (!response || !response.success || !response.data) {
                    console.error('Invalid filter response:', response);
                    return;
                }
                
                // Determine if a geographic zone is selected
                const isZoneSelected = zona !== '';
                
                // Process the response data
                let locationsData = [];
                
                if (response.data.locations) {
                    locationsData = response.data.locations;
                } else if (Array.isArray(response.data)) {
                    locationsData = response.data;
                }
                
                console.log('Processed locations count:', locationsData.length);
                
                // Find the closest location
                let closestLocation = null;
                if (scbServiziMapData && scbServiziMapData.user_location && locationsData.length > 0) {
                    closestLocation = findClosestLocation(locationsData, scbServiziMapData.user_location);
                }
                
                // Update markers
                addMarkers(locationsData, {
                    isZoneSelected: isZoneSelected,
                    selectedZone: zona,
                    closestLocation: closestLocation
                });
            })
            .catch(error => {
                console.error('Filter FETCH FAILED:', error);
            })
            .finally(() => {
                mapElement.removeClass('loading');
            });
        } catch (error) {
            console.error('Error in filterMap function:', error);
            // Make sure to hide the loading indicator even if an error occurs
            $('#scb-servizi-map').removeClass('loading');
        }
    }
    
    /**
     * Reset the map to its original state
     */
    function resetMap() {
        try {
            console.log('resetMap function called');
            
            // Check if filter elements exist
            const zonaFilter = $('#scb-servizi-zona-filter');
            const categoriaFilter = $('#scb-servizi-categoria-filter');
            const mapElement = $('#scb-servizi-map');
            const showServiceFilter = (
                typeof scbServiziMapVars !== 'undefined' &&
                (
                    scbServiziMapVars.show_service_filter === '1' ||
                    scbServiziMapVars.show_service_filter === 1 ||
                    scbServiziMapVars.show_service_filter === true
                )
            );
            
            if (zonaFilter.length === 0) {
                console.error('Zone filter element not found in the DOM!');
                return;
            }
            
            if (mapElement.length === 0) {
                console.error('Map element not found in the DOM!');
                return;
            }
            
            // Show loading indicator
            console.log('Showing loading indicator');
            mapElement.addClass('loading');
            
            // Reset filter dropdowns to default values
            console.log('Resetting filter dropdowns');
            zonaFilter.val('');
            if (showServiceFilter && categoriaFilter.length > 0) {
                categoriaFilter.val('');
            }
            
            // Check if we have the original data
            if (!window.scbLocationsData || window.scbLocationsData.length === 0) {
                console.error('Original locations data not available!');
                mapElement.removeClass('loading');
                return;
            }
            
            console.log('Resetting map with original data:', window.scbLocationsData.length, 'locations');
            
            // Find the closest location to the user in the original data
            let closestLocation = null;
            if (scbServiziMapData && scbServiziMapData.user_location && window.scbLocationsData.length > 0) {
                console.log('Finding closest location in original data');
                closestLocation = findClosestLocation(window.scbLocationsData, scbServiziMapData.user_location);
                
                if (closestLocation) {
                    console.log('Found closest location in original data:', closestLocation.title);
                } else {
                    console.log('No closest location found in original data');
                }
            }
            
            // Reset map view to initial state
            console.log('Resetting map view');
            
            // Default view (world view)
            let initialView = [20, 0];
            let initialZoom = 4;
            
            // If we have a closest location, use its coordinates
            if (closestLocation) {
                const lat = parseFloat(closestLocation.latitude);
                const lng = parseFloat(closestLocation.longitude);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    console.log('Setting initial view to closest location:', closestLocation.title);
                    initialView = [lat, lng];
                    initialZoom = 12; // Closer zoom for a specific location
                }
            }
            
            // Validate initialView and initialZoom before passing to setView
            if (!Array.isArray(initialView) || isNaN(parseFloat(initialView[0])) || isNaN(parseFloat(initialView[1])) || isNaN(parseFloat(initialZoom))) {
                console.error('Invalid view parameters for resetMap, using world defaults');
                initialView = [20, 0];
                initialZoom = 4;
            }
            
            // Reset the map view
            if (map) {
                map.setView(initialView, initialZoom);
            }
            
            // Update markers with original data
            addMarkers(window.scbLocationsData, {
                closestLocation: closestLocation
            });
            
            // Hide loading indicator
            console.log('Reset complete, hiding loading indicator');
            mapElement.removeClass('loading');
        } catch (error) {
            console.error('Error in resetMap function:', error);
            // Make sure to hide the loading indicator even if an error occurs
            $('#scb-servizi-map').removeClass('loading');
        }
    }

})(jQuery);
