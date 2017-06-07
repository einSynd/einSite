<html>
<head>
<meta charset="UTF-8">
<?php
	//Make a handy little warning icon to ensure it's the test page when testing
	$title = "Stream Popup Launcher";
	$uri = $_SERVER['REQUEST_URI'];
	if(strstr($uri,"/dev/") || strstr($uri, "/development/")) {
		$title = "/!\\ " . $title . " - TESTING /!\\";
	}
	echo "<title>" . $title . "</title>";
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<style>
.disabledText {
	background: #666;
	color: #CCC;
	border: 1px solid #333;
}

#dropdownIcon1, #dropdownIcon2 { display: inline-block; }
</style>
</head>
<body bgcolor="#99999">
<?php 
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_NOTICE);
	
	$streams = array(
		"stream" => "",
		"stream1" => "",
		"stream1site" => "ttv",
		"stream2" => "", 
		"stream2site" => "ttv",
		"wide" => true, );
	
	if (empty($_POST)){	$urlVars = $_GET; }
		else { $urlVars = $_POST; }
		
	foreach($urlVars as $key => $val) {
	// v = vertical, w = widescreen, f = fullscreen, s/ds = stream
	// Also have to look for s1, s1s, s2, s2s for backwards compatibility
		if  ($key == "v")  { $streams["vert"] = true; }
		if((($key == "w")  && ($val == "n"))||($key == "f")) { $streams["wide"] = false; }
		if((($key == "ds") || ($key == "s")) && (!empty($val))) {
			$spl = explode(';',$val);
			if(count($spl)==1){ 
				$streams["stream1"] = $val;
			} else { 
				$streams["stream1"] = $spl[0];
				$streams["stream2"] = $spl[1];
			}
		}
		if (($key == "s1")  && (!empty($val))) { $streams["stream1"] = $val; }
		if (($key == "s1s") && (!empty($val))) { $streams["stream1site"] = $val; }
		if (($key == "s2")  && (!empty($val))) { $streams["stream2"] = $val; }
		if (($key == "s2s") && (!empty($val))) { $streams["stream2site"] = $val; }
	}
	
	$pos = strpos($streams["stream1"],",");
	if($pos !== false) {
		$spl = preg_split('/\,/',$streams["stream1"]);
		$streams["stream1"] = $spl[0];
		$streams["stream1site"] = strtolower($spl[1]);
	}	

	$pos = strpos($streams["stream2"],",");
	if($pos !== false) {
		$spl = preg_split('/\,/',$streams["stream2"]);
		$streams["stream2"] = $spl[0];
		$streams["stream2site"] = strtolower($spl[1]);
	}

	$txt1 = $streams["stream1"];
	$txt2 = $streams["stream2"];
?>
<script type="text/javascript">
var needsUStream = 0;
var str1 = "";
var str2 = "";
var host = "http://" + window.location.host;

//Add some custom stream handlers to access in parseStreams
var sda = ["GamesDoneQuick","ttv"];
var agdq = sda;
var sgdq = sda;
var main = ["VGZTV","ttv"];
var alpha = ["Vidyagamez","sma"];
var vgzutv = ["TheOtherAplha:15211673","utv"];
var alt = vgzutv;

//Get the stream site's favicon for cosmetic pleasure.
function getFavicon(site) {
	return "<img class='favicon' width='14' height='14' src='" + host + "/style/" + site + ".ico' alt='(" + site + ")' title='" + site + "' />";
}

//Get the stream names from the form
function parseStreams(){
	var s1  = $('#s1').val();
	var s2  = $('#s2').val();
	var s1s = $('#s1s').val();
	var s2s = $('#s2s').val();
	
	var s1l = s1.toLowerCase();
	var s2l = s2.toLowerCase();
	
	//Handle custom streams
	if( s1l !== "" && window[s1l] !== undefined ) { s1 = window[s1l][0]; s1s = window[s1l][1]; }
	if( s2l !== "" && window[s2l] !== undefined ) { s2 = window[s2l][0]; s2s = window[s2l][1]; }
	
	//If UStream, do an ID check
	var checkNum = "a";
	if(s1s == "utv") { 
		if(isNaN(s1)){
			checkNum = s1.split(":");
			if(isNaN(checkNum[1])){
				s1 = getUStreamID(s1, 1);
				needsUStream = needsUStream + 1; 
			}
		}
	}
	
	checkNum = "a";
	if(s2s == "utv") {
		if(isNaN(s2)){
			checkNum = s2.split(":");
			if(isNaN(checkNum[1])){
				s2 = getUStreamID(s2, 2);
				needsUStream = needsUStream + 1; 
			}
		}
	}
	
	//Check for and parse out full Youtube URL for silly people that paste the full URL
	if(s1s == "you") {
		if(s1.search("v=") > -1){
			s1 = s1.substr(s1.search("v=") + 2);
			if(s1.search("&")){
				s1 = s1.substr(0,s1.search("&"));
			}
		}
	}
	
	if(s2s == "you") {
		if(s2.search("v=") > -1){
			s2 = s2.substr(s2.search("v=") + 2);
			if(s2.search("&")){
				s2 = s2.substr(0,s2.search("&"));
			}
		}
	}
	
	//Parse str1 and str2. They'll be wrong if UStream, but the UStream ID checking will set it itself.
	str1 = s1 + "," + s1s;
	str2 = s2 + "," + s2s;
		
	//If we're waiting for UStream IDs, don't let them change the text boxes.
	//If there's nothing to wait for, just go right ahead.
	if( needsUStream > 0) {  
		$('#s1').attr('readonly',true).toggleClass("disabledText");
		$('#s2').attr('readonly',true).toggleClass("disabledText");
	} else {
		makePopup();
	}
}

