# Risoluzione Problemi di Visualizzazione Pagine CPT

Questa guida spiega come risolvere i problemi di visualizzazione delle pagine per i Custom Post Types "Servizi" e "Location".

## Problema

Se hai creato servizi o location ma non riesci a visualizzare le loro pagine individuali o le pagine di archivio, potrebbe essere necessario aggiornare la struttura dei permalink di WordPress.

## Soluzione

Il plugin ora include diverse funzionalità per risolvere questo problema:

### 1. Notifica in Amministrazione

Nella dashboard di WordPress apparirà una notifica che ti avvisa del potenziale problema e ti offre un pulsante per aggiornare i permalink.

### 2. Pulsante nella Barra di Amministrazione

È stato aggiunto un menu "CPT Servizi" nella barra di amministrazione di WordPress con un'opzione "Aggiorna Permalink" che puoi utilizzare in qualsiasi momento.

### 3. Script di Test

Abbiamo incluso uno script di test (`test-cpt-pages.php`) che puoi utilizzare per verificare se le pagine dei tuoi CPT sono accessibili:

1. Carica lo script nella directory principale di WordPress
2. Accedi allo script tramite browser (es. `https://tuosito.it/test-cpt-pages.php`)
3. Lo script mostrerà lo stato delle tue pagine CPT e ti offrirà opzioni per risolvere eventuali problemi
4. **Importante**: Elimina lo script dopo l'uso per motivi di sicurezza

## Istruzioni per la Manutenzione

Per garantire che le pagine dei CPT funzionino correttamente:

1. **Dopo l'attivazione del plugin**: I permalink vengono aggiornati automaticamente
2. **Dopo la creazione di nuovi servizi o location**: Tutto dovrebbe funzionare normalmente
3. **Se modifichi la struttura dei permalink**: Potrebbe essere necessario aggiornare manualmente i permalink utilizzando una delle opzioni sopra descritte
4. **Se aggiorni il plugin**: Potrebbe essere necessario aggiornare i permalink

## Risoluzione dei Problemi

Se continui a riscontrare problemi:

1. Verifica che i permalink "carini" siano abilitati in WordPress (Impostazioni > Permalink)
2. Prova a utilizzare il pulsante "Aggiorna Permalink" nella barra di amministrazione
3. Verifica che il tuo tema non stia sovrascrivendo i template per i CPT
4. Controlla se ci sono conflitti con altri plugin

## Struttura delle Pagine

Il plugin ora formatta automaticamente le pagine dei CPT con un layout migliorato:

### Pagine Singole

- **Servizi**: Mostra titolo, immagine in evidenza, contenuto, categorie, tag e location dove il servizio è disponibile
- **Location**: Mostra titolo, immagine in evidenza, contenuto, coordinate geografiche, zone e servizi disponibili in quella location

### Pagine di Archivio

- Layout a griglia responsive
- Visualizzazione di immagini in evidenza, titoli e meta informazioni
- Paginazione automatica

## Supporto

Se hai bisogno di ulteriore assistenza, contatta lo sviluppatore del plugin:

- Stefano Callisto Bassi
- [Solferino3.it](https://solferino3.it)