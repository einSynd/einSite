<html>
<head>
<title>Stream Shortening List/Request</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script src="sorttable.js"></script>
<style>
/* Sortable tables */
table.sortable thead {
	background-color:#999999;
	font-weight: bold;
	cursor: default;
}

table {
	border-collapse:collapse;
}

table, th, td {
	border: 1px solid black;
}

td {
	padding: 3px;
}

.error {
	color: #900;
}

#shortName {
	width: 118px;
}
</style>
<?php
	ini_set('display_errors', 'On');
	ini_set('display_startup_errors', 'On');
	error_reporting(E_ALL ^ E_STRICT);
	
	//Make an array of all the possible sites and their prefixes
	$prefixList = array('TTV' => 'T', 'LST' => 'L', 'UTV' => 'U', 'SMA' => 'S', 'BEA' => 'B');
	
	//Generate a string of random characters of length $length with default alphanumeric charset
	function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')	{
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count-1)];
		}
		return $str;
	}
	
	//Check all the streams to make sure the one being added isn't already in
	function duplicateStream($name, $site, $array){
		foreach ($array as $key => $val){
			if (strtolower($val["name"]) === strtolower($name)){
				if ($val["site"] === $site){
					return true;
				}
			}
		}
		return false;
	}
	
	//Check if the random string is already used
	function duplicateRandom($short, $array){
		foreach ($array as $key => $val){
			if ($val["short"] == $short){
				return true;
			}
		}
		return false;
	}
	
	//List all the streams shortened
	$con = mysqli_connect("127.0.0.1","short","magic1","shorten");
	$req = "SELECT short,name,site FROM streams;";
	$result = mysqli_query($con,$req);
	
	//Say you fail to connect if you fail to connect
	if (mysqli_connect_errno($con)) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	//Fill array with current shortened streams
	while($row = mysqli_fetch_array($result)) {
		$bigList[] = array("short" => $row["short"], "name" => $row["name"], "site" => $row["site"]);
	}
	
	//If there's a $_POST, then you're trying to shorten a stream
	if (!empty($_POST)) {
		//Don't do anything if the stream site isn't in our list
		if (!empty($prefixList[$_POST["site"]])){
			$prefix = $prefixList[$_POST["site"]];
			
			//Don't do anything if they didn't type a stream name.
			if (!empty($_POST["stream"])) {
				//If they suggested a shortened name, use that.
				//No duplicate check because JQuery checks for that before submission.
				if (!empty($_POST["shortName"])){
					$shortName = $prefix . $_POST["shortName"];
				} else {
					//Keep generating strings of length 5 until you get one that's not in use.
					do {
						$shortName = $prefix . randString(5);
					} while (duplicateRandom($shortName, $bigList));
				}
				//Add to database and big array
				$req = "INSERT INTO streams(name,site,short) values ('".$_POST["stream"]."','".$_POST["site"]."','".$shortName."');";
				$result = mysqli_query($con,$req);
				$bigList[] = array("short" => $shortName, "name" => $_POST["stream"], "site" => $_POST["site"]);
			}
		}
	}
	//We're done here, close MySQL connection
	mysqli_close($con);
?>
</head>
<body bgcolor="#99999">
<form id="form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<p>Stream to shorten: &nbsp;&nbsp;<input type="text" name="stream" value="" id="stream" />
<select name="site" id="site">
	<option value="TTV">Twitch.TV</option>
	<option value="LST">Livestream</option>
	<option value="UTV">UStream</option>
	<option value="SMA">Smashcast.TV</option>
    <option value="BEA">Beam.Pro</option>
</select><br />
(optional) Short name: &nbsp;<input type="text" name="shortName" maxlength="10" value="" id="shortName" /> (10 characters or less)
<br /><input type="submit" value="Submit" name="submit" id="submit" /> &nbsp; &nbsp; First letter of the site will be added to the short name
<div id="error1" class="error"></div>
<div id="error2" class="error"></div>
</p>
</form>
Click on header to sort
<table border="1" class="sortable">
<tr>
	<th>Short</th>
	<th>Stream</th>
	<th>Site</th>
</tr>
<?php
	//Print array into table
	foreach( $bigList as $row ){
		echo "<tr><td><a href='http://".$_SERVER["SERVER_NAME"] . "/s/".$row["short"]. "'>" .$row["short"]."</a></td><td>".$row["name"]."</td><td>".$row["site"]."</td></tr>";
	}
?>
</table>
<script type="text/javascript">
//It's a bit ugly, but drop the entire stream list into the page in JSON
var streamArray = <?php echo json_encode($bigList); ?>;
	$("#form").submit(function(event) {
		$(".error").text("");
		//Check for errors before submission: Duplicate stream, duplicate shortened name
		//Same stream name and/or shortened name are allowed if the site is different
		//	as the shortened name is given a site-specific prefix.
		for( var a=0; a < streamArray.length; a++ ){
			//If the stream's already in the list, don't accept it.
			if( streamArray[a]["name"].toLowerCase() == $("#stream").val().toLowerCase() ) {
				if( streamArray[a]["site"].toLowerCase() == $("#site").val().toLowerCase() ) {
					$("#error1").text("Stream already shortened.");
					return false;
				}
			}
			if( $("#shortName").val() != "" ){
				//If the shortened name is already in use, don't accept it.
				if( streamArray[a]["short"].toLowerCase().substring(1) == $("#shortName").val().toLowerCase() ) {
					if( streamArray[a]["site"].toLowerCase() == $("#site").val().toLowerCase() ) {
						$("#error2").text("Shortened name already taken.");
						return false;
					}
				}
			}
		}
	});
</script>
</body>
</html>