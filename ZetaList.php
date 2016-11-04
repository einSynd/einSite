<html><head></head><body>
<script type="text/javascript">
var basePage="./ztest.php?pageNum=";
var pageNum=<?php echo $_GET["startAt"]; ?>;
var theIframe;

function nextPage(incAmt) {
	pageNum = pageNum + incAmt;
	var thePage = pageNum;
	if(pageNum < 100) { thePage = "0" + thePage; }
	theIframe.src = basePage + thePage;
}

function prevPage(decAmt) {
	if( pageNum - decAmt < 0 ) { return 0; }
	pageNum = pageNum - decAmt;
	var thePage = pageNum;
	if(pageNum < 100) { thePage = "0" + thePage; }
	theIframe.src = basePage + thePage;
}

window.onload = function() {
	theIframe = document.getElementById('thePage');
	nextPage(0);
}


</script>
<div>
<input type='button' onclick='prevPage(1)' value='&lt;&lt;'>
<input type='button' onclick='nextPage(1)' value='&gt;&gt;'><br />
<input type='button' onclick='prevPage(10)' value='-10'>
<input type='button' onclick='nextPage(10)' value='+10'>
</div>
<iframe id="thePage" width='99%' height='200px'></iframe>

</body></html>