//Make the popup after all the stream names have been figured out
function makePopup() { 
    var width = 480;
    var wideWidth = 640;
    var height = 360;
    var left = (window.screenX != 'undefined' ? window.screenX : window.screenLeft) - 10;
    var top = (window.screenY != 'undefined' ? window.screenY : window.screenTop) + 200;
	var pathname = window.location.pathname;
	var host = "http://"+window.location.host;
	
	if (pathname.indexOf("/dev/") > -1 || pathname.indexOf("/development/") > -1) {
		host = host + "/development"; }
	
    var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
	
	var vert = $('#v').is(':checked');
	var wide = $('#w').is(':checked');
	
    if (str1.substring(0,1)==",") { str1 = str2; str2 = ",ttv"; }
	if (str1.substring(0,1)==",") { return; }
	
	if (wide) { width = wideWidth; }
	
	if (str2.substring(0,1)==",") {
		windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
		if( $('#redirect').is(':checked') ){
			window.location.href = host + "/ls.php?s="+str1;
		} else {
			window.open(host + "/ls.php?s="+str1, str1, windowFeatures);
		}
		var stream = str1;
	} else {
		var dual = str1 + ";" + str2;
		if (vert)	{ dual = dual + "&v=y"; height = height * 2; }
			else	{ width = width * 2; }
		windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
		if( $('#redirect').is(':checked') ){
			window.location.href = host + "/ls.php?ds="+dual;
		} else {
			window.open(host + "/ls.php?ds="+dual, str1+str2, windowFeatures);
		}
		var stream = dual;
	}
	var link  = host + "/p?s=" + stream;
	var link2 = host + "/ls.php?s=" + stream;
	if (!wide) { link = link + "&w=n"; link2 = link2 + "&w=n"; }
	$("#link").html("Link for automatic popup: <a href='" + link + "'>" + link + "</a><br />" 
					+ "Stream page itself: <a href='" + link2 + "'>" + link2 + "</a><br />");
}

//Sets UStream ID to the found number or -1 if none was found and make popup if all IDs are retrieved
//[ Callback from AJAX call in getUStreamID() ]
function setID(streamID, streamNum){ 
    console.log(streamID);
	if (streamID != null){
		a = streamID;
	} else {
		a = "-1";
	}
	
	if(streamNum == 1) { 
		str1 = a + ",utv"; 
		if(a!="-1"){ str1 = $('#s1').val() + ":" + str1;}
	}
	if(streamNum == 2) { 
		str2 = a + ",utv";
		if(a!="-1"){ str2 = $('#s2').val() + ":" + str2;}
	}
	needsUStream = needsUStream - 1;
	
	//If we're not waiting for any UStream IDs, make the popup and restore text boxes.
	if( needsUStream == 0 ){
		$('#s1').attr('readonly', false).toggleClass("disabledText", false);
		$('#s2').attr('readonly', false).toggleClass("disabledText", false);
		
		//Check if either stream ID is set to -1 (invalid) or -2 (timed out)
		if(str1=="-1,utv" && str2=="-1,utv"){
			alert("Both of the UStream names given were invalid. Try again.");
		} else if(str1=="-1,utv") {
			alert("Stream 1's UStream name was invalid. Try again.");
		} else if(str2=="-1,utv") {
			alert("Stream 2's UStream name was invalid. Try again.");
		} else if(str1=="-2,utv" && str2=="-2,utv"){
			alert("Timed out trying to get both of the UStream channel IDs.");
		} else if(str1=="-2,utv"){
			alert("Timed out trying to get stream 1's UStream channel ID.");
		} else if(str2=="-2,utv") {
			alert("Timed out trying to get stream 2's UStream channel ID.");
		} else {
			makePopup();
		}
	} 
}

