<html>
<head>
<meta charset="UTF-8">
<title>JTV Recording Viewer</title>
<link rel="stylesheet" type="text/css" href="css/recordingList.css" />
<link rel="stylesheet" type="text/css" href="css/responsiveSlides.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script src="responsiveSlides.min.js"></script>
<?php
//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL);
	date_default_timezone_set('America/New_York');
?>
<style>
.title {
	border: 0px;
}

.img, .rslides {
	background:url('css/trans_25_1px.png') repeat;
	width: 240px;
	height: 180px;
}

.selectedRec {
	-moz-box-shadow: 0 0 3px 3px #0A5;
	-webkit-box-shadow: 0 0 3px 3px#0A5;
	box-shadow: 0 0 3px 3px #0A5;
}
</style>
</head>
<body>
<script type="text/javascript">
<?php echo "var theStream = '" . $_GET['stream'] . "';\n"; ?>
<?php 
	echo "var selRec = '#"; 
	if(isset($_GET['sel'])) {
		echo $_GET['sel']; } 
	echo "';\n"; 
	
	if(isset($_GET['site'])) {
		echo "var site = '" . $_GET['site'] . "';";
	} else {
		echo "var site = 'ttv';";
	}
?>

function secondsToTime(seconds) {
	var t = seconds
	var h = Math.floor(t / 3600);
	t %= 3600;
	var m = Math.floor(t / 60);
	var s = t % 60;
	
	var hr = ( h == 1 ? ' hour ' : ' hours ')
	var mn = ( m == 1 ? ' minute ' : ' minutes ')
	var se = ( s == 1 ? ' second' : ' seconds')
	return (h > 0 ? h + hr : '') +
		(m > 0 ? m + mn : '') +
		s + se;
}

$(document).ready(function(){
	$(".rslides").responsiveSlides();
	$(".recThumb").mouseup(function(e) { loadRec(this, e.button); });
	
	if(selRec != "#"){
		loadRec($(selRec));
	}
	
	$("#getStream").submit(function(e){
		var whichStream = $("#stream").val();
		window.location.replace("recPics.php?stream="+whichStream);
		
		e.preventDefault();
		return false;
	});
});

function loadRec(recThumb, which){
	var data = $(recThumb).find('#data').text();
	var parsed = jQuery.parseJSON(data);
	
	//Add a highlight so you know what you've clicked when scrolling back down.
	//But first, remove anything else hightlighted.
	$(".selectedRec").each( function() {
		$(this).removeClass("selectedRec");
	});
	$(recThumb).addClass("selectedRec");
	
	$('.title').html(parsed.title);
	$('.length').html("Duration: " + secondsToTime(parsed.length));
//	$('.numParts').html(parsed.parts + ' parts');
	$('.desc').html(parsed.created);
	$('.img').html("<ul class='mainSlide'>" + $(recThumb).find('.rslides').html() + "</ul>");
	$(".mainSlide").responsiveSlides();
		
	$('.title').css("border","1px solid #00A");
	$('.title').off();
	$('.title').on("click",function(button) { makePopup(parsed.recID); });
	
	//If the recording was middle clicked, open a popup straight away.
	//if( which == 1 ) { makePopup(parsed.recID); }
}

function makePopup(recID) {
    var width = 640;
    var height = 360;
    var left = 30;
    var top = 500;
	var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
	
	var pathname = window.location.pathname;
	var page = "";
	if( site == "ttv" ) {
		//page = 'http://www.twitch.tv/' + theStream + '/popout?videoId=' + recID;
        page = "/ls.php?s=" + theStream + ":" + recID + ",rec";
	} else if( site == "sma" ) {
		page = 'http://smashcast.tv/';
		//Not implemented yet.
	} else {
		alert("Site not supported: " + site);
	}

    var host = "http://"+window.location.host;
	if (pathname.indexOf("/dev/") > -1 || pathname.indexOf("/development/") > -1) {
		host = host + "/development"; }
	windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
	window.open(host + page, theStream + " recording #" + recID, windowFeatures);
}
</script>
<?php
function loadRecordings($streamName){
    $options = array(
        "http"=>array(
            "method"=>"GET",
            "header"=>"Client-ID: glrjuvya5xmg8batkujqtfij53q073h\r\n"
        )
    );
    
    $context = stream_context_create($options);
    
	$recordings = @file_get_contents('https://api.twitch.tv/kraken/channels/' . $streamName . '/videos?limit=100&broadcasts=true', false, $context);
	$recordings = json_decode($recordings, true);
	$recordings = $recordings["videos"];
	$extraRec = @file_get_contents('https://api.twitch.tv/kraken/channels/' . $streamName . '/videos?limit=100', false, $context);
	$extraRec = json_decode($extraRec, true);
    //echo $spareThing;
	$recordings = array_merge($recordings, $extraRec["videos"]);
	$count = 0;
	$newList = array();
	
	foreach($recordings as $record) {
		$newList[strtotime($record['recorded_at'])] = $record;
	}
	krsort($newList);
	
	foreach($newList as $record) {
		$recList[$count] = array("title" => $record["title"], "created" => $record["recorded_at"], "length" => $record["length"], "ID" => $record["_id"], "thumb" => $record["preview"], "desc" => "Game: " . $record["game"]);
		$count++;
	}
	
	makeTwitchList($recList);
}

function makeTwitchList($recList){
	foreach($recList as $recording){
		$title = $thumb = $desc = $created = "";
		
		$length = $recording["length"];
		$title = $recording["title"];
		$desc = $recording["desc"];
		$created = $recording["created"];
		$created = substr("Recorded on " . str_replace("T"," at ",$created), 0, -1);
		$ID = $recording["ID"];
		$thumb = "<img src='" . $recording["thumb"] . "' alt='error'>";
		
		makeEntry($thumb, $title, $created, $desc, $length, $ID);
	}
}

function makeEntry($thumb, $title, $created, $desc, $length, $ID){
	echo "<div id='" . $ID . "' class='recThumb'>";
	echo "<div id='imgTitle' class='nowrap'>" . $title . "</div>";
    $title = str_replace('"', '\"', $title);
	echo '<div id="data" class="hidden">{"title": "' . $title.'","parts": 0, "recID": "' . $ID . '","created": "' . $created . '","length": ' . $length . '}</div>';
	echo "<ul class='rslides'>";
	echo $thumb;
	echo "</ul>";
	echo "<div id='time'>" . $created . ", " . gmdate("H:i:s", $length) . " long</div>";
	echo "</div>\n";
}

if(isset($_GET["stream"]) && $_GET["stream"] != ""){
	$thumb = "css/trans_75_1px.png";
	$title = "Left click a picture to load a recording's info or middle click to open a popup.";
	$parts = $length = $ID = 0;
	$desc = "After clicking picture, click recording title to open popup to the recording.";
	include 'recTemplate.php';
	loadRecordings($_GET["stream"]);
} else {

	echo "<font color=#FFF><h1>No stream given.</h1>";
	echo "Enter a Twitch.TV stream name to fetch its 100 latest recordings.</font><br />";
	echo "<form id='getStream'>";
	echo "<input type=\"text\" name=\"stream\" id=\"stream\" />";
	echo "<input type=\"submit\" value=\"Get Recordings\" name=\"submit\" id=\"submit\" />";
	echo "</form>";
 }
?>
</body>
</html>