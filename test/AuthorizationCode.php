<?php
/**
 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
 */

/**
 * Before you proceed running in the command line, a code must be generated first.
 * STEP 1: Generate Code
 * You may generate the code via the link below and replace the text in uppercase with proper values:
 * http://api.blubrry.com/oauth2/authorize?response_type=code&client_id=YOURCLIENTID&redirect_uri=YOURREDIRECTURI&state=random
 * Grant Access by clicking the Authorize button
 * A code will be generated and displayed.
 * You must copy the code.
 * STEP 2: Generate Authorization Code
 * Run AuthorizationCode.php
 * Parameters: --verbose --client_id YOURCLIENTID --client_secret YOURCLIENTSECRET --redirect_uri YOURRIDERECTURI --code CODE
 */

$localLog = '';
$verbose = false;

$client_id='';
$client_secret='';
$code = '';
$redirect_uri = '';

if( count($argv) > 1 )
{
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
			case '--code': { //Sets the value for $code
				$code = trim($argv[$x+1]);
			}; break;
			case '--redirect_uri': { //Sets the value for $redirect_uri
				$redirect_uri = trim($argv[$x+1]);
			}; break;
		}
	}
}

if ( count($argv) == 10 )
{
	require_once(dirname(dirname(__FILE__)).'/src/BlubrryApiOauth2.php'); //Includes the BlubrryApiOauth2 class
	$api_test = new BlubrryAPIOauth2('https://api.blubrry.com/oauth2/'); //Create an object of BlubrryOauth2

	LocalEcho("Processing Authorization Code: \n\n");

	$api_test->setClient($client_id, $client_secret); //Sets the client credentials
	$result = $api_test->authorizationCode($code, $redirect_uri); //Returns a boolean for success or fail API call

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