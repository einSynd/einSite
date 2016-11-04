<html><head>
<meta charset="utf-8">
<?php
$chat = $_GET["chat"];
?>
<title>Chatango - <?php echo $chat; ?></title>
<style>
body {
	background: #000000;
	color: #999999;
}
object, embed {
	width: 100%;
	height: 100%;
}
</style>
</head><body>
<object class="fill">
	<param name="movie" value="http://<?php echo $chat; ?>.chatango.com/group"/>
	<param name="AllowScriptAccess" value="always"/>
	<param name="AllowNetworking" value="all"/>
	<param name="AllowFullScreen" value="true"/>
	<param name="bgcolor" value="#000000"/>
	<param name="flashvars" value="cid=1277925377203&a=000000&b=100&c=999999&d=848484&e=000000&g=CCCCCC&h=333333&i=29&j=CCCCCC&k=666666&l=333333&m=000000&n=CCCCCC&p=13&t=0&v=0&ab=0"/>
	<embed
		class="fill"
		type="application/x-shockwave-flash"
		src="http://<?php echo $chat; ?>.chatango.com/group"
		allowScriptAccess="always"
		allowNetworking="all"
		allowFullScreen="true"
		bgcolor="#000000"
		flashvars="cid=1277925377203&a=000000&b=100&c=999999&d=848484&e=000000&g=CCCCCC&h=333333&i=29&j=CCCCCC&k=666666&l=333333&m=000000&n=CCCCCC&p=13&t=0&v=0&ab=0">
</object>
</body></html>