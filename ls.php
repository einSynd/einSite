<html><head>
<meta charset="UTF-8">
<?php
//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL ^ E_STRICT);
	
	$stream1 = ""; $site1 = "";
	$stream2 = ""; $site2 = "";
	
	$urlVars = $_GET;
	
	foreach($urlVars as $key => $val) {
		if ($key == "s" || $key == "ds") {
			if (strpos($val,";") !== false) {
				$spl = preg_split('/;/',$val);
				$stream1 = $spl[0];
				$stream2 = $spl[1];
			} else {
				$stream1 = $val;
				$stream2 = "none";
			}
		}
		if ($key == "s1")	{ $stream1 = $val;	}
		if ($key == "s1s")	{ $site1 = $val;	}
		if ($key == "s2")	{ $stream2 = $val;	}
		if ($key == "s2s")	{ $site2 = $val;	}
		if ($key == "v")	{ $vert = $val;		}
	}

	if(strpos($stream1,",") !== false) {
		$spl = preg_split('/,/',$stream1);
		$stream1 = $spl[0];
		$site1 = $spl[1];
	}
	$stream1name = $stream1;
	
	if($stream2 != "none"){
		if(strpos($stream2,",") !== false) {
			$spl = preg_split('/,/',$stream2);
			$stream2 = $spl[0];
			$site2 = $spl[1];
		}
		$stream2name = $stream2;
	}
	
	//If site received is old Livestream ("ls"), change to new Livestream ("lst")
	if($site1 == "ls"){ $site1 = "lst"; }
	if($site2 == "ls"){ $site2 = "lst"; }
	
/*	This sets the title, which is now done in the stream change function
	if($stream2 != "none") {
		$title = $stream1name.' and '.$stream2name.' dual viewing page';
	} else {
		$title = $stream1name . ' viewing page';
	}
	
	$uri = $_SERVER['REQUEST_URI'];
	if(strstr($uri,"/dev/") || strstr($uri, "/development/")) {
		$title = "/!\\ " . $title . " - TESTING /!\\";
	}
	echo '<title>' . $title . '</title>';
*/
?>
<title>Stream Popups</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<link rel="stylesheet" type="text/css" href="css/streamPage.css" />
<link rel="dialog stylesheet" type="text/css" href="css/jquery-ui-1.10.2.custom.css" />
<style>
#leftDiv, #centerDiv, #rightDiv {
	position: absolute;
	display: inline-block;
	height: 20px;
	bottom: 10px;
	text-align: center;
	font-size: 9pt;
	margin: 0 0 3px 0;
	padding: 0;
	background-color: #DDD;
	border: 1px solid #000;
	border-radius: 5px;
	width: auto;
}

#centerDiv {
	width: 65px;
}

#leftDiv, #rightDiv {
	width: 90px;
	height: 18px;
	margin: 0 0 4px 0;
	white-space: nowrap;
}

#expand1, #expand2, #contract1, #contract2 { font-size: 14px; }
.decor:hover {color:#070;}
.decor {display:inline;}
.outline {border: 1px solid #000;}
.disabled { display: none; }

#floating {
	width: 260px; height: 212px; position: absolute; z-index: 1;
	background-color: #FFF; left: 0px; top: 0px; border: 1px solid #FFF;
	display: none; text-align: center; font-size: 12px;
}

#close {
	position:absolute;
	top:0px;
	right:0px;
}

.ui-widget-overlay {
	background-color: white;
	background-image: none;
	opacity: 0.2;
	filter: "Alpha(Opacity=20)";
}

#stream1, #stream2 {
	display: inline-block;
}

#wrap {
	white-space: nowrap;
}

#block {
	display: none;
	position: absolute;
	z-index: 1;
	top: 0px;
	left: 0px;
}
</style>
<script type="text/javascript">

var site1="<?php echo $site1; ?>";
var stream1="<?php echo $stream1; ?>";
var site2="<?php echo $site2; ?>";
var stream2="<?php echo $stream2; ?>";
var vert = "<?php if(isset($vert)){ echo "vert"; } else { echo "horiz"; } ?>";
var host = "http://" + window.location.host;


