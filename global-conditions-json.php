<?php
// proxy fetch for global-conditions.json from http://www.northamericanweather.net/ for the
// Global station map
// Ken True - webmaster@saratoga-weather.org
// Version 1.00 - 17-Jul-2010 - initial release for Google map 
// Version 1.01 - 20-Jul-2010 - added target dir and self-downloader
// Version 1.02 - 27-Nov-2013 - support for Google map V3 with Global Map V2.00
// Version 4.00 - 12-Aug-2018 - rewrite for Leaflet/OpenStreetMaps
// Version 4.02 - 11-Feb-2019 - update to support HTTP/2 returns
// settings -------------------------------------------------------------------
$refreshTime = 300;  // 10 minute cache time
$targetDir = './';   // target directory for cache files with trailing '/'
//-----------------------------------------------------------------------------
$Version = 'global-conditions-json.php V4.02 - 11-Feb-2019';

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

$masterHost = "https://www.northamericanweather.net/";
$cacheName = 'global-conditions.json';


$URL = $masterHost . $cacheName;

if (file_exists($targetDir.$cacheName) and 
	filemtime($targetDir.$cacheName) + $refreshTime > time() and filesize($targetDir.$cacheName) > 100) {
  header("Content-type: application/javascript; charset=ISO-8859-1");
	header("Cache-Control: no-cache,no-store,  must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	$NOWdate = gmdate("D, d M Y H:i:s", time());
	$udate = gmdate("D, d M Y H:i:s", filemtime($cacheName));
	header("Expires: $NOWdate GMT");
	header("Last-Modified: $udate GMT");
	readfile($cacheName);
    print "// cache lmod=$udate GMT -->\n";
    return;
}


  $rawhtml = GMAP_fetchUrlWithoutHanging($URL,false);
  
  list($headers,$html) = explode("\r\n\r\n",$rawhtml);
  $RC = '';
  if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
	$RC = trim($matches[1]);
  }

  if(preg_match('|200|',$RC) and strlen($html) > 100) {
    header("Content-type: application/javascript; charset=ISO-8859-1");
		header("Cache-Control: no-cache,no-store,  must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		$NOWdate = gmdate("D, d M Y H:i:s", time());
		header("Expires: $NOWdate GMT");
	
		$udate = gmdate("D, d M Y H:i:s", time()) . " GMT";
		if(preg_match('|\nLast-Modified: (.*)\n|Ui',$headers,$match)) {
			$udate = trim($match[1]);
		}
		header("Last-Modified: $udate");
		print $html;
		print "// -- lmod=$udate\n";
		print "// -- $Version \n";
 
   $fp = fopen($targetDir.$cacheName, "w");
   if ($fp) {
		$write = fputs($fp, $html);
		fclose($fp);
   } else {
		print "// unable to write cache file $targetDir$cacheName \n";
   }
 
  
  } else {
    print "Problem fetching $targetDir$cacheName with RC=$RC , html length=".strlen($html) . "<br/>\n";
	print "Headers returned are:\n";
	print "<pre>\n$headers\n</pre>\n";
	print "cache not saved.\n";
   }

exit;

function GMAP_fetchUrlWithoutHanging($url,$useFopen) {
// get contents from one URL and return as string 
  global $Status, $needCookie;
  
  $overall_start = time();
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=6;   

// Thanks to Curly from ricksturf.com for the cURL fetch functions

  $data = '';
  $domain = parse_url($url,PHP_URL_HOST);
  $theURL = str_replace('nocache','?'.$overall_start,$url);        // add cache-buster to URL if needed
  $Status .= "<!-- curl fetching '$theURL' -->\n";
  $ch = curl_init();                                           // initialize a cURL session
  curl_setopt($ch, CURLOPT_URL, $theURL);                         // connect to provided URL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                 // don't verify peer certificate
  curl_setopt($ch, CURLOPT_USERAGENT, 
    'Mozilla/5.0 (get-UV-forecast-inc.php - saratoga-weather.org)');

  curl_setopt($ch,CURLOPT_HTTPHEADER,                          // request LD-JSON format
     array (
         "Accept: text/html,text/plain"
     ));

  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $numberOfSeconds);  //  connection timeout
  curl_setopt($ch, CURLOPT_TIMEOUT, $numberOfSeconds);         //  data timeout
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);              // return the data transfer
  curl_setopt($ch, CURLOPT_NOBODY, false);                     // set nobody
  curl_setopt($ch, CURLOPT_HEADER, true);                      // include header information
//  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);              // follow Location: redirect
//  curl_setopt($ch, CURLOPT_MAXREDIRS, 1);                      //   but only one time
  if (isset($needCookie[$domain])) {
    curl_setopt($ch, $needCookie[$domain]);                    // set the cookie for this request
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);             // and ignore prior cookies
    $Status .=  "<!-- cookie used '" . $needCookie[$domain] . "' for GET to $domain -->\n";
  }

  $data = curl_exec($ch);                                      // execute session

  if(curl_error($ch) <> '') {                                  // IF there is an error
   $Status .= "<!-- curl Error: ". curl_error($ch) ." -->\n";        //  display error notice
  }
  $cinfo = curl_getinfo($ch);                                  // get info on curl exec.
