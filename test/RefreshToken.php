<?php
/**
 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
 */

/**
 * STEP 1: Run UserCredentials.php
 * Keep the refresh token
 * Parameters: --verbose --client_id YOURCLIENTID --client_secret YOURCLIENTSECRET --username USERNAME --password PASSWORD
 * STEP 2: Run RefreshToken.php
 * Parameters: --verbose --client_id YOURCLIENTID --client_secret YOURCLIENTSECRET --refresh_token REFRESHTOKEN
 * Note: Make sure to use the same client credentials on both steps.
 */

$localLog = '';
$verbose = '';

$client_id='';
$client_secret='';
$refresh_token = '';

if( count($argv) == 8 )
{
	//Initializes variables
	for( $x = 1; $x < count($argv); $x++ )
	{
		switch($argv[$x])
		{
			case '--verbose': { //Prints results to std out
				$verbose = true;
			}; break;
			case '--client_id': { //Sets the value for $client_id
				$client_id = trim($argv[$x+1]);
			}; break;
			case '--client_secret': { //Sets the value for $client_secret
				$client_secret = trim($argv[$x+1]);
			}; break;
			case '--refresh_token': { //Sets the value for $refresh_token
				$refresh_token = trim($argv[$x+1]);
			}; break;
		}
	}

	LocalEcho("Processing Refresh Token: \n\n");

	require_once(dirname(dirname(__FILE__)).'/src/BlubrryApiOauth2.php'); //Includes the BlubrryApiOauth2 class

	$api_test = new BlubrryAPIOauth2('http://api.blubrry.local/oauth2/'); //Create an object of BlubrryOauth2
	$api_test->setClient($client_id, $client_secret); //Sets the client credentials

	$result = $api_test->refreshToken($refresh_token); //Returns a boolean for success or fail API call

	if( $result )
		LocalEcho("We have results!\n");
	else if( !empty( $api_test->getError() ) )
		LocalEcho($api_test->getError() ."\n");
	else
		LocalEcho("HTTP Error: ". $api_test->getHTTPCode() ."\n");
	LocalEcho("\n");

	$tokens = $api_test->getJsonResult(); //Returns the API call result in JSON format

	if( !empty($tokens) )
	{
		LocalEcho("JSON Results:\n");
		print_r($tokens);
	}
	else
		LocalEcho("No json results available.\n");
}
else
{
	LocalEcho('Missing parameter');
}

/**
 * Displays the output
 * @param string $val - output
 */
function LocalEcho($val)
{
	global $verbose, $localLog;

	if( $verbose )
		echo $val;
	$localLog .= $val;
}


?>