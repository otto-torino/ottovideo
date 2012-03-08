OttoVideo by Otto srl, MIT license
===================================================================

Version 0.1

Ottovideo è un'applicazione web per la gestione di una webTV.

Permette di gestire una programmazione video live, onair con palinsesto ed una collezione di video selezionabili a richiesta dall'utente (ondemand) commentabili. Inoltre fornisce uno specchietto (onair box) nel quale scorrono informazioni riguardo ai contenuti attualmente in onda.

Ottovideo gestisce spot categorizzati che possono essere collegati ai video, lo spot verrà riprodotto prima della visualizzazione del video. Ciascuno spot prevede un numero massimo di visualizzazioni prima della automatica disattivazione.

Ottovideo espone feed pubblici che contengono la lista dei video disponibili all'ondemand e dei commenti inseriti dagli utenti.

REQUISITI
------------
- php >= 5   
- mysql >= 5   
- apache >= 2   

Settare il DBMS in modo da utilizzare una connessione utf8.  
 
INSTALLAZIONE
--------

* copiare tutti i file in una directory interna alla web server root.   
* creare un database vuoto ed in seguito le tabelle utilizzando il file **db_ottovideo.sql**.
* configurare i parametri di connessione al db nel file **config.php**
* ottovideo utilizza flowplayer (http://www.flowplayer.org), un video player per la visualizzazione di video in formato adobe flv con anche la possibilità di visualizzare formati html5 stanadard. E' pertanto necessario installarlo per fruire i contenuti video (vedi prossima sezione).
* navigare nella directory di instalalzione. user e password di default sono i classici admin:admin. 

INSTALLAZIONE FLOWPLAYER
------------------------

* scaricare la versione desiderata del player e dei plugin **content**, controls, **rtmp** e sharing (in grassetto quelli necessari).
* copiare i file con estensione swf scaricati nella directory **lib/js/flowplayer**
* scaricare i plugin javascript **flowplayer-x.x.x.min.js** e **flowplayer.ipad-x.x.x.min.js** e copiarli nella directory **lib/js/flowplayer/js**
