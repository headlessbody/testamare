# Implementazione delle Modifiche alla Mappa

Questo documento descrive le modifiche apportate alla mappa dei servizi per soddisfare i seguenti requisiti:

1. L'altezza della mappa deve essere sempre alta almeno come il riquadro delle info della location
2. Quando si sceglie dai dropdown una zona geografica o un servizio, deve comparire l'info della location più vicina all'utente

## Modifiche al CSS

### 1. Aggiornamento del Layout Principale

Abbiamo modificato il CSS per garantire che l'altezza della mappa si adatti automaticamente all'altezza del pannello dei dettagli:

```css
/* Map and details container */
.scb-servizi-map-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
    align-items: stretch; /* Ensure children stretch to fill the container height */
    min-height: 500px; /* Minimum height for the container */
}

/* Map container */
#scb-servizi-map {
    width: 60%;
    min-height: 500px; /* Minimum height that can grow with content */
    height: 100%; /* Fill the height of the container */
    border: 1px solid #ddd;
    border-radius: 4px;
    position: relative;
    flex: 3;
    box-sizing: border-box; /* Ensure consistent box model with location details */
    transition: height 0.3s ease; /* Smooth transition when height changes */
}

/* Location details sidebar */
#scb-servizi-location-details {
    width: calc(40% - 20px);
    min-height: 500px; /* Minimum height to match map's height */
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    overflow-y: visible; /* Show all content without scrollbars */
    background-color: #f9f9f9;
    flex: 2;
    box-sizing: border-box; /* Ensure padding is included in height calculation */
    display: flex; /* Use flexbox for the details panel */
    flex-direction: column; /* Stack children vertically */
}
```

### 2. Aggiornamento degli Stili Responsive

Abbiamo aggiornato gli stili responsive per garantire un comportamento corretto su schermi più piccoli:

```css
@media (max-width: 992px) {
    /* Stack layout on tablets and mobile */
    .scb-servizi-map-container {
        flex-direction: column;
        min-height: auto; /* Reset min-height for stacked layout */
    }
    
    #scb-servizi-map {
        width: 100%;
        min-height: 450px;
        height: auto; /* Allow height to be determined by content in stacked layout */
        margin-bottom: 20px;
    }
    
    /* In stacked layout, we don't need to match heights */
}

@media (max-width: 768px) {
    #scb-servizi-map {
        min-height: 400px; /* Use min-height instead of fixed height */
    }
}
```

## Modifiche al JavaScript

### 1. Funzione per Regolare l'Altezza della Mappa

Abbiamo aggiunto una funzione per regolare dinamicamente l'altezza della mappa in base all'altezza del pannello dei dettagli:

```javascript
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
```

### 2. Chiamata alla Funzione di Regolazione dell'Altezza

Abbiamo aggiunto chiamate alla funzione `adjustMapHeight()` in punti strategici:

1. Dopo la visualizzazione dei dettagli della location:

```javascript
// Show the location content
contentElement.show();

// Adjust map height to match the details panel
setTimeout(function() {
    adjustMapHeight();
}, 100); // Small delay to ensure content is fully rendered
```

2. Al ridimensionamento della finestra:

```javascript
// Window resize event - adjust map height when window is resized
$(window).on('resize', function() {
    console.log('Window resized, adjusting map height');
    adjustMapHeight();
});
```

### 3. Mostrare la Location Più Vicina dopo il Filtraggio

Abbiamo modificato la funzione `filterMap()` per trovare e mostrare la location più vicina all'utente dopo il filtraggio:

```javascript
// Find the closest location to the user in the filtered results
let closestLocation = null;
if (scbServiziMapData && scbServiziMapData.user_location && locationsData.length > 0) {
    console.log('Finding closest location in filtered results');
    closestLocation = findClosestLocation(locationsData, scbServiziMapData.user_location);
    
    if (closestLocation) {
        console.log('Found closest location in filtered results:', closestLocation.title);
    } else {
        console.log('No closest location found in filtered results');
    }
}

// Update markers with options including closest location
addMarkers(locationsData, {
    isZoneSelected: isZoneSelected,
    selectedZone: zona,
    closestLocation: closestLocation
});
```

