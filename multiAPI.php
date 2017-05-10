<?php
/*Page to do multiple API requests at once based on site.
 *Inputs:
 *	Site: TTV, LST, UST, SMA
 *	Type: Status (more TBA, default if excluded)
 *	Streams: Comma-separated list of streams to check
 *
 *  Used to be used in a WIP version of check.htm, but didn't work out
*/

ini_set('display_errors', 'On');
error_reporting(E_ALL);


function curlRequest($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	if( $output === false ){ die(curl_error($ch)); }
	curl_close($ch);
	
	return $output;
}

$validSites = array("ttv","lst","utv","sma");
$validTypes = array("status");

$twitchAPI = 'https://api.twitch.tv/kraken/streams/';
$livestreamAPI = '.api.channel.livestream.com/2.0/';
$ustreamAPI = 'http://api.ustream.tv/json/channel/';
$smashcastAPI = 'http://api.smashcast.tv/media/live/';

//This speeds up file_get_contents immensely.
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));

$urlVars = $_GET;
foreach($urlVars as $key => $val) {
	if( $key == "site" ) { $site = $val; }
	if( $key == "type" ) { $type = $val; }
	if( $key == "streams" ) { $streams = explode(",", $val); }
}

if( !isset($site) ){
	die("No site given.");
}

if( !isset($type) ){
	$type = "status";
}

if( isset($type) && !in_array($type, $validTypes)){
	die('{"live":"error", "error":"Request type not supported: \'' . $type . '\'}');
}

if( !in_array($site, $validSites) ){
	die('{"live":"error", "error":"Site not supported: \'' . $site . '\'"}');
}

if( !isset($streams) ){
	die("No streams given.");
}

if( isset($streams) && count($streams) < 1 ){
	die("No streams given.");
}

$responseJSON = array();

foreach($streams as $index => $val){
	$tempArray = array();
	$tempArray["stream"] = $val;
	if( $site == "ttv" ){
		if( $type == "status" ){
			$response = json_decode(@curlRequest($twitchAPI . $val . "/"), true);
			if( !$response ){
				$tempArray['live'] = 'error';
				$tempArray['error'] = error_get_last();
				$tempArray['error'] = explode(":",$tempArray['error']['message']);
				$tempArray['error'] = $tempArray['error'][3];
			} else {
				if( $response['stream'] == null ){
					$tempArray['live'] = false;
				} else {
					$tempArray['live'] = true;
					$tempArray['viewers'] = $response["stream"]["viewers"];
					$tempArray['topic'] = $response["stream"]["channel"]["status"];
					$tempArray['game'] = $response["stream"]["game"];
				}
			}
		}
	} elseif( $site == "sma" ){
		if( $type == "status" ){
			$response = json_decode(@curlRequest($smashcastAPI . $val . "?nocache=true"), true);
			if( !$response ){
				$tempArray['live'] = 'error';
				$tempArray['error'] = error_get_last();
				$tempArray['error'] = explode(":",$tempArray['error']['message']);
				$tempArray['error'] = $tempArray['error'][3];
			} else {
				if( $response['livestream'][0]['media_is_live'] == 0 ){
					$tempArray['live'] = false;
				} else {
					$tempArray['live'] = true;
					$tempArray['viewers'] = $response['livestream'][0]['media_views'];
					$tempArray['topic'] = $response['livestream'][0]['media_status'];
					$tempArray['game'] = $response['livestream'][0]['category_name'];
				}
			}
		}
	} elseif( $site == "lst" ){
		if( $type == "status" ){
			$val = "x" . $val . "x";
			$response = json_decode(@curlRequest('http://' . $val . $livestreamAPI . 'info.json'), true);
			if( !$response ){
				$tempArray['live'] = 'error';
				$tempArray['error'] = error_get_last();
				$tempArray['error'] = explode(":",$tempArray['error']['message']);
				$tempArray['error'] = $tempArray['error'][3];
			} else {
				if( $response['channel']['isLive'] == false ){
					$tempArray['live'] = false;
				} else {
					$tempArray['live'] = true;
					$tempArray['viewers'] = $response['channel']['currentViewerCount'];
				}
			}
		}
	} elseif( $site == "utv" ){
		if( $type == "status" ){
			$response = json_decode(@curlRequest($ustreamAPI . $val . '/getInfo'), true);
			if( !$response ){
				$tempArray['live'] = 'error';
				$tempArray['error'] = error_get_last();
				$tempArray['error'] = explode(":",$tempArray['error']['message']);
				$tempArray['error'] = $tempArray['error'][3];
			} else {
				if( $response['results'] == null ) {
					$tempArray['live'] = 'error';
					$tempArray['error'] = $response['msg'];
				} elseif( $response['results']['status'] !== 'live' ){
					$tempArray['live'] = false;
				} else {
					$tempArray['live'] = true;
					$tempArray['id'] = $response['results']['id'];
				}
			}
		}
	}
	$responseJSON[] = $tempArray;
}
if( count($responseJSON) == 1 ){
	$responseJSON = $responseJSON[0];
}
$responseJSON = json_encode($responseJSON);

$responseJSON = str_replace("\\r\\n","",$responseJSON);
$responseJSON = str_replace("\/","/",$responseJSON);
echo $responseJSON;
?>