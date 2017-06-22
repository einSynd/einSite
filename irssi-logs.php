<html>
<head>
<title>IRC Log Viewer</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="Autolinker.min.js"></script>
<style>
.fore00 { color: #FFFFFF; }
.fore01 { color: #000000; }
.fore02 { color: #00007F; }
.fore03 { color: #009300; }
.fore04 { color: #FF0000; }
.fore05 { color: #7F0000; }
.fore06 { color: #9C009C; }
.fore07 { color: #FC7F00; }
.fore08 { color: #FFFF00; }
.fore09 { color: #00FC00; }
.fore10 { color: #009393; }
.fore11 { color: #00FFFF; }
.fore12 { color: #0000FC; }
.fore13 { color: #FF00FF; }
.fore14 { color: #7F7F7F; }
.fore15 { color: #D2D2D2; }

.back00 { background-color:#FFFFFF; }
.back01 { background-color:#000000; }
.back02 { background-color:#00007F; }
.back03 { background-color:#009300; }
.back04 { background-color:#FF0000; }
.back05 { background-color:#7F0000; }
.back06 { background-color:#9C009C; }
.back07 { background-color:#FC7F00; }
.back08 { background-color:#FFFF00; }
.back09 { background-color:#00FC00; }
.back10 { background-color:#009393; }
.back11 { background-color:#00FFFF; }
.back12 { background-color:#0000FC; }
.back13 { background-color:#FF00FF; }
.back14 { background-color:#7F7F7F; }
.back15 { background-color:#D2D2D2; }

body, .nocolor{ color: white; background-color: black; }
.spoiler.nocolor{ color: black; background-color: black; }
.hidden { font-size: 0px; }
a{ color: inherit !important; }

html, body { margin: 0px; padding: 0px; }
#headBar { position: fixed; width: 100%; border-bottom: 1px solid #000; background: inherit; 
			top: 0px; padding: 2 5; }
#colorButtons { float: right; padding: 0 5 0 0; }
input { margin: 0px; padding: 0px; }
#content { display: inline-block; padding: 10 0 5 5; margin: 15 0 0 0;}
.line { display: inline; }

</style>
</head>
<body>
<script type="text/javascript">
function setColor(match, p1, offset, string){
	if(p1.length == 1){ p1 = "0" + p1; }
	return "</span><span id='color' class='fore" + p1 + " backNo'>";
}

function setColor2(match, p1, p2, offset, string){
	if(p1.length == 1){ p1 = "0" + p1; }
	if(p2.length == 1){ p2 = "0" + p2; }
	
	var spoiler = "";
	if(p1 == p2){ spoiler = " spoiler"; }

	return "</span><span id='color' class='fore" + p1 + " back" + p2 + spoiler + "'>";
}

function addAnchor(match, p1, offset, string){
	return "<span id='"+p1+"'>"+p1+"</span>";
}

function changeColor(bgColor){
	$("body").css("background",bgColor);
	
	color="black";
	if(bgColor=="black"){
		color = "white"; }

	$("body").css("color",color);
	$("#headBar").css("border-color",color);
}

function fileCheck(date, first){
	if(date==""){ date="today"; }
	//Get today minus five hours because GMT-5
	var today = new Date();
	today.setTime(today.getTime() - 5 * 60 * 60 * 1000);
	
	//Strip off the actual time, just need the day
	today = today.toISOString().split("T")[0];
	
	if(date == "today") {
		date = today;
	}
	
    $("#currentLog").html(date);
	file = "http://" + window.location.host + "/vgzlogs/"+date+".log";
	$.get(file)
    .done(function(data) { 
	
		//Get rid of a bunch of IRSSI's internal coloration codes	
		data = data.replace(/\u0004.\//g,"");
		data = data.replace(/\u0004./g,"");

		//Parse the raw log into something vaguely HTML compatible
		//Split and rejoin the greater than and less than signs; a bit faster than global regex
		data = data.split("/?<").join("&lt;");
		data = data.split("/?>").join("&gt;");
        	data = "<span class='line'>" + data;
		
		//Turn unicode 0002 (stx) into Strong tag for bold
		data = data.replace(/\u0002/g,"<strong>");
		//Turn unicode 0003 (etx) plus the following numbers into spans for color tags
		data = data.replace(/\u0003(\d{1,2}),(\d{1,2})/g,setColor2);
		data = data.replace(/\u0003(\d{1,2})/g, setColor);
		//Turn unicode 0003 (etx) without a following numbers into end span
		data = data.replace(/\u0003/g, "");
		//Turn unicode 000F (si) into closing strong and span
		data = data.replace(/\u000f/g, "</strong></span>");
		//End the line
		data = data.replace(/\n/g,"</strong></span></span><br />\r\n<span class='line'>");
	        //Add anchors (.php#stuff) for the timestamps
		data = data.replace(/^(\d{2}:\d{2})/g, addAnchor);
	        //Give the JOIN and QUIT messages a span so they can be toggled
        	data = data.replace(/(-!- \S+ \[\S+\]) has quit/g,"<span class='connect'>$1 has quit</span>");
		data = data.replace(/(-!- \S+ \[\S+\]) has joined/g,"<span class='connect'>$1 has joined</span>");

		var autolinker = new Autolinker();

	        var anchored = autolinker.link(data);
		$("#content").html(anchored);
		
		if(date == today){
			$("#dateUp").prop("disabled",true);
		} else {
			$("#dateUp").prop("disabled",false);
		}
		
		if(date == "2015-01-01"){
			$("#dateDown").prop("disabled",true);
		} else {
			$("#dateDown").prop("disabled",false);
		}
		
		if(typeof first != 'undefined' && window.location.hash != ""){
			location.hash = window.location.hash;
			window.scrollTo(0, window.scrollY - 28);
		}
		
    }).fail(function() { 
        $("#content").html("There is no log for this date.");
    })
}

function changeDate(direction){
	var current = $("#currentLog").html();
	var oneDay = 86400000;
	
	var newDay = Date.parse(current);
	if( direction == "up" ) { newDay = newDay + oneDay; }
	if( direction == "down" ) { newDay = newDay - oneDay; }
	newDay = new Date(newDay);
	newDay = newDay.toISOString().split("T")[0];
	
	$("#content").html("");
	fileCheck(newDay);
}

$(document).ready(function(){
	fileCheck("<?php if(isset($_GET["d"])) { echo $_GET["d"]; } ?>", true);
	
});
</script>
<div id="headBar">
<input type="button" onclick="$('span').toggleClass('nocolor');" value="Toggle Colors" /> 
<input type="button" onclick="$('.connect').parent().toggleClass('hidden');" value="Toggle Connections" />&nbsp; &nbsp; &nbsp; &nbsp;
<input type="button" onclick="changeDate('down');" value="<" /><span id="currentLog"></span><input type="button" id="dateUp" onclick="changeDate('up');" value=">" />&nbsp; &nbsp; &nbsp; &nbsp;
Scroll: <input type="button" onclick="$('body').scrollTop(0);" value="&nbsp;&#8593;&nbsp;" />
<input type="button" onclick="$('body').scrollTop($('body').height());" value="&nbsp;&#8595;&nbsp;" />
<div id="colorButtons">Background: 
<input type="button" onClick="changeColor('white')" value="White" /><input type="button" onClick="changeColor('gray')" value="Gray" /><input type="button" onClick="changeColor('black')" value="Black" />
</div>
</div>
<div id="content"></div>
</body>
</html>