//Makes an AJAX call using a UStream's stream URL and translates it into an appropriate ID.
function getUStreamID(stream, streamNum) {
	var intRegex = /^\d+$/;
	
	if(!intRegex.test(stream)) {
		$.ajax({
			url: 'http://einsynd.pw/ustreamID.php?stream=' + stream,
			dataType: "jsonp",
            jsonp: "callback",
			success: function(data){
                data = data["id"];
				setID(data, streamNum)
			},
			error: function(){
				if(streamNum == 1) { str1 = "-2,utv"; }
				if(streamNum == 2) { str2 = "-2,utv"; }
			
				needsUStream = needsUStream - 1;
			},
			type: "get",
			timeout: 10000
		});
	}
}

$(document).ready(function(){
	//Set dropdown change to trigger changing favicon next to it
	$("#s1s").change(function() {
		var selected = $("#s1s option:selected").val();
		$("#dropdownIcon1").html(getFavicon(selected));
	});
	$("#s1s").change(); //Auto-trigger it to set up the first one.
	
	//Set dropdown change to trigger changing favicon next to it
	$("#s2s").change(function() {
		var selected = $("#s2s option:selected").val();
		$("#dropdownIcon2").html(getFavicon(selected));
	});
	$("#s2s").change(); //Auto-trigger it to set up the first one.
});

</script>
<p id="down"></p>
<p>This page creates popups for several stream sites, either one or two per popup.<br />
Obviously won't do anything if popups are blocked.<br />
<div id="link"></div>
</p>
<form action="<?php echo $_SERVER['PHP_SELF'];?>">
Stream 1: &nbsp;&nbsp;<input type="text" name="s1" value="<?php echo $txt1; ?>" id="s1" />&nbsp;&nbsp;
<select name="s1s" id="s1s">
<option value="ttv" <?php if($streams["stream1site"]=="ttv"){ echo 'selected';} ?>>Twitch.TV</option>
<option value="lst" <?php if($streams["stream1site"]=="lst"){ echo 'selected';} ?>>Livestream</option>
<option value="utv" <?php if($streams["stream1site"]=="utv"){ echo 'selected';} ?>>UStream</option>
<option value="sma" <?php if($streams["stream1site"]=="sma"){ echo 'selected';} ?>>Smashcast.TV</option>
<!--<option value="cas" <?php if($streams["stream1site"]=="cas"){ echo 'selected';} ?>>ConnectCast</option>-->
<option value="mxr" <?php if($streams["stream1site"]=="mxr"){ echo 'selected';} ?>>Mixer</option>
<option value="you" <?php if($streams["stream1site"]=="you"){ echo 'selected';} ?>>Youtube</option>
<option value="ypl" <?php if($streams["stream1site"]=="ypl"){ echo 'selected';} ?>>Youtube (Playlist)</option>
</select><div id="dropdownIcon1"></div>
<br />

Stream 2: &nbsp;&nbsp;<input type="text" name="s2" value="<?php echo $txt2; ?>" id="s2" /></div>&nbsp;&nbsp;
<select name="s2s" id="s2s">
<option value="ttv" <?php if($streams["stream2site"]=="ttv"){ echo 'selected';} ?>>Twitch.TV</option>
<option value="lst" <?php if($streams["stream2site"]=="lst"){ echo 'selected';} ?>>Livestream</option>
<option value="utv" <?php if($streams["stream2site"]=="utv"){ echo 'selected';} ?>>UStream</option>
<option value="sma" <?php if($streams["stream2site"]=="sma"){ echo 'selected';} ?>>Smashcast.TV</option>
<!--<option value="cas" <?php if($streams["stream2site"]=="cas"){ echo 'selected';} ?>>ConnectCast</option>-->
<option value="mxr" <?php if($streams["stream2site"]=="mxr"){ echo 'selected';} ?>>Mixer</option>
<option value="you" <?php if($streams["stream2site"]=="you"){ echo 'selected';} ?>>Youtube</option>
<option value="ypl" <?php if($streams["stream2site"]=="ypl"){ echo 'selected';} ?>>Youtube (Playlist)</option>
</select><div id="dropdownIcon2"></div>
&nbsp;&nbsp;(leave blank for single stream)<br />

<br /><input type="checkbox" value="y" name="v[]" id="v"/>Vertical Dual Streams<br />
<input type="checkbox" value="y" name="w[]" <?php if($streams["wide"]){ echo 'checked';} ?> id="w"/>Widescreen<br />
<input type="checkbox" value="y" name="redirect" id="redirect"/>Redirect instead of open popup<br />
<input type="submit" value="Popup" name="submit" id="submit" onclick="parseStreams();" />
</p></form>
<script>
$("form").submit(function(event) {event.preventDefault();});
</script>

<?php if (!$streams["stream1"]=="") { echo "<script type=text/javascript>parseStreams();</script>"; } ?>
</body>
</html>