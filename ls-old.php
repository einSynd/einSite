<html><head>
<?php
//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL | E_STRICT);
	
	$stream = $_GET['s'];
	$pos = strpos($stream,",");
	if($pos !== false) {
		$spl = preg_split('/\,/',$stream);
		$stream = $spl[0];
		$site = $spl[1];
		
	} else {
		$site = $_GET['ss'];
	}
	
	$title = $stream.' viewing page';
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

#popupStream {
   width: 100%;
   height: 100%;
}

#chatdiv {
	position: absolute;
	height: 18px;
	bottom: 0px;
	left: 40%;
	width: 50px;
	text-align: center;
	font-size: 9pt;
	margin: 0 0 3px 0;
	background-color: #DDD;
	border: 1px solid #000;
	border-radius: 5px;
}


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

	$jtv = '<object type="application/x-shockwave-flash" id="popupStream" data="http://www.justin.tv/widgets/live_embed_player.swf?channel=%stream%"><param name="wmode" value="transparent"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="channel=%stream%&auto_play=true&start_volume=100&color=#000000" /></object>';
	$ls  = '<object type="application/x-shockwave-flash" id="popupStream" data="http://cdn.livestream.com/grid/LSPlayer.swf?channel=%stream%"><param name="wmode" value="transparent"><param name="movie" value="http://cdn.livestream.com/grid/LSPlayer.swf?channel=%stream%" /><param name="flashVars" value="channel=%stream%&amp;autoPlay=true&amp;mute=false&amp;color=#000000" /><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /></object>';
	$o3d = '<object type="application/x-shockwave-flash" id="popupStream" data="http://www.own3d.tv/livestream/%stream%;autoplay=true"><param name="wmode" value="transparent"><param name="movie" value="http://www.own3d.tv/livestream/214803;autoplay=true" /><param name="allowscriptaccess" value="always" /><param name="allowfullscreen" value="true" /><param name="wmode" value="transparent" /></object>';
	$utv = '<object type="application/x-shockwave-flash" id="popupStream" data="http://www.ustream.tv/flash/viewer.swf?id=%stream%"><param name="flashvars" value="autoplay=true&amp;brand=embed&amp;cid=%stream%&amp;v3=1"/><param name="wmode" value="transparent"><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="movie" value="http://www.ustream.tv/flash/viewer.swf"/></object>';
	
	if($site=="") { $site="jtv"; }
	if($site=="ls")  { $strEmbed = $ls; }
	if($site=="jtv") { $strEmbed = $jtv; }
	if($site=="o3d") { $strEmbed = $o3d; }
	if($site=="utv") { $strEmbed = $utv; }	
	$embed = str_replace("%stream%",$stream, $strEmbed);
	$chat = $stream . "','" . $site;
	
	if ($site=="jtv" || $site=="utv") {
		echo "<div id=\"chatdiv\"><a href=\"#\" onclick=\"makeChat('".$chat."');\">[CHAT]</a></div>"; }
	
/*	if($stream=="beta") {
		$embed = $o3d;	}
	elseif($site=="ls")	{
		$embed = str_replace("%stream%",$stream,$ls);	}
	else {
		$embed = str_replace("%stream%",$stream,$jtv);	}  */

	echo $embed;
?>

</body>