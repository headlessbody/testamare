# Rilevamento Automatico della Nazione dalle Coordinate

Questo documento descrive la funzionalità di rilevamento automatico della nazione dalle coordinate geografiche implementata nel plugin CPT Servizi.

## Funzionalità

Quando si crea o si modifica una location, il sistema ora rileva automaticamente la nazione in base alle coordinate geografiche inserite. Questo avviene nei seguenti passaggi:

1. L'utente inserisce le coordinate (latitudine e longitudine) nei campi appositi
2. Il sistema verifica che le coordinate siano valide
3. Viene effettuata una richiesta a un servizio di geocodifica inversa (OpenStreetMap Nominatim)
4. La nazione rilevata viene automaticamente inserita nel campo "Nazione"

## Implementazione Tecnica

### File JavaScript

Il file `js/location-admin.js` gestisce la parte client-side della funzionalità:

- Aggiunge event listener ai campi di latitudine e longitudine
- Valida le coordinate prima di effettuare la richiesta
- Effettua una richiesta AJAX per ottenere la nazione
- Aggiorna il campo nazione con il risultato
- Fornisce feedback visivo (indicatori di caricamento, successo, errore)

### Funzioni PHP

Sono state aggiunte le seguenti funzioni PHP:

1. `scb_enqueue_location_admin_scripts` - Carica lo script JavaScript solo nella schermata di modifica delle location

2. `scb_get_country_from_coordinates` - Effettua la geocodifica inversa:
   - Valida le coordinate
   - Verifica se esiste un risultato in cache
   - Effettua una richiesta all'API di OpenStreetMap Nominatim
   - Estrae il nome della nazione dalla risposta
   - Memorizza il risultato in cache per 30 giorni

3. `scb_ajax_get_country_from_coordinates` - Gestisce la richiesta AJAX:
   - Verifica il nonce per la sicurezza
   - Sanitizza le coordinate
   - Chiama la funzione `scb_get_country_from_coordinates`
   - Restituisce il risultato come risposta JSON

## Test della Funzionalità

Per testare la funzionalità:

1. Vai a "Locations" > "Aggiungi Nuova" nel pannello di amministrazione di WordPress
2. Inserisci le coordinate geografiche nei campi "Latitudine" e "Longitudine"
3. Clicca fuori dal campo o passa al campo successivo
4. Verifica che il campo "Nazione" venga automaticamente compilato

### Esempi di Coordinate per Test

Puoi testare la funzionalità con le seguenti coordinate:

| Paese | Latitudine | Longitudine |
|-------|------------|-------------|
| Italia | 41.9028 | 12.4964 |
| Francia | 48.8566 | 2.3522 |
| Germania | 52.5200 | 13.4050 |
| Spagna | 40.4168 | -3.7038 |
| Regno Unito | 51.5074 | -0.1278 |
| Stati Uniti | 40.7128 | -74.0060 |
| Giappone | 35.6762 | 139.6503 |
| Australia | -33.8688 | 151.2093 |

### Test di Casi Limite

- **Coordinate non valide**: Inserisci valori non numerici o fuori range (latitudine: -100, longitudine: 200)
- **Coordinate in mare aperto**: Prova con coordinate in mezzo all'oceano
- **Coordinate al confine tra nazioni**: Prova con coordinate vicine ai confini tra nazioni

## Risoluzione dei Problemi

Se la funzionalità non funziona come previsto:

1. **Verifica la Console del Browser**: Apri gli strumenti di sviluppo del browser (F12) e controlla la console per eventuali errori JavaScript

2. **Verifica la Connessione Internet**: La funzionalità richiede una connessione internet attiva per effettuare la richiesta all'API di geocodifica

3. **Limiti dell'API**: L'API di OpenStreetMap Nominatim ha dei limiti di utilizzo. Se vengono effettuate troppe richieste in poco tempo, l'API potrebbe temporaneamente bloccare le richieste

4. **Cache**: I risultati vengono memorizzati in cache per 30 giorni. Se è necessario forzare un aggiornamento, è possibile cancellare i transient di WordPress dal database

## Note Tecniche

- La funzionalità utilizza l'API gratuita di OpenStreetMap Nominatim per la geocodifica inversa
- I risultati vengono memorizzati in cache per ridurre il numero di richieste all'API
- La funzionalità è progettata per funzionare con qualsiasi formato di coordinate valido (decimale o DMS)
- Il campo nazione viene aggiornato solo se è vuoto o se le coordinate sono cambiate