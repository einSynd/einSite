<html><head>
<title>Chatango Popups</title>
<meta charset="UTF-8">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function makeChat() {
	var chat = $("#chat").val();
    var width = 350;
    var height = 450;
    var left = 30;
    var top = 500;
    var windowFeatures = ",menubar=no,toolbar=no,scrollbars=no,resizable=yes," +
            "left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;

	windowFeatures = "width=" + width + ",height=" + height + windowFeatures;
	window.open("http://ibill.ath.cx/chatango.php?chat=" + chat, chat + " Chatango window", windowFeatures);
}
</script>
<style>
body {
   background: #999;
   margin: 0;
}
</style>
</head><body>
Enter Chatango chat name:
<form action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="text" name="chat" id="chat" />
<input type="submit" value="Popup" name="submit" id="submit" onclick="makeChat();" />
</form>
<script>$("form").submit(function(event) {event.preventDefault();});</script>
</body>
</html>