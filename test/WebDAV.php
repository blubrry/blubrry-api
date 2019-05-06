<?php
/**
 * Created by PhpStorm.
 * User: Henry Nguyen
 * Date: 5/3/2019
 * Time: 10:33 AM
 */

$localLog = '';
$verbose = '';
$program = '';
$file_name = '';
$file = '';
$username = '';
$password = '';

if( count($argv) > 1 )
{
    for( $x = 1; $x < count($argv); $x++ )
    {
        switch($argv[$x])
        {
            case '--program-keyword': {
                $program = trim($argv[$x + 1]);

            }; break;

            case '--verbose': { //Prints results to std out
                $verbose = true;

            }; break;

            case '--file':{
				$file = trim($argv[$x + 1]);
                $file_name = basename($file);

            }; break;

            case '--username':{
                $username = trim($argv[$x + 1]);
            }; break;

            case '--password':{
                $password = trim($argv[$x + 1]);

            }; break;
			case '--help': {
				printUsage();
			}; break;
        }
    }
}

// Check for required parameters
if( empty($program) || 
		empty($file) ||
		empty($username) ||
		empty($password) ) {
	printUsage();
}

require_once(dirname(dirname(__FILE__)).'/src/BlubrryApi.php'); //Includes the BlubrryApiOauth2 class
$api_test = new BlubrryApi();


/**
 *  url format: https://api.blubrry.com/media/program_keyword/filename.ext
 */
$api_test->setAuth($username, $password, CURLAUTH_BASIC);
$api_test->put("https://api.blubrry.com/media/{$program}/{$file_name}",$file);

//uncomment to see list of unpublished files
//$result = $api_test->get("https://api.blubrry.com/media/{$program}/index.xml");


if( $result )
    localEcho("We have results!\n");

else if( !empty( $api_test->getError() ) )
    localEcho($api_test->getError() ."\n");

else if($api_test->getHTTPCode() == '201')
    echo "HTTP CODE: ". $api_test->getHTTPCode()." Upload Successful\n";

else
    localEcho("HTTP Error: ". $api_test->getHTTPCode() ."\n");
localEcho("\n");





/**
 * Displays the output
 * @param string $val - output
 */
function localEcho($val) {
    global $verbose, $localLog;

    if( $verbose )
        echo $val;
    $localLog .= $val;
}

function printUsage() {
	echo "Usage:\n\n";
	echo "php WebDAV.php --program-keyword your_show_keyword --username email@address.com --password accountpassword --file /path/to/file.mp3 --verbose\n\n";
	exit;
}
