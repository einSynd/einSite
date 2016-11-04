<html><head></head><body>
<script type="text/javascript">
var basePage="https://linezeta.files.wordpress.com/2014/01/icon";
var start=<?php echo $_GET["start"]; ?>;
var end=<?php echo $_GET["end"]; ?>;

window.onload = function() {
	if( start > end ) {
		var temp = start;
		start = end;
		end = temp;
	}
	
	var theDiv = document.getElementById('theDiv');
	for( var i = start; i <= end; i++ ) {
		theDiv.innerHTML = theDiv.innerHTML + "<img src='" + basePage + i + ".png' />";
	}
}


</script>
<div id="theDiv">
</div>

</body></html>