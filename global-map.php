<?php
############################################################################
# Sample PHP Affiliated Regional Networks Global Map
# Author: Ken True - 27-Nov-2013  http://saratoga-weather.org/
#
# Version 4.00 - 12-Aug-2018 - rewrite to use Leaflet/OpenStreetMaps+others for map display
#
# note: settings for this script should be done in global-map-settings.php, not here.
############################################################################

require_once("global-map-settings.php");

$showGizmo = false; // needed to fake-out lack of template support
header('Content-type: text/html; charset='.GMNET_CHARSET);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo GMNET_CHARSET; ?>" />
<title><?php langtrans('Global Affiliated Regional Weather Networks'); ?></title>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<?php echo "<!-- lang=$lang used - Google Lang=$Lang -->\n"; ?>
<link rel="stylesheet" href="global-map.css"/>
<script src="global-conditions-json.php" type="text/javascript"></script>
<script src="global-map.js" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style type="text/css">
body {
  color: black;
  background-color: #F3F2EB;
  font-family: verdana, helvetica, arial, sans-serif;
  font-size: 73%;  /* Enables font size scaling in MSIE */
  margin: 0;
  padding: 0;
}

html > body {
  font-size: 9pt;
}

#page {
        margin: 20px 20px;
        color: black;
        background-color: white;
        padding: 0 0 0 2em;
        width: 93%;
        border: 1px solid #959596;
}

</style>
</head>
<body>
<div id="page">
<h1><?php langtrans('Global Affiliated Regional Weather Networks'); ?></h1>  
<?php
  if(file_exists("global-map-inc.php")) { 
    include_once("global-map-inc.php"); 
  } else {
	print "<p>Sorry. The Global Mesonet map is not currently available.</p>\n";  
  }
?>
</div>
</body>
</html>