function setBlockingDiv(streamDiv) {
    return
	//Currently for single stream and horizontal dual stream
	
	if( (stream1 == "none") || (stream2 == "none") ) {
		if( (site1 == "ttv") || (site2 == "ttv") ){
			if( $(window).height() > 110 ){
				$("#block").width( $(window).width() );
				$("#block").height( $(window).height()  - 120);
				$("#block").show();
			}
		}
	} else if (vert == "horiz") {
		if( (site1 == "ttv") && (site2 == "ttv") ){
			if( $(window).height() > 110 ){
				$("#block").width( $(window).width() );
				$("#block").height( $(window).height()  - 120);
				$("#block").show();
			}
		} else {
			$("#block").hide();
		}
	} else {
		$("#block").hide();
	}
}

function getViewers(stream) {
	//HBX viewer request removed, they added their own.
	//	Leaving this in case another site needs to be added later.
	return getViewersTTV(stream);
}

function getViewersTTV(stream) {
	//Do a JSONP request for the number of viewers
	return $.ajax({
		url: 'https://api.twitch.tv/kraken/streams/' + stream + '/',
		timeout: 15000,
        headers: {
            "Client-ID": "glrjuvya5xmg8batkujqtfij53q073h"
        },
		success: function(data){
			if(data.stream != null){
				data = data.stream;
				document.title = document.title.split("page")[0] + "page (" + data.viewers + " viewers)";
				if(window.location.pathname.search("dev") == 1){
					//Skip the first warning sign since it should be saved after being split
					document.title = document.title + " - TESTING /!\\";
				}
			} else {
				//If the array is empty, who knows what it's doing, just set a basic title.
				document.title = document.title.split("page")[0] + "page";
				if(window.location.pathname.search("dev") == 1) {
					//Skip the first warning sign since it should be saved after being split
					document.title = document.title + " - TESTING /!\\";
				}
			}
		},
		error: function(a,b,c) {
			//Some sort of error occurred, might as well take care of it to prevent any unforseen consequences.
			if( typeof b === "string" ) {
				document.title = document.title.split("page")[0] + "page";
				if(window.location.pathname.search("dev") == 1) {
					//Skip the first warning sign since it should be saved after being split
					document.title = document.title + " - TESTING /!\\";
				}
			}
		}
	});
}

function makeChat(stream,streamSite) {
	stream = stream.toLowerCase();
	var width = 350;
    var height = 450;
    var left = (window.screenX != 'undefined' ? window.screenX : window.screenLeft) - 10;
    var top = (window.screenY != 'undefined' ? window.screenY : window.screenTop) + 200;
    var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;

	windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
	if(streamSite=="ttv" || streamSite=="th5") {
		window.open("http://www.twitch.tv/"+stream+"/chat?popout=&secret=safe", stream + " chat window", windowFeatures);
	} else if(streamSite=="hbx") {
		window.open("http://www.hitbox.tv/embedchat/"+stream+"?autoconnect=true", stream + " chat window", windowFeatures);
    } else if(streamSite=="bea") {
    	window.open("https://beam.pro/embed/chat/"+stream, stream + " chat window", windowFeatures);
	} else {
		window.open(host + "/chat.php?s="+stream+"&ss="+streamSite, stream + " chat window", windowFeatures);
	}
}

