<html><head>
<?php
//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL | E_STRICT);

	$stream = $_GET['ds'];
	if(!($stream == "")) {
		$spl = preg_split('/;/',$stream);
		$stream1 = $spl[0];
		$stream2 = $spl[1];
	}

	if($stream1=="") {$stream1 = $_GET['s1'];}
	$pos = strpos($stream1,",");
	if($pos !== false) {
		$spl = preg_split('/\,/',$stream1);
		$stream1 = $spl[0];
		$stream1site = $spl[1];
		
	} else {
		$stream1site = $_GET['s1s'];
	}
	
	if($stream2=="") {$stream2 = $_GET['s2'];}
	$pos = strpos($stream2,",");
	if($pos !== false) {
		$spl = preg_split('/\,/',$stream2);
		$stream2 = $spl[0];
		$stream2site = $spl[1];
		
	} else {
		$stream1site = $_GET['s2s'];
	}
	
	$stream1name = ($stream1 == "beta") ? "VidyaGameZ" : $stream1;
	$stream2name = ($stream2 == "beta") ? "VidyaGameZ" : $stream2;
	
	$title = $stream1name.' and '.$stream2name.' dual viewing page';
	echo '<title>'.$title.'</title>';
?>
<style>
html { overflow: hidden; }

a {text-decoration:none;}
a:link, a:visited {color:#000;}
a:hover   {color:#060;}
a:active  {color:#000;}

body {
   background: #000;
   /* margin: 0 0 0 0; */
   margin: 0 0 0 0;
}

#chatdiv {
	position: absolute;
	height: 18px;
	width: 50px;
	text-align: center;
	font-size: 9pt;
	margin: 0 0 3px 0;
	background-color:#DDD;
	border: 1px solid #000;
	border-radius: 5px;
}

.divbot {
	bottom: 0px;
	left: 40%;
}

.divtop {
	bottom: 50%;
	left: 40%;
}

.divleft {
	bottom: 0px;
	left: 20%;
}

.divright {
	bottom: 0px;
	left: 70%;
}
<?php
	if (isset($_GET["v"])) {
		$v = $_GET["v"];	}
	
	if (isset($v)) {
		echo '#popup1 { width: 100%; height: 50%; } #popup2 { width: 100%; height: 50%; }'; 	}
	else {
		echo '#popup1 { width: 50%; height: 100%; } #popup2 { width: 50%; height: 100%; }';	}
?>
</style>
<script type="text/javascript">
function makeChat(stream,streamSite) {
    var width = 350;
    var height = 450;
    var left = 30;
    var top = 500;
    var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;

	windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
	window.open("http://ibill.ath.cx/chat.php?s="+stream+"&ss="+streamSite, stream + " chat window", windowFeatures);
}
</script>
</head>
<body>
<?php
//	$stream1 = $_GET['s1'];
//	$stream1site = $_GET['s1s'];
//	$stream2 = $_GET['s2'];
//	$stream2site = $_GET['s2s'];
	$jtv = '<object type="application/x-shockwave-flash" id="%id%" data="http://www.justin.tv/widgets/live_embed_player.swf?channel=%stream%" bgcolor="#000000"><param name="wmode" value="transparent"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="channel=%stream%&auto_play=true&start_volume=0" /></object>';
	$ls  = '<object type="application/x-shockwave-flash" id="%id%" data="http://cdn.livestream.com/grid/LSPlayer.swf?channel=%stream%"><param name="wmode" value="transparent"><param name="movie" value="http://cdn.livestream.com/grid/LSPlayer.swf?channel=%stream%" /><param name="flashVars" value="channel=%stream%&autoPlay=true&mute=true&color=#000000" /><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /></object>';
	$o3d = '<object type="application/x-shockwave-flash" id="%id%" data="http://www.own3d.tv/livestream/%stream%;autoplay=true"><param name="wmode" value="transparent"><param name="movie" value="http://www.own3d.tv/livestream/214803;autoplay=true" /><param name="allowscriptaccess" value="always" /><param name="allowfullscreen" value="true" /><param name="wmode" value="transparent" /></object>';
	$utv = '<object type="application/x-shockwave-flash" id="%id%" data="http://www.ustream.tv/flash/viewer.swf?id=%stream%"><param name="flashvars" value="autoplay=true&amp;brand=embed&amp;cid=%stream%&amp;v3=1"/><param name="wmode" value="transparent"><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="movie" value="http://www.ustream.tv/flash/viewer.swf"/></object>';
	
	if($stream1site=="") { $stream1site = "jtv"; }
	if($stream2site=="") { $stream2site = "jtv"; }
	
/*	if($stream1=="beta") {
		$embed = $o3d;	}
	else {
		$embed = str_replace("%stream%",$stream1,$stream1site==="ls"?$ls:$jtv);	}
	$embed = str_replace("%id%","popup1",$embed);
		
	if($stream2=="beta") {
		$embed = $embed . $o3d;	}
	else {
		$embed = $embed.str_replace("%stream%",$stream2,$stream2site==="ls"?$ls:$jtv);	}
	$embed = str_replace("%id%","popup2",$embed); 
*/
	if($stream1site=="ls")  { $str1Embed = $ls; }
	if($stream1site=="jtv") { $str1Embed = $jtv; }
	if($stream1site=="o3d") { $str1Embed = $o3d; }
	if($stream1site=="utv") { $str1Embed = $utv; }	
	$chat1 = $stream1 . "','" . $stream1site;
	
	$embed = str_replace("%stream%",$stream1, $str1Embed);
	$embed = str_replace("%id%","popup1",$embed);
	if ($stream1site=="jtv" || $stream1site=="utv") { 
		if (isset($v))	{	echo "<div id=\"chatdiv\" class=\"divtop\"><a href=\"#\" onclick=\"makeChat('".$chat1."');\">[CHAT]</a></div>"; }
		else			{	echo "<div id=\"chatdiv\" class=\"divleft\"><a href=\"#\" onclick=\"makeChat('".$chat1."');\">[CHAT]</a></div>"; }
	}
	
	if($stream2site=="ls")  { $str2Embed = $ls; }
	if($stream2site=="jtv") { $str2Embed = $jtv; }
	if($stream2site=="o3d") { $str2Embed = $o3d; }
	if($stream2site=="utv") { $str2Embed = $utv; }
	$chat2 = $stream2 . "','" . $stream2site;
	
	$embed = $embed . str_replace("%stream%",$stream2, $str2Embed);
	$embed = str_replace("%id%","popup2",$embed);
	if ($stream2site=="jtv" || $stream2site=="utv") { 
		if (isset($v))	{	echo "<div id=\"chatdiv\" class=\"divbot\"><a href=\"#\" onclick=\"makeChat('".$chat2."');\">[CHAT]</a></div>"; }
		else			{	echo "<div id=\"chatdiv\" class=\"divright\"><a href=\"#\" onclick=\"makeChat('".$chat2."');\">[CHAT]</a></div>"; }
	}
	
	#$embed  = $embed.str_replace("%stream%",$stream2,'<object id="popupStream" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param name="movie" value="http://cdn.livestream.com/grid/LSPlayer.swf"></param><param name="flashVars" value="channel=%stream%&amp;autoPlay=false&amp;mute=true&amp;color=#000000"></param><param name="allowScriptAccess" value="always"></param><param name="allowFullScreen" value="true"></param><embed id="popupStream" src="http://cdn.livestream.com/grid/LSPlayer.swf" flashVars="channel=%stream%&amp;autoPlay=true&amp;mute=true&amp;color=#000000" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash"></embed></object>');
	
	echo $embed;
?>

</body>