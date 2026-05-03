# CPT Servizi - Plugin WordPress

Plugin WordPress per la creazione di un Custom Post Type "Servizi" con categorie, zona geografica, tag e mappa mondiale interattiva.

## Autore
Stefano Callisto Bassi - [Solferino3.it](https://solferino3.it)

## Descrizione
Questo plugin crea due Custom Post Types (CPT):
- **Servizi**: Per gestire i servizi offerti
- **Location**: Per gestire le posizioni geografiche

Con supporto per:
- Categorie (tassonomia gerarchica)
- Zone Geografiche (tassonomia gerarchica)
- Tag (tassonomia non gerarchica)
- Mappa mondiale interattiva che visualizza i servizi con filtri AJAX

## Funzionalità

### Custom Post Type "Servizi"
- Supporto completo per editor WordPress, immagini in evidenza, estratti e campi personalizzati
- Interfaccia amministrativa in italiano

### Custom Post Type "Location"
- Gestione centralizzata delle posizioni geografiche
- Campi per coordinate geografiche (latitudine e longitudine) e nazione
- Selezione multipla dei servizi disponibili in quella location tramite checkbox
- Possibilità di associare più servizi alla stessa location

### Tassonomie
- **Categorie Servizi**: Tassonomia gerarchica per categorizzare i servizi
- **Zone Geografiche**: Tassonomia gerarchica per specificare le aree geografiche delle location
- **Tag Servizi**: Tassonomia non gerarchica per etichette aggiuntive

### Mappa Mondiale
- Layout a due colonne con mappa (60%) e dettagli della location (40%)
- Visualizzazione delle locations su una mappa mondiale interattiva
- Ogni marker sulla mappa rappresenta una location, non un singolo servizio
- Pannello laterale che mostra i dettagli della location selezionata
- Filtri AJAX per categoria e zona geografica
- Zoom sulla mappa attivabile solo tenendo premuto il tasto Control (o Command su Mac) con tooltip informativo
- Design responsive per tutti i dispositivi (layout a colonne su desktop, impilato su mobile)

## Utilizzo

### Aggiunta di una Location
1. Nel pannello di amministrazione WordPress, vai su "Locations" > "Aggiungi Nuova"
2. Inserisci titolo e descrizione della location
3. Assegna le zone geografiche appropriate
4. Nella sezione "Coordinate Geografiche", inserisci latitudine, longitudine e nazione della location
5. Nella sezione "Servizi Collegati", seleziona i servizi che saranno disponibili in questa location
6. Pubblica la location

### Aggiunta di un Servizio
1. Nel pannello di amministrazione WordPress, vai su "Servizi" > "Aggiungi Nuovo"
2. Inserisci titolo, descrizione e immagine in evidenza
3. Assegna categorie e tag
4. Pubblica il servizio
5. Per collegare il servizio a una o più locations, modifica le locations desiderate e seleziona il servizio nella sezione "Servizi Collegati"

### Visualizzazione della Mappa
Per visualizzare la mappa mondiale con le locations e i relativi servizi, utilizza lo shortcode:

```
[servizi_map]
```

Puoi personalizzare l'altezza e la larghezza della mappa:

```
[servizi_map height="600px" width="100%"]
```

#### Come Funziona la Mappa
- Sulla mappa viene visualizzato un marker per ogni location che ha almeno un servizio associato
- Cliccando su un marker, nel pannello laterale vengono mostrati:
  - Informazioni sulla location (titolo, descrizione)
  - Immagine in evidenza della location come sfondo con categorie, zone e nazione sovrapposte
  - Lista di tutti i servizi collegati a quella location
  - Per ogni servizio: titolo, categorie, breve descrizione e link ai dettagli
- I servizi sono raggruppati per location, quindi tutti i servizi selezionati per una location appariranno insieme nel pannello laterale
- Su dispositivi mobili, il pannello dei dettagli viene visualizzato sotto la mappa

### Filtri della Mappa
Sopra la mappa sono presenti due menu a tendina:
1. **Zone Geografiche**: Filtra le locations in base alle zone geografiche assegnate direttamente alle location
2. **Categorie**: Filtra le locations in base alle categorie dei servizi associati

Come funzionano i filtri:
- Quando selezioni una zona geografica, vengono mostrate solo le locations a cui è stata assegnata quella zona
- Quando selezioni una categoria, vengono mostrate solo le locations che hanno almeno un servizio con quella categoria
- Nel pannello laterale di una location filtrata, vengono comunque mostrati tutti i servizi disponibili in quella location
- Questo approccio permette di trovare facilmente tutte le locations in una specifica area geografica o che offrono un certo tipo di servizio

La mappa si aggiorna automaticamente quando selezioni un'opzione dai menu a tendina. È anche disponibile un pulsante "Filtra" che puoi utilizzare se preferisci effettuare più selezioni prima di aggiornare la mappa.

## Requisiti Tecnici
- WordPress 5.0 o superiore
- PHP 7.0 o superiore
- Connessione internet (per caricare le mappe OpenStreetMap)

## Supporto per le Traduzioni
Il plugin è completamente pronto per essere tradotto:

- Tutte le stringhe di testo sono preparate per la traduzione
- Supporto completo per TranslatePress e altri plugin di traduzione
- File di traduzione (.pot) incluso nella cartella `/languages`

### Per i Traduttori
Se desideri tradurre il plugin in un'altra lingua:

1. Utilizza il file `languages/cpt-servizi.pot` come modello
2. Crea un file .po per la tua lingua (es. `cpt-servizi-it_IT.po`)
3. Traduci le stringhe nel file .po
4. Genera il file .mo corrispondente
5. Posiziona entrambi i file nella cartella `/languages` del plugin

### Integrazione con TranslatePress
Il plugin è completamente compatibile con TranslatePress:

1. Installa e attiva TranslatePress
2. Configura le lingue desiderate in TranslatePress
3. Utilizza l'editor di traduzione visuale di TranslatePress per tradurre tutte le stringhe del plugin
4. Sia le stringhe PHP che JavaScript saranno rilevate automaticamente da TranslatePress
5. Tutte le stringhe di testo nell'interfaccia utente della mappa sono completamente traducibili
6. I messaggi di errore e le notifiche di console sono anch'essi traducibili per gli sviluppatori internazionali

#### Elementi Traducibili
- Etichette dei filtri e pulsanti
- Testo segnaposto e messaggi informativi
- Titoli delle sezioni e intestazioni
- Etichette delle categorie, zone e nazioni
- Pulsanti e link nell'interfaccia utente
- Messaggi di errore e notifiche di sistema

## Note per gli Sviluppatori
- Il plugin utilizza Leaflet.js per la visualizzazione delle mappe
- I file JavaScript e CSS sono organizzati nelle rispettive cartelle
- Le richieste AJAX sono gestite tramite l'API WordPress
- L'internazionalizzazione è implementata utilizzando le funzioni standard di WordPress
- Le stringhe JavaScript sono tradotte tramite `wp_localize_script()`

## Licenza
GPL2