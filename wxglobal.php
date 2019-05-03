<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (V2/V3 template sets)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
#   Copyright:  (c) 1992-2007 Copyright TNET Services, Inc.
############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
// Version 2.00 - 27-Nov-2013 - initial release of Global Map V2.00 with Google API V3
// Version 2.01 - 05-Jul-2016 - added support for required Google Browser JavaScript API key
// Version 4.00 - 12-Aug-2018 - rewrite to use Leaflet/OpenStreetMaps+others for map display
############################################################################
#   This document uses Tab 4 Settings
############################################################################
require_once("Settings.php");
require_once("common.php");
include_once("global-map-settings.php");############################################################################
$TITLE= $SITE['organ'] . " - " .langtransstr("Global Stations Map");
$showGizmo = false;  // set to false to exclude the gizmo
$keywords = "World weather,Global weather, live, data";
include("top.php");
############################################################################
// Settings for are in global-map-settings.php, not here
?>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<script src="global-conditions-json.php" type="text/javascript"></script>
<script src="global-map.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="global-map.css" media="screen" />
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
?>

<div id="main-copy">

<?php
  if(file_exists("global-map-inc.php")) { 
    include_once("global-map-inc.php"); 
  } else {
	print "<p>Sorry. The Global map is not currently available.</p>\n";  
  }
?>

</div><!-- end main-copy -->
<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