function makeStream(streamToMake, makeSite, whatDiv, size, streamName) {
	var useSWFObj = true;
	var flashVars = "";
	
	var TTVFlashVars = {
		channel: streamToMake,
		hostname: "www.twitch.tv",
		auto_play: "true",
		start_volume: "50"
	};

	var LSFlashVars = {
		channel: streamToMake,
		autoPlay: "true"
	};
	
	var UTVFlashVars = {
		autoplay: "true",
		brand: "embed",
		cid: streamToMake
	};
	
	var HTVFlashVars = {
		autostart: "true",
		channel: streamToMake.toLowerCase()
	};
	
	var params = {
		wmode: "transparent", 
		allowFullScreen: "true", 
		allowScriptAccess: "always", 
		allowNetworking: "all", 
		movie: "http://www.twitch.tv/widgets/live_embed_player.swf"
	};
		
	var ttv  = "http://www.twitch.tv/widgets/live_embed_player.swf";
	var ttv2 = "?channel=";
	var lst  = "http://cdn.livestream.com/grid/LSPlayer.swf";
	var lst2 = "?channel=";
	var utv  = "http://www.ustream.tv/flash/viewer.swf";
	var utv2 = "?autoplay=true&volume=1&cid=";

	
	var width = "100%";
	var height = "100%";
	if (size == "horiz"){
		width = "50%";
	} else if (size == "vert") {
		height = "50%"
	}

	var embedSWF="";
	if(makeSite=="lst") { embedSWF = lst + lst2 + streamToMake; params["movie"] = lst; flashVars = LSFlashVars; }
	else if(makeSite=="utv") { embedSWF = utv + utv2 + streamToMake; params["movie"] = utv; flashVars = UTVFlashVars; }
//	else if(makeSite=="ttv") { embedSWF = ttv + ttv2 + streamToMake; params["movie"] = ttv; flashVars = TTVFlashVars; }	
	else if(makeSite=="htv") { embedSWF = htv + htv2 + streamToMake; params["movie"] = htv; flashVars = HTVFlashVars; }
	
	//This grew from Veemi, it has to be in an iframe or it won't work, which breaks everything else.
	//Luckily, this has made it easy to embed other sites that use iframes. Currently, Hitbox and Youtube.
	else {
		//Make sure the stream is gone, then prepare a fresh div
		if($("#stream"+whatDiv).is("object")) {
			swfobject.removeSWF("stream"+whatDiv);
			$("#wrap").prepend('<div id="stream'+whatDiv+'"></div>');
			$("#stream"+whatDiv).height(height);
			$("#stream"+whatDiv).width(width);
		} else {		
			$("#stream"+whatDiv).empty();
		}
		
		//Add the appropriate IFrame to the div
		if(makeSite=="vee") {
			$("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency=true src=http://www.veemi.com/embed.php?v="+streamToMake+"&vw=100%&vh=100%&domain=einsynd.pw></iframe>");
		} else if(makeSite=="hbx") {
			$("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen src=//www.hitbox.tv/#!/embed/"+streamToMake+"?autoplay=true></iframe>");
		} else if(makeSite=="you") {
			$("#stream"+whatDiv).append("<iframe id='youframe' width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen autoplay src=//www.youtube.com/embed/"+streamToMake+"?autoplay=1></iframe>");
		} else if(makeSite=="ypl") {
			$("#stream"+whatDiv).append("<iframe id='youframe' width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen autoplay src=https://www.youtube.com/embed/videoseries?list="+streamToMake+"></iframe>");
		} else if(makeSite=="ttv") {
            $("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen auto_play src=//player.twitch.tv/?volume=1&channel="+streamToMake+"></iframe>");
        } else if(makeSite=="uh5") {
            $("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen auto_play webkitallowfullscreen src=http://www.ustream.tv/channel/"+streamName.toLowerCase()+"/pop-out></iframe>");
        } else if(makeSite=="cas") {
            $("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen auto_play webkitallowfullscreen src=http://connectcast.tv/popout/live/"+streamName+"></iframe>");
        } else if(makeSite=="bea") {
            $("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen auto_play webkitallowfullscreen src='https://beam.pro/embed/player/"+streamName+"'></iframe>");
        } else if(makeSite=="rec") {
            $("#stream"+whatDiv).append("<iframe width=100% height=100% scrolling=no frameborder=0 allowtransparency allowfullscreen auto_play src=//player.twitch.tv/?video="+streamToMake+"></iframe>");
        }
		
		//Make it visible, tell it not to use SWFObject below
		$("#stream"+whatDiv).css("visibility","visible");
		useSWFObj = false;
	}
	
	if(useSWFObj){
		swfobject.embedSWF(embedSWF, "stream"+whatDiv, width, height, "9.0.0", false, flashVars, params);
	}
	$("#change"+whatDiv).text("[Change Stream]");
	
	// Add chat link, for Twitch, UStream, Hitbox, and Beam.
	if (makeSite == "ttv" || makeSite == "utv" || makeSite == "hbx" || makeSite == "bea") {
		$("#achat"+whatDiv).off();
		$("#achat"+whatDiv).click({stream: streamToMake, site: makeSite}, function(e) {
			stream = e.data.stream;
			site = e.data.site;
			
			makeChat(stream, site);
		});
		$("#achat"+whatDiv).show();
	} else {
		$("#achat"+whatDiv).hide();
	}
	
	if(whatDiv == "1") {
		if (makeSite == "ttv" || makeSite == "utv" || makeSite == "hbx" || makeSite == "bea") {
			$("#leftDiv").css("width", "115px");
		} else {
			$("#leftDiv").css("width", "90px");
		}
		site1 = makeSite;
		stream1 = streamToMake;
		
	} else if(whatDiv == "2") {
		if (makeSite == "ttv" || makeSite == "utv" || makeSite == "hbx" || makeSite == "bea") {
			$("#rightDiv").css("width", "115px");
		} else {
			$("#rightDiv").css("width", "90px");
		}
		site2 = makeSite;
		stream2 = streamToMake;
	}
	
	setBlockingDiv("stream"+whatDiv);
}

