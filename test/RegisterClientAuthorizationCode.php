<?php
/**
 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
 */

/**
 * Note: Keep a record of all generated data as it will be required later on.
 * STEP 1: Generate Temporary Client Credentials
 * Run the IssueTemporaryClient.php
 * Keep the temporary client credentials generated
 * STEP 2: Generate Code via the link below:
 * http://api.blubrry.com/oauth2/authorize?response_type=code&client_id=TEMPCLIENTID&redirect_uri=YOURREDIRECTURI&state=random
 * Grant Access by clicking the Authorize button
 * A code will be generated and displayed.
 * Keep the code.
 * STEP 3: Generate Client Credentials
 * Run IssueClient.php in the command line using the parameters below
 * Parameters are: --verbose --temp_client_id TEMPCLIENTID --temp_client_secret TEMPCLIENTSECRET --code CODE
 * Keep the client credentials
 * STEP 4: Generate Authorization Code for the registered client
 * Run RegisterClientAuthorizationCode.php in the command line using the parameters below
 * Parameters are: --verbose --client_id CLIENTID --client_secret CLIENTSECRET --code CODE
 */

$localLog = '';
$verbose = '';

$client_id='';
$client_secret='';
$code = '';
$redirect_uri = '';

if( count($argv) == 10 )
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
			case '--redirect_uri': { //Sets the value for $code
				$redirect_uri = trim($argv[$x+1]);
			}; break;
		}
	}

	LocalEcho("Processing Register Client Authorization Code: \n\n");

	require_once(dirname(dirname(__FILE__)).'/src/BlubrryApiOauth2.php'); //Includes the BlubrryApiOauth2 class

	$api_test = new BlubrryAPIOauth2('http://api.blubrry.local/oauth2/'); //Create an object of BlubrryOauth2
	$api_test->setClient($client_id, $client_secret); //Sets the client credentials

	$result_code = $api_test->registerClientAuthorizationCode($code, $redirect_uri); //Gets the authorization code for the registered client

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