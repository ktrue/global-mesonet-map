<?php
############################################################################
# Main processing for Affiliated Regional Networks Global Map
#
# Version 4.00 - 12-Aug-2018 - initial release for Leaflet/OpenStreetMaps
# Version 4.01 - 12-Aug-2018 - update to use base64 decode of translation table
#
# note: settings for this script should be done in global-map-settings.php, not here.
############################################################################
$GMVersion = "Version 4.00 - 12-Aug-2018";
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}
  include_once("global-map-settings.php");
	
  global $firstPart,$secondPart,$thirdPart,$SITE, $LTRANS;
  print "<!-- global-map-inc.php - $GMVersion -->\n";
;

gmGenBoilerplate(); // load default text for first, second and third part of page display

  if (isset($SITE['lang']) and $SITE['lang'] <> 'en') {
  // handle included files for other language instructions-XX.html

   file_exists("wxglobal-".$SITE['lang'].'.html')?include_once("wxglobal-".$SITE['lang'].'.html'):
     print "<!-- Sorry, no wxglobal-".$SITE['lang'].".html version of this page can be found. -->\n";
   
	 file_exists("wxglobal-".$SITE['lang'].'.html')? print '':include_once("wxglobal-en.html");

  } else {
	  if(file_exists("wxglobal-en.html")) {include_once("wxglobal-en.html");}
  }// end of non-english inclusion

	
?>
<div class="tabber" style="width: 99%; margin: 0 auto;"><!-- MAP tab begin -->
  <div class="tabbertab" style="padding: 0;">
    <h2><?php langtrans("Map"); ?></h2>
    <div style="width: 99%;">


<div id="GMNETmap-container">
  <div id="GMNETmap">Map is loading...<br/><?php print $firstPart; ?></div>
<?php
include_once("global-map-genjs-inc.php"); // generate the JavaScript
?>
  <table width="100%" style="border: none">
  <tr>
    <?php if($doRotatingLegends) { ?>
    <td style="width: 180px">
    <form action="#">
      <div id="GMNETcontrols">
        <input type="button" value="<?php echo GMNET_RUN; ?>" name="run" onclick="GMNET_set_run(1);" />
        <input type="button" value="<?php echo GMNET_PAUSE; ?>" name="pause" onclick="GMNET_set_run(0);" />
        <input type="button" value="<?php echo GMNET_STEP; ?>" name="step" onclick="GMNET_step_content();" />
      </div>
    </form>
    <?php } else { ?>
    <td>&nbsp;
    <?php } // end no rotating legends ?>
    </td>
    <?php if($doRotatingLegends) { ?>
    <td style="text-align: center;">
    <div id="GMNETlegend">
      <span class="GMNETcontent0" style="text-align: left;"><?php echo GMNET_TEMPL; ?> [ <span id="curTempUOM"><?php print $gmTempUOM; ?></span>&deg; ]</span>
      <span class="GMNETcontent1" style="text-align: left;"><?php echo GMNET_DEWPT; ?> [ <span id="curTempUOM2"><?php print $gmTempUOM; ?></span>&deg; ]</span>
      <span class="GMNETcontent2" style="text-align: left;"><?php echo GMNET_HUML; ?> [ % ]</span>
      <span class="GMNETcontent3" style="text-align: left;"><?php echo GMNET_WIND; ?> [ <span id="curWindUOM"><?php print $gmWindUOM; ?></span> ]</span>
      <span class="GMNETcontent4" style="text-align: left;"><?php echo GMNET_PRECIPSL; ?> [ <span id="curRainUOM"><?php print $gmRainUOM; ?></span> ]</span>
      <span class="GMNETcontent5" style="text-align: left;"><?php echo GMNET_BAROB; ?> [ <span id="curBaroUOM"><?php print $gmBaroUOM; ?></span> ]</span>
  <!--    <span class="GMNETcontent6" style="text-align: left;"><?php echo GMNET_BAROT; ?></span> -->
      <?php if($gmShowFireDanger) { ?>
      <span class="GMNETcontent6" style="text-align: left;"><?php 
	    echo preg_replace('|<[^>]+>|',' ',GMNET_CBILEGEND); ?></span>
      <?php }// end ShowFireDanger headings ?>
    </div>
    <?php } else { ?>
    <td>&nbsp;
    <?php } // end no rotating legends ?>
    </td>
    <td style="text-align:right;">
    <form action="#">
      <div id="GMcontrolsUOM">
      <select id="selTemp" name="selTemp" onchange="GMNET_ChangeSelTemp(this.value);">
<?php
foreach (array('C','F') as $i => $val) {
  if($val == $gmTempUOM) {
    print "        <option value=\"$val\" selected=\"selected\">&deg;$val</option>\n";
  } else {
    print "        <option value=\"$val\">&deg;$val</option>\n";
  }
}
?>
      </select>
      <select id="selWind" name="selWind" onchange="GMNET_ChangeSelWind(this.value);">
