<html>
<head>
<meta charset="UTF-8">
<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	date_default_timezone_set('America/New_York');
?>
<title>JTV Archive Viewer</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<style>
body { margin: 0; }
</style>
<link rel="stylesheet" type="text/css" href="css/recordingList.css" />
<script type="text/javascript">
function makePopup(stream, rec) {
    var width = 640;
    var height = 360;
    var left = 30;
    var top = 500;
	var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
	
	var pathname = window.location.pathname;
	var site = "http://"+window.location.host;
	if (pathname.indexOf("/dev/") > -1 || pathname.indexOf("/development/") > -1) {
		site = site + "/development"; }

	stream = stream.replace("-",",");
	windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
	window.open(site+"/ls.php?s=" + stream + ":" + rec + ",rec", stream + " recording #" + rec, windowFeatures);
}

$(document).ready(function(){
	$("#getStream").submit(function(e){
		var whichStream = $("#stream").val();
		window.location.replace("recList.php?stream="+whichStream);
		
		e.preventDefault();
		return false;
	});
});
</script>
<?php
//Get ID from https://api.twitch.tv/kraken/channels/*streamName*/videos?limit=100&broadcasts=true
//Might as well grab a lot, so limit 100, which is max
//Has to have broadcasts=true or it'll only grab highlights
function loadRecordings($streamName){
    //Twitch now requires a developer Client-ID to be sent in header, so I have to build a header array
    $options = array(
        "http"=>array(
            "method"=>"GET",
            "header"=>"Client-ID: glrjuvya5xmg8batkujqtfij53q073h\r\n" .
                       "Accept-language: en\r\n"
        )
    );
    
    $context = stream_context_create($options);
    
	$broadcasts = file_get_contents('https://api.twitch.tv/kraken/channels/' . $streamName . '/videos?limit=100&broadcasts=true', false, $context);
	$broadcasts = json_decode($broadcasts, true);
	$broadcasts = $broadcasts["videos"];
		
	foreach($broadcasts as $record) {
		unset($record["_links"]);
		unset($record["channel"]);
		$newList[strtotime($record['recorded_at'])] = $record;
	}
	
    if( true ){
        krsort($newList);
	
        $recList[0] = array();
        foreach($newList as $record) {
            $recList[] = array("title" => $record["title"], "created" => $record["recorded_at"], "length" => $record["length"], "ID" => substr($record["_id"], 1), "thumb" => $record["preview"], "desc" => "Game: " . $record["game"]);
        }
        unset($recList[0]);
        makeTwitchList($recList);
    }
}

function formatPartURL($filename, $partNum){
	$break = " &nbsp;";
	if( $partNum == 3 ){ $break = "<br \>"; }
	if($filename == "") {
		return "No Part ". $partNum . "," . $break;
	} else {
		return "<a href='" . $filename . "'>Part " . $partNum . "</a>" . $break;
	}
}

function makeTwitchList($recList){
	foreach($recList as $recording){
		$title = $thumb = $desc = $created = "";
		
		$length = $recording["length"];
		$title = $recording["title"];
		$desc = $recording["desc"];
		$created = $recording["created"];
		$created = "Recorded on " . str_replace("T"," at ",$created);
		$ID = $recording["ID"];
		$thumb = $recording["thumb"];
		
		$parts = "-1";
		
		makeEntry($thumb, $title, $created, $desc, $length, $ID, $parts);
	}
}

function makeEntry($thumb, $title, $created, $desc, $length, $ID, $parts){
	include 'recTemplate.php';
}
?>
</head>
<body>
<div id="divTest">
<?php 
if(isset($_GET["stream"]) && $_GET["stream"] != ""){ loadRecordings($_GET["stream"]);
} else {

	echo "<font color=#FFF><h1>No stream given.</h1>";
	echo "Enter a Twitch.TV stream name to fetch its 100 latest recordings.<br />Justin.TV has closed and therefore cannot be used.</font>";
	echo "<form id='getStream'>";
	echo "<input type=\"text\" name=\"stream\" id=\"stream\" />";
	echo "<input type=\"submit\" value=\"Get Recordings\" name=\"submit\" id=\"submit\" />";
	echo "</form>";
}
?>
</div>
</body>