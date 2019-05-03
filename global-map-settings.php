<?php
global $Debug;
############################################################################
#  global-map-settings.php
#
#  provides global setup values for the Affiliated Regional Networks Global Map
#
#  Author: Ken True - webmaster@saratoga-weather.org
#
# Version 4.00 - 12-Aug-2018 - rewrite to use Leaflet/OpenStreetMaps+other tile provider for map
# Version 4.01 - 12-Aug-2018 - update for base64 decode of translate table
############################################################################
// ------------- Required settings for global-map.* scripts ----------------------------------------
//
  $lang = 'en';      // default language
  $condIconsDir = './MESO-images/';  // relative directory for pin/cluster/conditions images
  $netLinksPath = './';  // relative path for including the network links files from get-links.php
  //   units-of-measure defaults for display
  $gmTempUOM = 'F';   // units for Temperature ='C' or ='F';
  $gmWindUOM = 'mph'; // units for Wind Speed ='mph', ='km/h', ='m/s', ='kts'
  $gmBaroUOM = 'inHg';// units for Barometer ='inHg', ='hPa', ='mb'
  $gmRainUOM = 'in';  // units for Rain ='in', ='mm'
  //  map settings
  $gmMapCenter = '42.8115,10.8984'; // latitude,longitude for initial map center display (decimal degrees)
  $gmMapZoom = 2; // initial map zoom level 2=world, 10=city;
  $gmClusterRadius = 5;     // default =5 number of pixels difference marker points to cluster
	                    // should be number from 5 to 80=max clustering
  $gmProvider = 'Esri_WorldTopoMap'; // ESRI topo map - no key needed
  //$gmProvider = 'OSM';     // OpenStreetMap - no key needed
  //$gmProvider = 'Terrain'; // Terrain map by stamen.com - no key needed
  //$gmProvider = 'OpenTopo'; // OpenTopoMap.com - no key needed
  //$gmProvider = 'Wikimedia'; // Wikimedia map - no key needed
  // 
  //$gmProvider = 'MapboxSat';  // Maps by Mapbox.com - API KEY needed in $mapboxAPIkey 
  //$gmProvider = 'MapboxTer';  // Maps by Mapbox.com - API KEY needed in $mapboxAPIkey 
  $mapboxAPIkey = '--mapbox-API-key--';  // use this for the Access Token (API key) to MapBox

  $gmShowFireDanger = false; // =true; show Fire Danger based on Chandler Burning Index; =false don't show

  $doLinkTarget = true; // =true to add target="_blank" to links in popups
  $doRotatingLegends = true; // =true to do rotating legends, =false for no rotating legends on map
//
//end settings
############################################################################
// -------------------------------------------------------------
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain; charset=ISO-8859-1");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}

// Use overrides from Saratoga template Settings.php if available
//
global $SITE, $Debug;
if (file_exists("Settings.php"))  { include_once("Settings.php"); }
if (isset($SITE['lang']))         { $lang = $SITE['lang']; }
if (isset($SITE['gmMapZoom']))    { $gmMapZoom = $SITE['gmMapZoom']; }
if (isset($SITE['gmMapCenter']))  { $gmMapCenter = $SITE['gmMapCenter']; }
if (isset($SITE['gmTempUOM']))    { $gmTempUOM = $SITE['gmTempUOM']; }
if (isset($SITE['gmWindUOM']))    { $gmWindUOM = $SITE['gmWindUOM']; }
if (isset($SITE['gmBaroUOM']))    { $gmBaroUOM = $SITE['gmBaroUOM']; }
if (isset($SITE['gmRainUOM']))    { $gmRainUOM = $SITE['gmRainUOM']; }
if (isset($SITE['gmShowFireDanger']))    { $gmShowFireDanger = $SITE['gmShowFireDanger']; }
if (isset($SITE['gmDoRotatingLegends'])) {$doRotatingLegends = $SITE['gmDoRotatingLegends']; }
if (isset($SITE['gmDoLinkTarget'])) {$doLinkTarget = $SITE['gmDoLinkTarget']; }
if (isset($SITE['gmProvider']))   { $gmProvider = $SITE['gmProvider']; }
if (isset($SITE['mapboxAPIkey'])) { $mapboxAPIkey = $SITE['mapboxAPIkey'];}
if (isset($SITE['gmClusterRadius'])) {$gmClusterRadius = $SITE['gmClusterRadius']; }

// end of overrides from Settings.php

if(!function_exists('langtransstr')) {
	// shim function if not running in Saratoga template set
	function langtransstr($input) { return($input); }
}
if(!function_exists('langtrans')) {
	// shim function if not running in Saratoga template set
	function langtrans($input) { echo $input; return($input); }
}

if(!isset($SITE['lang'])) {
	$SITE = array(); // shim for not running in Saratoga template set
}

############################################################################
# DO NOT CHANGE THESE SETTINGS
$GoogleLang = array ( // ISO 639-1 2-character language abbreviations from country domain to Google usage
  'af' => 'af',
  'bg' => 'bg',
  'ct' => 'ca',
  'dk' => 'da',
  'nl' => 'nl',
  'en' => 'en',
  'fi' => 'fi',
  'fr' => 'fr',
  'de' => 'de',
  'el' => 'el',
  'ga' => 'ga',
  'it' => 'it',
  'he' => 'iw',
  'hu' => 'hu',
  'no' => 'no',
  'pl' => 'pl',
  'pt' => 'pt',
  'ro' => 'ro',
  'es' => 'es',
  'se' => 'sv',
  'si' => 'sl',
	'sk' => 'sk',
	'sr' => 'sr',
);
$showGizmo = false; // needed to fake-out lack of template support

$Lang = $GoogleLang[$lang]; // the default from above settings
$Status = '';
if(isset($SITE['lang']) and isset($GoogleLang[$SITE['lang']])) {
	$Lang = $GoogleLang[$SITE['lang']];
	$Status .= "<!-- site lang=".$SITE['lang']." used - Google Lang=$Lang -->\n";
}
if(isset($_GET['lang']) and isset($GoogleLang[$_GET['lang']])) {
	$lang = $_GET['lang'];
	$Lang = $GoogleLang[$lang];
	$SITE['lang'] = $lang;
	$Status .=  "<!-- parm lang=$lang used - Google Lang=$Lang -->\n";
}

# Make the GMNET_xxxx string constants for use throughout the pages
# for local language support (derived from the mesonet-map-lang-xx.txt scripts)
if(!file_exists("global-map-lang-inc.php")) {
	print "<h2>Warning: global-map-lang-inc.php file not found.  Upload the file " .
	  "from the global-map.zip distribution</h2>\n";
		exit;
}

//$L = unserialize(file_get_contents("global-map-lang-inc.php"));
include_once("global-map-lang-inc.php");

if(!isset($L['en']['text']['charset'])) {
	print "<h2>Warning: global-map-lang-inc.php is not usable.  Upload an unmodified " .
	  "copy from the global-map.zip distribution.</h2>\n";
		exit;
}
global $L;
$LTRANS = array();
if(isset($L[$lang]['text']['charset'])) {
	$LT = $L[$lang];
} else {
	$LT = $L['en']; // use English as the default for missing language
}
foreach ($LT['text'] as $key => $val) { // make all the global text (GMNET_...) constants
	$t = str_replace("'","\'",$val);
	$t = str_replace("\\'","\'",$t); 
	//print "  langTransLookup['$key'] = '$t';\n";
	$LTRANS[$key] = $val;
	define('GMNET_'.strtoupper($key),$val);
}


// end mesonet-map-settings.php