/*
curl info sample
Array
(
[url] => http://saratoga-weather.net/clientraw.txt
[content_type] => text/plain
[http_code] => 200
[header_size] => 266
[request_size] => 141
[filetime] => -1
[ssl_verify_result] => 0
[redirect_count] => 0
  [total_time] => 0.125
  [namelookup_time] => 0.016
  [connect_time] => 0.063
[pretransfer_time] => 0.063
[size_upload] => 0
[size_download] => 758
[speed_download] => 6064
[speed_upload] => 0
[download_content_length] => 758
[upload_content_length] => -1
  [starttransfer_time] => 0.125
[redirect_time] => 0
[redirect_url] =>
[primary_ip] => 74.208.149.102
[certinfo] => Array
(
)

[primary_port] => 80
[local_ip] => 192.168.1.104
[local_port] => 54156
)
*/
  $Status .= "<!-- HTTP stats: " .
    " RC=".$cinfo['http_code'];
	if(isset($cinfo['primary_ip'])) {
		$Status .= " dest=".$cinfo['primary_ip'] ;
	}
	if(isset($cinfo['primary_port'])) { 
	  $Status .= " port=".$cinfo['primary_port'] ;
	}
	if(isset($cinfo['local_ip'])) {
	  $Status .= " (from sce=" . $cinfo['local_ip'] . ")";
	}
	$Status .= 
	"\n      Times:" .
    " dns=".sprintf("%01.3f",round($cinfo['namelookup_time'],3)).
    " conn=".sprintf("%01.3f",round($cinfo['connect_time'],3)).
    " pxfer=".sprintf("%01.3f",round($cinfo['pretransfer_time'],3));
	if($cinfo['total_time'] - $cinfo['pretransfer_time'] > 0.0000) {
	  $Status .=
	  " get=". sprintf("%01.3f",round($cinfo['total_time'] - $cinfo['pretransfer_time'],3));
	}
    $Status .= " total=".sprintf("%01.3f",round($cinfo['total_time'],3)) .
    " secs -->\n";

  //$Status .= "<!-- curl info\n".print_r($cinfo,true)." -->\n";
  curl_close($ch);                                              // close the cURL session
  //$Status .= "<!-- raw data\n".$data."\n -->\n"; 
  $i = strpos($data,"\r\n\r\n");
  $headers = substr($data,0,$i);
  $content = substr($data,$i+4);
  if($cinfo['http_code'] <> '200') {
    $Status .= "<!-- headers returned:\n".$headers."\n -->\n"; 
  }
  return $data;                                                 // return headers+contents

 } else {
//   print "<!-- using file_get_contents function -->\n";
   $STRopts = array(
	  'http'=>array(
	  'method'=>"GET",
	  'protocol_version' => 1.1,
	  'header'=>"Cache-Control: no-cache, must-revalidate\r\n" .
				"Cache-control: max-age=0\r\n" .
				"Connection: close\r\n" .
				"User-agent: Mozilla/5.0 (get-UV-forecast-inc.php - saratoga-weather.org)\r\n" .
				"Accept: text/html,text/plain\r\n"
	  ),
	  'https'=>array(
	  'method'=>"GET",
	  'protocol_version' => 1.1,
	  'header'=>"Cache-Control: no-cache, must-revalidate\r\n" .
				"Cache-control: max-age=0\r\n" .
				"Connection: close\r\n" .
				"User-agent: Mozilla/5.0 (get-UV-forecast-inc.php - saratoga-weather.org)\r\n" .
				"Accept: text/html,text/plain\r\n"
	  )
	);
	
   $STRcontext = stream_context_create($STRopts);

   $T_start = GMAP_fetch_microtime();
   $xml = file_get_contents($url,false,$STRcontext);
   $T_close = GMAP_fetch_microtime();
   $headerarray = get_headers($url,0);
   $theaders = join("\r\n",$headerarray);
   $xml = $theaders . "\r\n\r\n" . $xml;

   $ms_total = sprintf("%01.3f",round($T_close - $T_start,3)); 
   $Status .= "<!-- file_get_contents() stats: total=$ms_total secs -->\n";
   $Status .= "<-- get_headers returns\n".$theaders."\n -->\n";
//   print " file() stats: total=$ms_total secs.\n";
   $overall_end = time();
   $overall_elapsed =   $overall_end - $overall_start;
   $Status .= "<!-- fetch function elapsed= $overall_elapsed secs. -->\n"; 
//   print "fetch function elapsed= $overall_elapsed secs.\n"; 
   return($xml);
 }

}    // end GMAP_fetchUrlWithoutHanging
// ------------------------------------------------------------------

function GMAP_fetch_microtime()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
   
?>