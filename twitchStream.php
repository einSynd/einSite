<html>
<head>
<script type="text/javascript" src="https://cdn.viblast.com/vb/stable/viblast.js"></script>
</head>
<body>
<?php
$stream = json_decode(file_get_contents("http://api.twitch.tv/api/channels/" . $_GET['stream'] . "/access_token?client_id=glrjuvya5xmg8batkujqtfij53q073h"));
$playlist = file_get_contents("http://usher.twitch.tv/api/channel/hls/" . $_GET['stream'] . ".m3u8?player=twitchweb&&token=" . $stream -> token . "&sig=" . $stream -> sig . "&allow_audio_only=false&allow_source=true&type=any&p=777654");
print_r($playlist);
$vid = substr($playlist, strpos($playlist, "AUTOSELECT=YES"), -1);
$vid = substr($vid, strpos($playlist, "VIDEO="), -1);
$vid = substr($vid, 0, strpos($vid, "m3u8") + 4);
$vid = substr($vid, strpos($vid, "http://"));
print("<br /><br />".$vid);
$play = file_get_contents($vid);
print("<br /><br />".$play);
?>


<video autoplay id="twitchVid" width="640" height="400" controls data-viblast-key="01969f79-2746-4c47-9386-a52fe1631c6d">
    <source src="<?php print($vid) ?>" />
</video>

</body>
</html>