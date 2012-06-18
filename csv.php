<?php
define("ZDAPIKEY", "key");
define("ZDUSER", "username");
define("ZDURL", "https://ajpspoke.zendesk.com/api/v2");
function curlWrap($url, $json, $action)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
	curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
	curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
	switch($action){
		case "POST":
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			break;
		case "GET":
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			break;
		case "PUT":
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		default:
			break;
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$output = curl_exec($ch);
	curl_close($ch);
	$decoded = json_decode($output);
	return $decoded;
}
/* ticket dumper */
$myFile = "users.csv";
$fh = fopen($myFile, 'w');
date_default_timezone_set('UTC');

/* See note below */ 

$time = strtotime("-8 hours");
$decoded = curlWrap("/exports/tickets/sample.json?start_time=1332034771"/*.$time*/, null, "GET");

/* when you are ready for production use you can remove the sample.json and make it 
 * just exports/tickets.json. Make sure the final line looks as follows:
 * 
 * $decoded = curlWrap("/exports/tickets.json?start_time=".$time, null, "GET");
 *
 * You set the timestamp to whatever you like using $time = strtotime("-20 minutes") as an example of 
 * all tickets from now to twenty minutes ago. 
 *
 */
 
 
$first = $decoded->results[0];
$first = (array) $first;
$keys = array_keys($first);
fputcsv($fh, $keys, ",", "\"");
foreach($decoded->results as $result){
	$result = (array) $result;	
	fputcsv($fh, $result, ",", "\"");
}

fclose($fh);
?>