<?php
/**
 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
 */

$localLog = '';
$verbose = '';

if( count($argv) > 1 )
{
	for( $x = 1; $x < count($argv); $x++ )
	{
		switch($argv[$x])
		{
			case '--verbose': { //Prints results to std out
				$verbose = true;
			}; break;
		}
	}
}

LocalEcho("Processing Issue Temporary Client: \n\n");

require_once(dirname(dirname(__FILE__)).'/src/BlubrryApiOauth2.php'); //Includes the BlubrryApiOauth2 class
$api_test = new BlubrryAPIOauth2('https://api.blubrry.com/client/'); //Creates an object of BlubrryApiOauth2

$result = $api_test->issueTempClient(); //Issues temporary client credentials

if( $result )
	LocalEcho("We have results!\n");
else if( !empty($api_test->getError() ) )
	LocalEcho( $api_test->getError() ."\n");
else
	LocalEcho("HTTP Error: ". $api_test->getHTTPCode() ."\n");
echo "\n";

$creds = $api_test->getJsonResult(); //Gets the result in JSON format

if (!empty($creds))
{
	LocalEcho("JSON Results: \n");
	print_r($creds);
}
else
	LocalEcho('No JSON result available');

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