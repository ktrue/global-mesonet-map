This is Version 4.00 of the Regional Affiliated Networks Global map.

This 4.xx version provides for Saratoga template or standalone usage (unlike previous 
versions with came in separate template/standalone versions)

This version requires no API keys to operate as it uses Leaflet/OpenStreetMaps
for map tile services instead of the prior Google JavaScript Map API.
Support is also provided for optional Mapbox.com map tiles if you have
acquired a Mapbox Access Token (API key). Two additional maps will be enabled
(Terrain3 and Satellite) with a valid Mapbox Access Token.

Files in the distribution:

global-map-README.txt   (this file)

./MESO-images/*         Image files used by the map display

global-map-settings.php (this file contains ALL the configuration settings for the script set)

global-map.php          (this is a single-page, non-template PHP sample page)


global-map.css          (style sheet for all the above pages)
global-map.js           (common JavaScript routines used by all the global map pages)

global-map-inc.php      (generation script used by wxglobal.php or global-map-sample.php pages)

global-map-lang-inc.php (PHP serialize[d] array of language entries for legend text.
                         NOTE: do NOT edit or modify in any way or the script will break)

global-map-genjs-inc.php (generates the JavaScript to control the Leaflet map)

global-conditions-json.php (common script for getting conditions data from the network hub site)\
  it will update the following file (so make sure permissions on it are 664 if need be):
     global-conditions.json

global-links.php        (script for getting data/links about the regional networks -
  it will update the following files (so make sure permissions on them are 664 if need be):
     member-count.txt
     members-list-inc.html
     network-links-inc.html
     network-list-inc.html

These scripts are used by the wxglobal.php (Saratoga Template):
wxglobal.php            (global map page for V2 or V3 of Saratoga template set)
wxglobal-en.html        (English boilerplate page for V2 or V3 of Saratoga template set)
wxglobal-es.html        (Spanish boilerplate page for V2 or V3 of Saratoga template set)

Configuration for the wxglobal.php or global-map.php pages is in global-map-settings.php
and looks like this:

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


Adjust the settings as guided by the comments next to each variable to configure your display.

----------------------------------------------------------------------------
Installation:

1) unzip the distribution file (preserving the directory structure) to your document root directory

2) upload the ./MESO-images/ directory and contents.

3) Edit your global-map-settings.php for your desired display configuration and upload.
   Note: if using the Saratoga template set, you can use the following entries in Settings.php
   to specify your settings.  They will OVERRIDE the similar settings inside global-map-settings.php

   $SITE['lang']
   $SITE['gmMapZoom']
   $SITE['gmMapCenter']
   $SITE['gmTempUOM']
   $SITE['gmWindUOM']
   $SITE['gmBaroUOM']
   $SITE['gmRainUOM']
   $SITE['gmShowFireDanger']
   $SITE['gmDoRotatingLegends']
   $SITE['gmDoLinkTarget']
   $SITE['gmProvider']
   $SITE['mapboxAPIkey']
   $SITE['gmClusterRadius']

4) Upload (WITHOUT modifications):
   global-map.css
   global-map.js
   global-map.php 
   global-map-inc.php
   global-map-lang-inc.php
   global-map-genjs-inc.php
   global-conditions-json.php
   global-links.php
   wxglobal.php
   wxglobal-en.html
 
and these files (which should be writable by PHP with permissions 664 or 666)
   member-count.txt
   members-list-inc.html
   network-links-inc.html
   network-list-inc.html
   global-conditions.json
   

If using the Saratoga template, you can copy wxglobal-en.html to wxglobal-LL.html to add new language support

Also add to the language-LL.txt entries for

langlookup|Global Station Map|Global Station Map|
langlookup|Global Station Map of Affiliated Weather Networks|Global Station Map of Affiliated Weather Networks|
langlookup|Nets|Nets|
langlookup|Weather, Lightning, WebCam|Weather, Lightning, WebCam|
langlookup|Weather, WebCam, Lightning|Weather, WebCam, Lightning|
langlookup|Weather, Lightning|Weather, Lightning|
langlookup|Weather, WebCam|Weather, WebCam|
langlookup|Weather|Weather|
langlookup|Conditions not available|Conditions not available|
langlookup|Temp|Temp|
langlookup|Hum|Hum|
langlookup|DewPT|DewPT|
langlookup|Baro|Baro|
langlookup|About the Global Map|About the Global Map|
langlookup|Affiliated Regional Weather Networks|Affiliated Regional Weather Networks|