function resizeDual() {
	if (vert == "vert") {
		$("#stream1").css("display","block").height("50%").width("100%");
		$("#stream2").css("display","block").height("50%").width("100%");
	} else {
		$("#stream1").css("display","inline-block").height("100%").width("50%");
		$("#stream2").css("display","inline-block").height("100%").width("50%");
	}
	setBlockingDiv("stream1");
}

function toggleVert() {
	if (stream2 == "none" || stream1 == "none") {
		return
	}
	
	if (vert == "vert") {
		vert = "horiz";
		$("#vert").text("[H]");
	} else {
		vert = "vert";
		$("#vert").text("[V]");
	}
	resizeDual();
}

$(window).resize(function(){
	//Get exact center of page, size of center div, find sides of center div
	var center = Math.round( $(window).width() / 2 );
	var centerWidth = $("#centerDiv").width();
	var leftCenter = center - (centerWidth / 2);
	var rightCenter = center + (centerWidth / 2);
	
	$("#centerDiv").css("left", leftCenter);
	$("#leftDiv").css("left", leftCenter - $("#leftDiv").width());
	$("#rightDiv").css("left", rightCenter);
	
	setBlockingDiv("stream1");
});

function changeStream(newStream, newSite, which){
	var otherStream, otherSite, other, side, otherName = "";
	var newName = "";
	
	if( newStream.indexOf(":") > -1 ){
		newName = newStream.split(":")[0];
		newStream = newStream.split(":")[1];
	} else {
		newName = newStream;
	}

	if(which=="1"){
		otherStream = stream2;
		otherSite = site2;
		other = "2";
		side = "#left";
	} else {
		otherStream = stream1;
		otherSite = site1;
		side = "#right";
		other = "1";
	}
	
	if( otherStream.indexOf(":") > -1 ){
		otherName = otherStream.split(":")[0];
		otherStream = otherStream.split(":")[1];
	} else {
		otherName = otherStream;
	}
	
	if (newStream == "none") {
		//If the new stream's name is "none," remove it.
		swfobject.removeSWF("stream"+which);
		if ($("#stream"+which).length) { $("#stream"+which).remove(); }
		$("#wrap").prepend('<div id="stream'+which+'"></div>');
		$("#achat"+which).hide();
		$(".hiddenMid").hide();
		$("#vert").hide()
		$(side+"Div").css("width", "70px");
		$("#change"+which).text("[Add Stream]");
		
		//If it was a dual stream, make the other one the only stream.
		if(otherStream != "none") {
			$("#stream"+other).height("100%");
			$("#stream"+other).width("100%");
			//If the other is on a site that supports chat, allow the middle [CHAT] button
			var chatSites = [ "ttv", "utv", "hbx", "bea" ];
			if ( $.inArray(otherSite, chatSites) > -1 ){
				$("#centerDiv").width("95px");
				$("#achatMid"+other).show();
			} else {
				$("#centerDiv").width("65px");
				$("#achatMid"+other).hide();
			}
			$("#vert").hide()
			
			document.title = otherName + " viewing page";
		}
	} else {
		//Looks like we're adding or changing a stream.
		if(otherStream != "none") {
			//There's already another stream, time to make it a dual stream.
			resizeDual();
			makeStream(newStream, newSite, which, vert, newName);
			
			$(".hiddenMid").hide();
			$("#vert").show();
			$("#centerDiv").width("65px");
			document.title = newName + " and " + otherName + " dual viewing page";
		} else {
			//New stream is the only stream, make sure it's maximized.
			$(".hiddenMid").show();
			$("#vert").hide();
			$("#stream"+which).height("100%");
			$("#stream"+which).width("100%");
			//Add the middle chat button if it's a site that supports chat
			if (newSite=="ttv" || newSite=="utv" || newSite=="hbx" || newSite == "bea"){
				$("#centerDiv").width("95px");
				$("#achatMid"+which).show();
			} else {
				$("#centerDiv").width("65px");
				$("#achatMid"+which).hide();
			}
			$("#achatMid"+other).hide();
			makeStream(newStream, newSite, which, "single", newName);
			if( newSite == "rec" ) { 
				document.title = newName + " recording #" + newStream + " viewing page";
			} else { 
				document.title = newName + " viewing page";
			}

		}
	}

	//Set warning signs around the title if it's the dev version so I know at a glance.
	if(window.location.pathname.search("dev") == 1){
		document.title = "/!\\ " + document.title + " - TESTING /!\\";
	}

	$(window).resize();
	if(which=="1"){
		stream1 = newStream;
	} else {
		stream2 = newStream;
	}
}