<?php
foreach (array('km/h','mph','m/s','kts') as $i => $val) {
  if($val == $gmWindUOM) {
    print "        <option value=\"$val\" selected=\"selected\">$val</option>\n";
  } else {
    print "        <option value=\"$val\">$val</option>\n";
  }
}
?>
      </select>
      <select id="selRain" name="selRain" onchange="GMNET_ChangeSelRain(this.value);">
<?php
foreach (array('mm','in') as $i => $val) {
  if($val == $gmRainUOM) {
    print "        <option value=\"$val\" selected=\"selected\">$val</option>\n";
  } else {
    print "        <option value=\"$val\">$val</option>\n";
  }
}
?>
      </select>
      <select id="selBaro" name="selBaro" onchange="GMNET_ChangeSelBaro(this.value);">
<?php
foreach (array('hPa','inHg','mb') as $i => $val) {
  if($val == $gmBaroUOM) {
    print "        <option value=\"$val\" selected=\"selected\">$val</option>\n";
  } else {
    print "        <option value=\"$val\">$val</option>\n";
  }
}
?>
      </select>
      </div>
    </form>
    </td>
   </tr>
  </table>
  </div>

<?php if(isset($firstPart)) { print $firstPart; } ?>

<p><small>[<img src="./MESO-images/mma_20_red.png" height="20" width="12" alt="Weather, Webcam, Lightning" style="vertical-align:middle"/>] <?php gmLTS('Weather, Lightning, WebCam'); ?>,

[<img src="./MESO-images/mma_20_yellow.png" height="20" width="12" alt="Weather, Lightning" style="vertical-align:middle"/>] <?php gmLTS('Weather, Lightning'); ?>,

[<img src="./MESO-images/mma_20_green.png" height="20" width="12" alt="Weather, Webcam" style="vertical-align:middle"/>] <?php gmLTS('Weather, WebCam'); ?>,

[<img src="./MESO-images/mma_20_blue.png" height="20" width="12" alt="Weather"  style="vertical-align:middle"/>] <?php gmLTS('Weather'); ?></small> 
</p>

  <?php print $secondPart; ?>
 </div> <!-- end map display area tab -->
 <?php print "<!-- $GMVersion; -->\n"; 
 $sTarget = $doLinkTarget?' target="_blank"':'';
 ?> 
 <p><small>Global mesonet-map script by 
 <a href="https://saratoga-weather.org/scripts-mesomap.php"<?php echo $sTarget; ?>>Saratoga-Weather.org</a></small></p>
 </div> <!-- end first tab --> 
  <div class="tabbertab" style="padding: 0;"><!-- begin second tab -->
    <h3><?php langtrans("Regional Mesonets"); ?></h3>
    <div style="width: 99%;">
<?php
 if(file_exists($netLinksPath."global-links.php")) {
   include_once($netLinksPath."global-links.php");
   print " <h3>".langtransstr('Affiliated Regional Weather Networks')."</h3>\n";
   print "<p>"; include($netLinksPath."member-count.txt"); print "</p>\n";
   include($netLinksPath."network-list-inc.html");
 }

   if(isset($thirdPart)) {print $thirdPart; }
?>	 
  <p>Regional Networks created by <a href="https://saratoga-weather.org/"<?php echo $sTarget; ?>>Saratoga-Weather.org</a> along with the Global Afilliated Regional Networks hub site at <a href="https://www.northamericanweather.net/"<?php echo $sTarget; ?>>NorthAmericanWeather.net</a>. 
  [<a href="https://www.northamericanweather.net/about.php"<?php echo $sTarget; ?>>About</a>]</p>
    </div><!-- end Regional Mesonet list include -->
  </div><!-- end second tab -->
