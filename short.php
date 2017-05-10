<?php
	ini_set('display_errors', 'On');
	ini_set('display_startup_errors', 'On');
	error_reporting(E_ALL ^ E_STRICT);
	
	$stream = $_GET["s"];
	$req = "SELECT * FROM streams ";
	
	if (substr($stream, 0, 1) == "T") {
		$req = $req . "WHERE site='TTV' AND short='" . $stream . "';";
	} else if (substr($stream, 0, 1) == "L") {
		$req = $req . "WHERE site='LS' AND short='" . $stream . "';";
	} else if (substr($stream, 0, 1) == "U") {
		$req = $req . "WHERE site='UTV' AND short='" . $stream . "';";
    } else if (substr($stream, 0, 1) == "H") {
        $req = $req . "WHERE site='HBX' AND short='" . $stream . "';";
    } else if (substr($stream, 0, 1) == "S") {
		$req = $req . "WHERE site='SMA' AND short='" . $stream . "';";
    } else if (substr($stream, 0, 1) == "B") {
        $req = $req . "WHERE site='BEA' AND short='" . $stream . "';";
	} else {
		$req = $req . "WHERE short='".$stream."';";
	}
	
	$con = mysqli_connect("127.0.0.1","short","magic1","shorten");
	
	// Check connection
	if (mysqli_connect_errno($con)) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	//echo $req;
	
	$result = mysqli_query($con, $req);
	if (mysqli_num_rows($result)==0) { 
		echo "No stream found using ID ". $stream;
	} else {
		$found = mysqli_fetch_array($result);
        if( $found["site"] == "HBX" ){
            $found["site"] = "SMA";
        }
		echo "Found stream: " . $stream . " => " . $found["name"] . " on " . $found["site"] . "<br />";
		echo "Redirecting to: http://einsynd.pw/p?s=" . $found["name"] . "," . $found["site"] . "<br />";
		echo "Should take less than a second. If not, try clicking the link above.";
		echo "<script type=\"text/javascript\">setTimeout('window.location.href=\"http://einsynd.pw/p?s=" . $found["name"] . "," . $found["site"] . "\"',500);</script>";
	}

	mysqli_close($con);
?> 