$(document).ready(function(){
	
	//Apparently needed to properly position JQueryUI dialogs
	$.extend($.ui.dialog.prototype.options.position, { collision: 'none' });
	
	//Make the change stream dialog
	var changeDialog = $('<div></div>')
		.html('Enter new stream or "none": <br /> (Currently ' +
			'<span id="curStream"></span><span id="extra"> on <span id="curSite"></span></span>)' +
			'<form id="change"><input type="hidden" id="which" />' +
			'<input type="text" id="changeTo" /><select id="siteTo">' +
			'<option value="ttv">Twitch.TV</option><option value="lst">Livestream</option>' +
			'<option value="utv">UStream</option><option value="hbx">Hitbox.TV</option>' +
			'<option value="vee">Veemi</option><option value="you">Youtube</option></select>' +
			'<div align="center"><input type="submit" value="Submit"/></div>' +
			'</form>')
		.css('overflow', 'hidden') 
		.dialog({
			autoOpen: false,
			resizable: false,
			width: "275px",
			height: 140,
			modal: true,
			title: 'Add or Change Stream',
			position: {my: "center center", at: "center center", of: document},
		});

	$("#change").submit(function(e){
		var changeTo = $("#changeTo").val();
		var siteTo = $("#siteTo").val();
		var which = $("#which").val();
		
		changeStream(changeTo, siteTo, which)
		changeDialog.dialog("close");
		
		e.preventDefault();
		return false;
	});
	
	//Hide all the less important buttons, they'll be toggled on if they're available.
	$(".hiddenMid").hide();
	$(".hidden1").hide();
	$(".hidden2").hide();
	$("#achat1").hide();
	$("#achat2").hide();
	
	//If dual streams set to vertical, make this a [V] since default is [H]
	if(vert == "vert"){
		$("#vert").text("[V]");
	}
	
	//Spawn the streams
	if(stream2 != "none"){
		changeStream(stream1, site1, "1");
		changeStream(stream2, site2, "2");
		$("#vert").show();
	} else {
		changeStream(stream1, site1, "1");
		$("#centerDiv").width("90px");
	
		//It's a solo stream, if Twitch, check the viewers immediately.
		if(site1 == "ttv") {
			getViewers(stream1);
		}
	}
	
	//Fire the resize function to make sure the centered div is centered.
	$(window).resize();
	
	$("#expand1").click(function () {
		$("#expand1").hide();
		$(".hidden1").show();
	});
	
	$("#contract1").click(function () {
		$("#expand1").show();
		$(".hidden1").hide();
	});
	
	$("#expand2").click(function () {
		$("#expand2").hide();
		$(".hidden2").show();
	});
	
	$("#contract2").click(function () {
		$("#expand2").show();
		$(".hidden2").hide();
	});
	
	$("#vert").click(function() {
		toggleVert();
	});
	
	$("#change1").click(function () {
		$("#which").val("1");
		$("#curStream").html('"'+stream1+'"');
		if(stream1 == "none") {
			$("#curStream").html("not enabled");
			$("#extra").hide();
		} else {
			$("#curSite").html(site1.toUpperCase());
			$("#extra").show();
		}
		changeDialog.dialog("open");
	});
	
	$("#change2").click(function () {
		$("#which").val("2");
		$("#curStream").html(stream2);
		if(stream2 == "none") {
			$("#curStream").html("no stream");
			$("#extra").hide();
		} else {
			$("#curSite").html(site2);
			$("#extra").show();
		}
		
		changeDialog.dialog("open");
	});
	
	$("#close").click(function () {
		swfobject.removeSWF("popup");
		$("#floating").hide();
	});
	
	//Add animation for mousing over centered div so the buttons are easily accessible.
	$("#navdiv").mouseover(function() {
		$("#leftDiv").animate({ bottom: "-2px",}, 300 );
		$("#centerDiv").animate({ bottom: "-2px",}, 300 );
		$("#rightDiv").animate({ bottom: "-2px",}, 300 );
	});
	
	//Add animation for center div to hide a bit when you're done using it.
	$("#navdiv").mouseleave(function() {
		$("#leftDiv").animate({ bottom: "-15px",}, 300 );
		$("#centerDiv").animate({ bottom: "-15px",}, 300 );
		$("#rightDiv").animate({ bottom: "-15px",}, 300 );
	});
	
	$("#achatMid1").click(function() {
		$("#achat1").click();
	});
	
	$("#achatMid2").click(function() {
		$("#achat2").click();
	});
	
	//Make the center dive hide itself a bit so it's less intrusive.
	$("#navdiv").animate({ bottom: "-15px",}, 500 );
	$("#centerDiv").animate({ bottom: "-15px",}, 500 );
	
	setInterval(function(){	//Set a timer to do viewer count checks ...
		if(stream1 != "none" && stream2 != "none") {
			return
		}
		
		if(stream1 != "none"){
			if(site1 == "ttv"){
				return getViewers(stream1);
			}
		} else {
			if(site2 == "ttv"){
				return getViewers(stream2);
			}		
		}
	}, 2 * 60 * 1000); //... every 2 minutes
});
</script>
</head>
<body>
<div id="wrap"><div id="block"></div><div id="stream1"></div><div id="stream2"></div></div>

<div id="navdiv"><div id="leftDiv" class="hidden1">
<div id="change1" class="decor">[Change Stream]</div><div id="achat1" class="decor">[Chat]</div>
</div>
<div id="centerDiv"><div id="expand1" class="decor">&lt;1]</div>
<div id="contract1" class="hidden1 decor">[1&gt;</div>
<div id="pip" class="decor disabled">[PIP]</div><div id="vert" class="decor hiddenMid">[H]</div><div id="achatMid1" class="decor hiddenMid">[CHAT]</div><div id="achatMid2" class="decor hiddenMid">[CHAT]</div>
<div id="expand2" class="decor">[2&gt;</div>
<div id="contract2" class="hidden2 decor">&lt;2]</div>
</div>
<div id="rightDiv" class="hidden2">
<div id="achat2" class="decor">[Chat]</div><div id="change2" class="decor">[Add Stream]</div>
</div></div>
</body>