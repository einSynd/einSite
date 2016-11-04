<html><head>
<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	
	$stream = $_GET['s'];
	$site   = $_GET['ss'];
	$title = $stream.' chat page';
	echo '<title>'.$title.'</title>';
?>
<style> body, #chat, #jtv_chat {padding:0px; margin:0px; width:100%; height:100%;} small_notice {visibility:hidden;} </style>
</head>
<body bgcolor="#000">
<?php
	$jtv = '<iframe id="chat" frameborder="0" marginwidth="0" scrolling="no" src="http://www.justin.tv/chat/embed?channel=%stream%&amp;default_chat=jtv&amp;popout_chat=true#r=-rid-&amp;s=em"></iframe>';
	$utv = '<iframe id="chat" scrolling="no" frameborder="0" style="border: 0px none transparent;" src="http://www.ustream.tv/socialstream/%stream%"></iframe>';
	
	if($site=="jtv") { $chat = $jtv; }
	if($site=="utv") { $chat = $utv; }
	
	$embed = str_replace("%stream%", $stream, $chat);
	echo $embed;
?>
</body></html>