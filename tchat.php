<html>
<head>
<title>Twitch Chat Selector</title>
<meta charset="utf-8"/>
<link rel="stylesheet" type="text/css" href="/css/main.css"/>
<style>
.centered { text-align: center; }
#error { color: red; }
</style>
</head>
<body>
<script type="text/javascript">
<?php
    if(isset($_GET["s"]) && $_GET["s"] !== ""){
        echo "var stream = '" . $_GET["s"] . "';";
    } else {
        echo "var stream = 'nil';";
    }
?>

window.onload = function(e){
    var newLink = document.getElementById("new");
    var oldLink = document.getElementById("old");
    
    if( stream == 'nil'){
        
        var errDiv = document.getElementById("error");
        errDiv.innerHTML = "<div id='error' class='centered'>No stream given, page will not function</div>";
        
        newLink.style.textDecoration = "underline";
        oldLink.style.textDecoration = "underline";
        
    } else {
        newLink.href = "http://www.twitch.tv/popout/"+stream+"/chat?popout=";
        oldLink.href = "https://www.twitch.tv/"+stream+"/chat?popout=&secret=safe";
        
        setTimeout(function(){
            window.location.href = "http://www.twitch.tv/popout/"+stream+"/chat?popout=";
            }, 15000);
    }
}
</script>
<div id="error"></div>
<div class="centered">Choose Twitch chat popout, old or new.<br />
Default is new, selected after 15 seconds.</div>
<br />
<br />
<div class="centered">
    <a id="new">New</a><br />
    <a id="old">Old</a>
</div>