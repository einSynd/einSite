<html><head>
<?php
//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL | E_STRICT);

	$stream1 = ""; $site1 = "";
	$stream2 = ""; $site2 = "";
	
	$urlVars = $_GET;
	
	foreach($urlVars as $key => $val) {
		if ($key == "s" || $key == "ds") {
			$spl = preg_split('/;/',$val);
			$stream1 = $spl[0];
			$stream2 = $spl[1];
		}
		
		if ($key == "s1") { $stream1 = $val; }
		if ($key == "s1s") { $site1 = $val; }
		if ($key == "s2") { $stream2 = $val; }
		if ($key == "s2s") { $site2 = $val; }
	}

	if(strpos($stream1,",") !== false) {
		$spl = preg_split('/,/',$stream1);
		$stream1 = $spl[0];
		$site1 = $spl[1];
	}
	
	if(strpos($stream2,",") !== false) {
		$spl = preg_split('/,/',$stream2);
		$stream2 = $spl[0];
		$site2 = $spl[1];
	}
	
	$stream1name = $stream1;
	$stream2name = $stream2;
	if(strpos($stream1,":") !== false) {
		$spl = preg_split('/:/',$stream1);
		$stream1name = $spl[0] . " (" . $spl[1] . ")";
		$stream1 = $spl[1];
	}
	
	if(strpos($stream2,":") !== false) {
		$spl = preg_split('/:/',$stream2);
		$stream2name = $spl[0] . " (" . $spl[1] . ")";
		$stream2 = $spl[1];
	}
	
	$title = $stream1name.' and '.$stream2name.' dual viewing page';
	$uri = $_SERVER['REQUEST_URI'];
	if(strstr($uri,"/prod/") || strstr($uri, "/production/")) {
		$title = "/!\\ " . $title . " - TESTING /!\\";
	}
	echo '<title>' . $title . '</title>';
?>
<link rel="stylesheet" type="text/css" href="css/streamPage.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script><script src="swfobject.js"></script>
<style>
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

function makeStream() {
	var flashVars = "";
<?php
	if (isset($_GET["v"])) {
		$v = $_GET["v"];	}
	
	if (isset($v)) {
		echo "var vert = true;";
	} else {
		echo "var vert = false;";
	}
?>	
	var width="10%";
	var height="10%";
	if(vert) {width="100%";height="50%"; }
	else	 {width="50%"; height="100%";}
	
	var stream1 = "<?php echo $stream1; ?>";
	var stream2 = "<?php echo $stream2; ?>";
	var site1="<?php echo $site1; ?>";
	var site2="<?php echo $site2; ?>";
	
	var JTVFlashVars = {
		channel: stream1,
		auto_play: "true",
		start_volume: "0"
	};

	var LSFlashVars = {
		channel: stream1,
		autoPlay: "true",
		mute: "true",
		color: "#000000"
	};
	
	var O3DFlashVars = false;
	
	var UTVFlashVars = {
		autoplay: "true",
		brand: "embed",
		cid: stream1,
		v3: "1"
	};
	
	var params = {
		wmode: "transparent", 
		allowFullScreen: "true", 
		allowScriptAccess: "always", 
		allowNetworking: "all", 
		movie: "http://www.justin.tv/widgets/live_embed_player.swf",
	};
	
	var jtv  = "http://www.justin.tv/widgets/live_embed_player.swf"; var jtv2 = "?channel=";
	var ls   = "http://cdn.livestream.com/grid/LSPlayer.swf"; var ls2  = "?channel=";
	var o3d  = "http://www.own3d.tv/livestream/"; var o3d2 = ";autoplay=true";
	var utv  = "http://www.ustream.tv/flash/viewer.swf"; var utv2 = "?id=";
	
	var embedSWF="";
	if(site1=="ls") { embedSWF = ls + ls2 + stream1; params["movie"] = ls; flashVars = LSFlashVars; }
	else if(site1=="o3d") { embedSWF = o3d + stream1 + o3d2; params["movie"] = o3d; flashVars = O3DFlashVars; }
	else if(site1=="utv") { embedSWF = utv + utv2 + stream1; params["movie"] = utv; flashVars = UTVFlashVars; }
	else if(site1=="jtv") { embedSWF = jtv + jtv2 + stream1; params["movie"] = jtv; flashVars = JTVFlashVars; }	
	
	swfobject.embedSWF(embedSWF, "stream1", width, height, "9.0.0", false, flashVars, params);
	
	embedSWF="";
	if(site2=="ls") { embedSWF = ls + ls2 + stream2; params["movie"] = ls; flashVars = LSFlashVars; }
	else if(site2=="o3d") { embedSWF = o3d + stream2 + o3d2; params["movie"] = o3d; flashVars = O3DFlashVars; }
	else if(site2=="utv") { embedSWF = utv + utv2 + stream2; params["movie"] = utv; flashVars = UTVFlashVars; }
	else if(site2=="jtv") { embedSWF = jtv + jtv2 + stream2; params["movie"] = jtv; flashVars = JTVFlashVars; }
	
	if(site2=="ls" || site2=="jtv") { flashVars["channel"] = stream2; }
	else if(site2=="utv") { flashVars["cid"] = stream2; }
	
	swfobject.embedSWF(embedSWF, "stream2", width, height, "9.0.0", false, flashVars, params);
}
</script>
</script>
</head>
<body onload="makeStream()">
<div id="stream1"></div><div id="stream2"></div>
<?php
	$chat1 = $stream1 . "','" . $site1;
	if ($site1=="jtv" || $site1=="utv") { 
		if (isset($v))	{	echo "<div id=\"chatdiv\" class=\"divtop\"><a href=\"#\" onclick=\"makeChat('".$chat1."');\">[CHAT]</a></div>"; }
		else			{	echo "<div id=\"chatdiv\" class=\"divleft\"><a href=\"#\" onclick=\"makeChat('".$chat1."');\">[CHAT]</a></div>"; }
	}

	$chat2 = $stream2 . "','" . $site2;
	if ($site2=="jtv" || $site2=="utv") { 
		if (isset($v))	{	echo "<div id=\"chatdiv\" class=\"divbot\"><a href=\"#\" onclick=\"makeChat('".$chat2."');\">[CHAT]</a></div>"; }
		else			{	echo "<div id=\"chatdiv\" class=\"divright\"><a href=\"#\" onclick=\"makeChat('".$chat2."');\">[CHAT]</a></div>"; }
	}
?>

</body>