</div><!-- end tabs display -->
<?php	 
// --- functions   
function gmGenBoilerplate () {
	global $firstPart,$secondPart,$thirdPart;
	
 # The $firstPart is what gets printed when the page is first presented.
 $firstPart = <<<EOT

    <noscript><b>JavaScript must be enabled in order for you to use the map.</b> 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view the maps, enable JavaScript by changing your browser options, and then 
      try again.
    </noscript>

EOT;
// do not remove the above EOT line

 # The $secondPart is what gets printed under the legend on the map.
 
 $secondPart = <<<EOT
<p>This map shows the locations of current affiliated regional weather network member stations.</p>
<p><span style="width: 25px; height: 25px; background-color: rgba(110, 204, 57, 0.6); border-radius: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Markers with numbers indicate clusters of stations - click to zoom the map to show station markers.
If you click on a marker for a station, a descriptive window will open and show the station features,
a link to the station&quote;s homepage, the regional network affiliations for the station, 
and current conditions at the station (where available).</p>

EOT;
// do not remove the above EOT line

 # The $thirdPart is what gets printed at the bottom of the page.
 
 $thirdPart = <<<EOT

<p><small>Map data from <a href="https://www.northamericanweather.net/">Affiliated Regional Networks</a> and scripts from
<a href="https://saratoga-weather.org/">Saratoga-Weather.org</a>.<br/>
If you have a personal weather station publishing to a personal weather website, you can submit a request to have your
data included in this display by visiting the network for your geography from the list above.</small></p>

EOT;
// do not remove the above EOT line
} 
function GMNET_genTranslate($lang) {
	global $LTRANS,$L;
	if(isset($L[$lang]['text']['charset'])) {
		$LT = $L[$lang];
	} else {
		$LT = $L['en']; // use English as the default for missing language
	}
// Generate the JavaScript lookups for wind, features, Baro Trend, Conditions from
// the associated [netid]-meso-lang-LL.txt configuration file.	
	$txtdir =  array(
	'N' => 'N',
	'NNE' => 'NNE',
	'NE' => 'NE',
	'ENE' => 'ENE',
	'E' => 'E',
	'ESE' => 'ESE',
	'SE' => 'SE',
	'SSE' => 'SSE',
	'S' => 'S',
	'SSW' => 'SSW',
	'SW' => 'SW',
	'WSW' => 'WSW',
	'W' => 'W',
	'WNW' => 'WNW',
	'NW' => 'NW',
	'NNW' => 'NNW',
	);
	
	foreach ($txtdir as $key) {
		if(isset($LT['winddir'][$key])) {
			$val = $LT['winddir'][$key];
		} else {
			$val = $key;
		}
		print "  langTransLookup['$key'] = '$val';\n";
		$LTRANS[$key] = $val;

	
	}
	
	$txtbarot =  array(  
	'Rising' => 'Rising',
	'Falling' => 'Falling',
	'Rising Rapidly' => 'Rising Rapidly',
	'Falling Rapidly' => 'Falling Rapidly',
	'Rising Slowly' => 'Rising Slowly',
	'Falling Slowly' => 'Falling Slowly',
	'Steady' => 'Steady'
	);
	foreach ($txtbarot as $key) {
		if(isset($LT['barotrend'][$key])) {
			$val = $LT['barotrend'][$key];
		} else {
			$val = $key;
		}
		print "  langTransLookup['$key'] = '$val';\n";
		$LTRANS[$key] = $val;

	
	}
	
	$txtfeat =  array(  
	'Weather, Lightning, WebCam' => 'Weather, WebCam, Lightning',
	'Weather, WebCam, Lightning' => 'Weather, WebCam, Lightning',
	'Weather, WebCam' => 'Weather, WebCam',
	'Weather, Lightning' => 'Weather, Lightning',
	'Weather' => 'Weather'
	);
	foreach ($txtfeat as $key => $val2) {
		if(isset($LT['features'][$key])) {
			$val = $LT['features'][$key];
		} else {
			$val = $val2;
		}
		print "  langTransLookup['$key'] = '$val';\n";
		$LTRANS[$key] = $val;

	
	}
	$txticon =  array( 
	'Sunny' =>  'Sunny',
	'Clear' =>  'Clear',
	'Cloudy' =>  'Cloudy',
	'Cloudy2' =>  'Cloudy2',
	'Partly Cloudy' =>  'Partly Cloudy',
	'Dry' =>  'Dry',
	'Fog' =>  'Fog',
	'Haze' =>  'Haze',
	'Heavy Rain' =>  'Heavy Rain',
	'Mainly Fine' =>  'Mainly Fine',
	'Mist' => 'Mist',
	'Fog' => 'Fog',
	'Heavy Rain' => 'Heavy Rain',
	'Overcast' => 'Overcast',
	'Rain' => 'Rain',
	'Showers' => 'Showers',
	'Snow' => 'Snow',
	'Thunder' => 'Thunder',
	'Overcast' => 'Overcast',
	'Partly Cloudy' => 'Partly Cloudy',
	'Rain' => 'Rain',
	'Rain2' => 'Rain2',
	'Showers2' => 'Showers2',
	'Sleet' => 'Sleet',
	'Sleet Showers' => 'Sleet Showers',
	'Snow' => 'Snow',
	'Snow Melt' => 'Snow Melt',
	'Snow Showers2' => 'Snow Showers2',
	'Sunny' => 'Sunny',
	'Thunder Showers' => 'Thunder Showers',
	'Thunder Showers2' => 'Thunder Showers2',
	'Thunder Storms' => 'Thunder Storms',
	'Tornado' => 'Tornado',
	'Windy' => 'Windy',
	'Stopped Raining' => 'Stopped Raining',
	'Wind/Rain' => 'Wind/Rain'
	);
	foreach ($txticon as $key) {
		if(isset($LT['condtext'][$key])) {
			$val = $LT['condtext'][$key];
		} else {
			$val = $key;
		}
		$t = str_replace("'","\'",$val);
		
		print "  langTransLookup['$key'] = '$t';\n";
		$LTRANS[$key] = $val;

	}

	return;
}
# built-in quickie langtrans using the global-map-lang-inc.php language arrays
function gmLTS($instr) {
	global $LTRANS;
	if(isset($LTRANS[$instr])) { 
	  print $LTRANS[$instr]; 
	} else {
		print $instr;
	}
}

// end of global-map-inc.php