<html>
<head>
<title>Test</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript">
var user = "<?php echo $_GET["user"]; ?>";
var pass = "<?php echo $_GET["pass"]; ?>";

function getToken(info) {
	$.ajax({
		url: 'http://api.hitbox.tv/auth/token',
		timeout: 15000,
		data: { "login": user, "pass": pass, "app": "desktop" },
		type: "POST",
		success: function(data) {
			data = $.parseJSON(data);
			postInfo(info, data["authToken"]);
		},
		error: function(a,b,c) {
			$("#data").text(b);
		}
	});
}

function getInfo() {
	$.ajax({
		url: 'http://api.hitbox.tv/media/live/' + user,
		timeout: 15000,
		type: "GET",
		success: function(data) {
			data = $.parseJSON(data);
			getToken(data);
		},
		error: function(a,b,c) {
			$("#data").text(b);
		}
	});
}

function postInfo(info, token) {
	var newTitle = "ein Streams things";
	var newGame  = "14978";
	
	info = info["livestream"][0];
	
	console.log(info);
	console.log(token);
	
	info["media_category_id"] = newGame;
	info["media_status"] = newTitle;
	
	console.log(info);
	
	$.ajax({
		url: 'http://api.hitbox.tv/media/live/' + user + '?authToken=' + token,
		timeout: 15000,
		contentType: 'application/json',
		data: info,
		type: "POST",
		success: function(data){
			console.log(data);
		},
		error: function(a,b,c){
			console.log(a);
			console.log(b);
		}		
	});
}

$(document).ready(function(){

	getInfo();

});

</script>
<div id="data"></div>
</body>
</html>