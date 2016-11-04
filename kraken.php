<?php 
	$streamName = "vgztv";
	
	$recordings = json_decode(@file_get_contents('http://api.justin.tv/api/channel/archives/vgztv.json?limit=100&broadcasts=true'), true);
	
	print_r($recordings);
?>