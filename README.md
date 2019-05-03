# Leaflet/OpenStreetMaps Global mesonet display

The **[Affiliated Regional Weather Networks](https://www.northamericanweather.net/)** have been quite popular, and now it is possible for you to add a Leaflet/OpenStreetMaps map display of conditions from around the world to your own website. We do _prefer_ that the script(s) be used by members who contribute their data to a Regional Weather Network, but all are welcome to use this script set if they like.  

Version 4.00 of the script set no longer requires an API key for map display. Support is provided for Mapbox.com map tile displays if you have an Access Token, but 5 supplied maps are 'API key free'.

Here's a sample of the display:

# Installation/Configuration

The map package (below) comes in one basic configuration:

*   A PHP map for standalone or a plugin for the Saratoga AJAX/PHP templates (V2 and V3 compatible)

The two support scripts (global-links.php and global-conditions-json.php ) are included to help minimize the access time to draw your map page by caching some files from the [Affiliated Regional Weather Networks home site](https://www.northamericanweather.net/) locally on your website. These files are:

*   _network-list-inc.html_
*   _network-links-inc.html_
*   _member-count.txt_
*   _members-list-inc.html_
*   _global-conditions.json_

Starter files of the above are included with the PHP distributions with content that lets you know that the cache file has not been written. Depending on your PHP installation, the above files may need to have permissions set to 666 (write/read by all) in order for the caching to work. If you do a 'view-source' on the PHP page with the scripts in it, it should show any error messages encoutered as HTML comments.

For the Saratoga multilingual templates, you may need to add to your _language-**LL**-local.txt_ files the translations for:

```
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
```

Which will allow for the translation of the headings and pop-up station data boxes to language _**LL**_. You may also need to copy _wxglobal-en_.html to _wxglobal-**LL**.html_ and translate the text inside from English to language _**LL**_ so the rest of the page will display correctly. Be sure to use the correct character set for your page to match the other translation files used by the Multilingual templates. The multilingual plugin will also read the corresponding _language-**LL**.js_ JavaScript so the conditions, wind-directions and barometric pressure trend can be translated in the popup conditions windows.  
**Note**: if you create a translation for the multilingual template map, please **[let me know](/contact.php)** so I can include it in the global-map-template-ML distribution so others can use it too. Thanks in advance!

Version 4.00 now has configuration in one file _global-map-settings.php_. See the comments in that file to perform your own configuration.  
See the [_global-map-README.txt_](global-map-README.txt) file for more information about installation.  
Also new in V4.00 are rotating conditions display under the station pins. This feature can be disabled in the configuration if you find it too 'busy' a display.