## Come Funziona

### Adattamento dell'Altezza della Mappa

1. Quando i dettagli di una location vengono visualizzati, la funzione `adjustMapHeight()` viene chiamata
2. La funzione controlla se siamo in layout desktop (larghezza > 992px)
3. Se siamo in layout desktop, confronta l'altezza del pannello dei dettagli con quella della mappa
4. Se il pannello dei dettagli è più alto della mappa, regola l'altezza della mappa per corrispondere
5. Chiama `map.invalidateSize()` per garantire che la mappa si ridisegni correttamente

### Visualizzazione della Location Più Vicina

1. Quando l'utente seleziona una zona geografica o un servizio dai dropdown, viene chiamata la funzione `filterMap()`
2. La funzione ottiene i dati filtrati dal server tramite AJAX
3. Dopo aver ricevuto i dati, trova la location più vicina all'utente tra i risultati filtrati
4. Passa questa location più vicina alla funzione `addMarkers()` come opzione
5. La funzione `addMarkers()` aggiunge i marker alla mappa e fa clic automaticamente sul marker della location più vicina
6. Questo fa sì che i dettagli della location più vicina vengano visualizzati nel pannello laterale

## Test

Per testare queste modifiche:

1. Verifica che l'altezza della mappa si adatti all'altezza del pannello dei dettagli quando il contenuto è lungo
2. Verifica che questo comportamento funzioni solo in layout desktop (larghezza > 992px)
3. Verifica che quando selezioni una zona geografica o un servizio dai dropdown, vengano mostrati i dettagli della location più vicina
4. Testa con diverse dimensioni dello schermo per assicurarti che il comportamento responsive funzioni correttamente

## Nuova impostazione: Mostra/Nascondi pulsante "Dettaglio"

- Aggiunta una nuova opzione nelle Impostazioni delle Location (menu Location > Impostazioni) per mostrare o nascondere il pulsante "Dettaglio" nel pannello laterale della mappa.
- Opzione memorizzata come `scb_location_show_details_button` (predefinito: attivo).
- Il valore viene passato al JavaScript e, quando disattivato, il pulsante non viene renderizzato.
- File coinvolti:
  - `cpt-servizi.php` (localizzazione variabili JS, salvataggio e UI dell'impostazione)
  - `js/servizi-map.js` (render condizionale del pulsante Dettaglio)

## Aggiornamenti recenti

### Geolocalizzazione piu affidabile su mobile e roaming

- La logica della mappa ora privilegia la geolocalizzazione reale del device tramite browser.
- Il flusso prova prima una richiesta ad alta precisione e, se necessario, un secondo tentativo bilanciato.
- Il fallback IP non e piu il percorso principale: viene usato solo come ultima risorsa quando il browser non restituisce coordinate utili.
- Questo riduce i casi in cui un utente in roaming viene localizzato nel paese della SIM invece che nella posizione reale.

### Selezione automatica della location piu vicina

- All'apertura della pagina contenente lo shortcode `[servizi_map]`, il plugin calcola le distanze tra la posizione dell'utente e tutte le location disponibili.
- La location piu vicina viene selezionata automaticamente, il marker viene evidenziato e il pannello laterale viene aperto.
- Lo stesso comportamento viene mantenuto anche dopo il filtraggio AJAX.

### Pulizia del backend e dei log

- Rimosso il notice admin `CPT Services: Update Required` dal backend.
- Lasciata disponibile l'azione `Update Permalinks` nel menu admin bar.
- Corretto un falso errore in console: `Services list element not found!` ora compare solo quando la lista servizi dovrebbe essere renderizzata davvero.
