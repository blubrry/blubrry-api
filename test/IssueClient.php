<?php
/**
 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
 */

$localLog = '';
$verbose = '';

$temp_client_id='';
$temp_client_secret='';
$code = '';

if( count($argv) > 1 )
{
	for( $x = 1; $x < count($argv); $x++ )
	{
		switch($argv[$x])
		{
			case '--verbose': { //Prints results to std out
				$verbose = true;
			}; break;
			case '--temp_client_id': { //Sets the value for $client_id
				$temp_client_id = trim($argv[$x+1]);
			}; break;
			case '--temp_client_secret': { //Sets the value for $client_secret
				$temp_client_secret = trim($argv[$x+1]);
			}; break;
			case '--code': { //Sets the value for $client_id
				$code = trim($argv[$x+1]);
			}; break;
		}
	}
}

if ( count($argv) == 8 ) //Checks if all the parameters are entered
{
	LocalEcho("Processing Issue Client: \n\n");

	require_once(dirname(dirname(__FILE__)) . '/src/BlubrryApiOauth2.php'); //Includes the BlubrryApiOauth2 class

	$api_test = new BlubrryAPIOauth2('https://api.blubrry.com/client/'); //Creates an object of BlubrryApiOauth2
	$api_test->setClient($temp_client_id, $temp_client_secret); //Sets the client credentials using the temporary client credentials

	$result = $api_test->issueClient($code); //Issues client credentials

	if ($result)
		LocalEcho("We have results!\n");
	else if (!empty($api_test->getError()))
		LocalEcho($api_test->getError() . "\n");
	else
		LocalEcho("HTTP Error: " . $api_test->getHTTPCode() . "\n");
	LocalEcho("\n");

	$tokens = $api_test->getJsonResult(); //Returns the API call result in JSON format

	if (!empty($tokens)) {
		LocalEcho("JSON Results:\n");
		print_r($tokens);
	} else
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