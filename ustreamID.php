<?php
/* UStream fucked their API again.
    Only way to currently get channel ID from channel name
    is to download the entire stream page and parse it out.
*/

//	ini_set('display_errors', 'On');
//	error_reporting(E_ALL);
	date_default_timezone_set('America/New_York');
	header('Content-Type: application/json');
	
    // Callback default to ?, unless specified.
	$callback = "?";
	if(isset($_GET["callback"])){
		$callback = $_GET["callback"];
	}
    
	// Check for stream variable.
	if(!isset($_GET["stream"])){
		// If there's no stream given, return an error.
		$data = '{"media_type": "error", "error_description","No \'stream\' parameter given."}';
	} else {
        $stream = $_GET["stream"];
        //Get the API request URL		
		$apiURL = "http://www.ustream.tv/channel/".$stream;
        $headers = get_headers($apiURL);
        //var_dump($headers);
        foreach ($headers as $info){
            if( strpos($info, "Content-Id") > 0 ){
                $id = substr($info, strpos($info, ":")+2);
            }
        }
/*		
    //Turns out, it sends the ID over headers.
    //Keeping cURL here as an example maybe?
    
		//Start a cURL request so I can actually handle errors.
		$fileReq = curl_init($apiURL);
		//Makes cURL not print the results straight to the page.
		curl_setopt($fileReq, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($fileReq, CURLOPT_FOLLOWLOCATION, true);
        
		$data = curl_exec($fileReq);
		if($data === false){
			$error = curl_error($fileReq);
			if( strpos($error, "Host not found") ){
				$data = '{"media_type": "error","error_description": "Failed to connect to Hitbox.tv"}';
			} else {
				$data = '{"media_type": "error","error_description": "' . $error . '"}';
			}
		} elseif( $data == "no_media_found") {
			$data = '{"media_type": "error","error_description": "Stream not found."}';
		}
		curl_close($fileReq);
    
        //Find the first instance of contentId (will be ustream.vars.channelId)
        //  and removes everything before it
        $data = substr($data, strpos($data, "contentId"));
        //Removes the variable name before the equals sign
        $data = substr($data, strpos($data, "=")+1);
        //Removes everything after the semicolon marking the end of channel ID
        $data = substr($data, 0, strpos($data, ";"));
*/

        //Put the ID into an actual JSONP format
        $data = '{"channel":"'.$stream.'", "id":"'.$id.'"}';
        
        //Return it in JSONP format
        echo $callback . "(" . $data . ")";
    }
?>