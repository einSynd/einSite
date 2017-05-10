<?php
/* 
    Somehow a copy paste of the UStream ID page?
    This was a page to test returning Twitch host targets
    since they don't add an easy way to look up that info
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
		$apiURL = 'http://tmi.twitch.tv/hosts?include_logins=1&host=' . $stream;

        $data = json_decode(file_get_contents($apiURL));
        var_dump($data->hosts[0]->target_login);
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
				$data = '{"media_type": "error","error_description": "Failed to connect to Smashcast.tv"}';
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
    }
?>