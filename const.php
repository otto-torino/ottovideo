<?php

define('TBL_USERS', 'ov_users');
define('TBL_LANGUAGES', 'ov_languages');
define('TBL_TRANSLATIONS', 'ov_translations');
define('TBL_CTG', 'ov_categories');
define('TBL_SPOT_CTG', 'ov_spot_categories');
define('TBL_VIDEO', 'ov_video_items');
define('TBL_VIDEO_COMMENT', 'ov_video_comments');
define('TBL_SCHEDULE', 'ov_schedule');
define('TBL_LAYOUT', 'ov_layout');
define('TBL_CONFIG', 'ov_config');

define('CLEAR', '<div style="clear:both;"></div>');
define('STAR', '<span style="color:#ff0000;">*</span>');
define('BACK', '<a href="javascript:history.go(-1)"><img alt="back" title="back" class="tooltip" src="'.REL_ICONS.'/ico_return.gif" /></a>');

define('HACK_MSG', "I'll hack your brain, soon...");

define("GENERAL_INFO", "<div class=\"nav\"><h1>"._("Benvenuto nel mondo di OttoVideo.")."</h1>" .
		"<p>"._("Ottovideo permette di gestire una programmazione video live, on air con palinsesto ed una collezione di video selezionabili a richiesta dall'utente (on demand) commentabili. Inoltre fornisce uno specchietto (on air box) nel quale scorrono informazioni riguardo ai contenuti attualmente in onda.")."</p>".
		"<p>"._("Ottovideo gestisce inoltre spot categorizzati che possono essere collegati ai video, lo spot verrà riprodotto prima della visualizzazione del video. Ciascuno spot prevede un numero massimo di visualizzazioni prima della automatica disattivazione.")."</p>".
		"<h2>"._("Gestione files (video | spot)")."</h2>" .
		"<p>"._("I video sono gestiti attraverso una categorizzazione ad albero infinito. In front end sono raggiungibili solamente " .
		"i video che appatengono ad una categoria 'foglia', cioè che non contiene sottocategorie.<br/>" .
		"Nel form per l'inserimento e modifica dei video, il campo 'Nome' deve essere compilato con il nome esatto del file, " .
		"mentre questo dovrà essere uploadato via ftp all'interno della cartella specificata nelle impostazioni di configurazione (vedi piu' avanti).<br/>" .
		"Il campo 'Nome file html5' deve contenere (se presente) il nome del file encodato nel formato dedicato alla visualizzazione mediante tag video di html5.<br/>" .
		"Se il file inserito è un video, si può decidere se attivare o meno la riproduzione di spot appartenenti alla categoria impostata nel campo 'Categoria spot' prima della riproduzione del video.<br/>" .
		"Se il file inserito è uno spot, si può decidere se attivarlo o meno, a quale categoria spot appartiene, il numero massimo di visualizzazioni prima della disattivazione ed un url verso il quale linkare lo spot.<br/>" .
		"L'immagine caricata associata al video non viene ridimensionata, quindi è necessario prepararle ad hoc, tenendo conto delle scelte di layout " .
		"e delle dimensioni impostate nella configurazione liste (vedi dopo).")."</p>" .
		"<h2>"._("Commenti")."</h2>" .
		"<p>"._("I video sono liberamente commentabili dagli utenti non autenticati, nell'area ammnistrativa è possibile visualizzare ed eventualmente eliminare i commenti pubblicati")."</p>".
		"<h2>"._("Live")."</h2>" .
		"<p>"._("Il player visualizza i contenuti in streaming dalla sorgente definita nella sezione raggiungibile da Configurazione -> Live streaming.").
		"<h2>"._("On Air")."</h2>" .
		"<p>"._("Il player visualizza contenuti in base alla programmazione stabilita nel palinsesto. La lista dei video in programmazione viene aggiornata ".
	       	"automaticamente con lo scorrere del tempo.")."</p>" .
		"<h2>"._("On Demand")."</h2>" .
		"<p>"._("Il player visualizza contenuti in base alla scelta dell'utente su una lista scorrevole di elementi. E' inoltre possibile visualizzare ".
		"un video all'apertura della pagina, passando un parametro <b>vid</b> tramite url (\$_GET) il cui valore deve essere l'id del video da visualizzare")."</p>" .
		"<h2>"._("Palinsesto")."</h2>" .
		"<p>"._("Al momento il palinsesto non prevede l'inserimento di contenuti in continuum temporale, cioè il palinsesto" .
		" viene gestito giorno per giorno, e se si inseriscono contenuti la cui durata va oltre le 24:00 questi non ruberanno spazio " .
		"al palinsesto del giorno successivo. Il comportamento del sistema sarà il seguente:<br/>chi si collega entro le ore 24:00" .
		" vedrà esaurirsi il palinsesto programmato per quel giorno che quindi continuerà anche nel giorno successivo, ma se ricarica " .
		"la pagina passate le ore 24:00, il palinsesto diventa quello del nuovo giorno."."</p>") .
		"<h2>"._("Layout")."</h2>" .
		"<p>"._("Permette di definire il layout per la visualizzazione dell'ondemand, dell'onair, dell'onairbox e del live. La scelta è limitata ai layout disponibili, " .
		"le dimensioni impostate influenzano le dimensioni dell'iframe che dovrà essere importato nel proprio sito.")."</p>" .
		"<h2>"._("Configurazione")."</h2>" .
		"<p>"._("Sito ospitante: inserire l'url completo della pagina che include ottovideo"."<br />" . 
		"Live streaming: inserire i percorsi appropriati che puntano alle risorse in streaming."."<br/>" .
		"Server: inserire il path (privo di schema e slash finale) della cartella sul server di streaming che contiene i file in streaming ed eventualmente il path del server http che contiene i file nel formato dedicato alla visualizzazione mediante tag video html5."."<br/>" .
		"Player: è possibile impostare immagini o filmati(flash) splash che vengono eseguiti quando non ci sono programmi in " .
		"palinsesto, non appena ci si collega alla pagina dell'ondemand ed in attesa del live streaming. Nei relativi campi inserire solamente i nomi dei file, " .
		"mentre i file veri e  propri devono essere uploadati nella cartella indicata.<br/>" .
		"Lista: si possono inserire le dimensioni delle diapositive delle immagini uploadate per i video. Queste diapositive sono mostrate nei" .
		" vari tipi di lista disponibili: orizzontale e verticale, e devono essere scelte accuratamente in base alle dimensioni decise per il layout" .
		" ed ovviamente alle dimensioni delle immagini uploadate.<br/>")."</p>" .
		"<h2>"._("Pubblica")."</h2>" .
		"<p>"._("In questa sezione si possono visualizzare i contenuti in anteprima (il contenuto dell'iframe), ed è presentato il codice html " .
		"da copiare ed incollare per visualizzare il tutto su un altro sito. Se dovessero esserci problemi di dimensioni, ovvero se " .
		"una parte dei contenuti dovesse risultare tagliata nell'iframe, ad esempio, modificare gli attributi height e width in modo da risolvere il problema.")."</p>" .
		"<h2>"._("Utenti")."</h2>" .
		"<p>"._("In questa sezione è possibile gestire gli utenti o gestire la propria password a seconda dei propri privilegi.")."</p>" .
		"<h2>"._("Lingue")."</h2>" .
		"<p>"._("Il sistema può gestire un numero a piacere di lingue. Una sola è settata come lingua principale e tutti gli inserimenti avverranno in questa lingua. ".
		"Le traduzioni dei campi che le richiedono vengono proposte per le sole lingue attive. Le stringhe contenute nel codice sono tradotte nel file <b>translations.xml</b> ".
		"che si trova nella root directory. Quando i contenuti vengono visualizzati in una lingua diversa dalla principale se esiste una traduzione nella ".
		"lingua in questione viene mostrata, altrimenti viene mostrato il contenuto nella lingua principale. La lingua nella quale si desiderano vedere i contenuti ".
		"deve essere passata tramite url (\$_GET) assegnando il suo codice (es. it_IT, en_US, es_ES, fr_FR) al parametro <b>lng</b>.")."</p>" .
		"<h2>"._("Layout & Css")."</h2>" .
		"<p>"._("I file css che vengono richiamati sono <i>onDemand.css</i>, <i>onAir.css</i> e <i>onAirBox.css</i> e si trovano all'interno della cartella " .
		"<i>/css</i>. Questi sono modificabili a piacere per integrare al meglio i contenuti nel proprio sito. Tuttavia si consiglia di giocare solamente " .
		"sui colori, sfondi etc... e di minimizzare le modifiche che vanno a toccare il dimensionamento degli oggetti.<br/>" .
		"Infatti ogni contenuto è dimensionato ad hoc, per rispettare le dimensioni definite nella sezione LAYOUT, e modifiche quali margini padding etc.. possono rendere " .
		"non visibili parti di contenuti all'interno dell'iframe, dunque: maneggiare con cura. I file sono commentati all'interno in modo da " .
		"sottolineare le operazioni non permesse.")."</p>"); 




				













?>
