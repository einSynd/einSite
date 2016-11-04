<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_NOTICE);

	if( !isset($_GET["id"]) || $_GET["id"] == ""){
		die("No ID given.");
	}
	$baseUrl = "http://www.hitbox.tv/api/player/hlsvod/";
	$plUrl = @file_get_contents($baseUrl . $_GET["id"]);
	$plUrl = explode("http",$plUrl);
	$plUrl = "http" . $plUrl[1];
	$plUrl = substr($plUrl, 0, -1);
	echo $plUrl . "<br />";
	$vodBase = explode("index.m3u8", $plUrl);
	$vodBase = $vodBase[0];
	$recParts = "";
	$plFile = file_get_contents($plUrl);
	$plFile = explode("\n", $plFile);
	foreach( $plFile as $a => $line ){
		if( substr($line, 0, 1) != "#" && !empty($line)){
			$recParts[] = $vodBase . $line;
		}
	}
	foreach( $recParts as $key => $val) {
		echo $val . "<br />";
	}
?>