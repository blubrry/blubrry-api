<?php
	/**
	 * Author: Angelo Mandato <amandato@gmail.com>
	 */
	/**
	 *
	 * BlubrryApi - the base class
	 *
 	*/

	class BlubrryApi {
		
		var $m_error = '';
		var $m_token = '';
		
		// Settings:
		var $m_http_user_agent = 'API Client';
		var $m_http_connect_timeout = 2;
		var $m_http_timeout = 10;
		var $m_http_auth_info = array();
		var $m_http_send_headers = array();
		
		var $m_http_code = 0;
		var $m_http_content_type = '';
		var $m_http_content_length = 0;
		var $m_http_body = '';
		var $m_http_headers = '';
		var $m_http_bytes_total = 0;


		function __construct() {}

		/**
		 * Gets the error message.
		 * @return string - error message
		 */
		function getError()
		{
			return $this->m_error;
		}

		/**
		 * Sets the error message to a member variable of this class
		 * @param string $msg - error message
		 */
		function setError($msg)
		{
			$this->m_error = $msg;
		}

		/**
		 * Get the HTTP message body in JSON  associative array format
		 * @return array|false - associative array or false if failure
		 */
		function getJsonResult()
		{
			return @json_decode($this->m_http_body, true);
		}

		/**
		 * Get the HTTP message body
		 * @return string - HTTP message body
		 */
		function getBody()
		{
			return $this->m_http_body;
		}

		/**
		 * Gets HTTP headers received from the server
		 * @return array - HTTP headers received from the server
		 */
		function getHeaders()
		{
			return $this->m_http_headers;
		}

		/**
		 * Set the total and connection timeouts
		 * @param $timeout - HTTP idle timeout
		 * @param int $connect_timeout - HTTP connect timeout
		 */
		function setTimeout($timeout, $connect_timeout = 2)
		{
			$this->m_http_timeout = $timeout;
			$this->m_http_connect_timeout = $connect_timeout;
		}

		/**
		 * Set this client's user agent
		 * @param $user_agent - HTTP User agent
		 */
		function setUserAgent($user_agent)
		{
			$this->m_http_user_agent = $user_agent;
		}

		/**
		 * Sets user, password and authentication method
		 * @param string $user - User
		 * @param string $password - Password
		 * @param int $method - Method
		 */
		function setAuth($user, $password, $method = CURLAUTH_ANY)
		{
			$this->m_http_auth_info['user'] = $user;
			$this->m_http_auth_info['password'] = $user;
			$this->m_http_auth_info['method'] = $method;
		}

		/**
		 * Sets the basic_auth value for Basic authentication header
		 * @param string $basic_auth - string base64_encoded
		 */
		function setAuthBasic($basic_auth)
		{
			$this->addCustomHeader('Authorization: Basic '.$basic_auth);
		}
		
		/**
		 * Add a custom header to send to server
		 * @param string $header - Custom header line (e.g. X-Cache: yes)
		 */
		function addCustomHeader($header)
		{
			$this->m_http_send_headers[] = $header;
		}

		/**
		 * Get HTTP status code returned from server
		 * @return int - HTTP status code
		 */
		function getHTTPCode()
		{
			return $this->m_http_code;
		}

		/**
		 * Get content type returned from server
		 * @return string - HTTP content type
		 */
		function getContentType()
		{
			return $this->m_http_content_type;
		}

		/**
		 * Get content length returned from server
		 * @return int - HTTP content length
		 */
		function getContentLength()
		{
			return $this->m_http_content_length;
		}

		/**
		 * Sets oAuth2 client credentials
		 * @param string $client_id - Client ID
		 * @param string $client_secret - Client secret
		 */
		function setClient($client_id, $client_secret)
		{
			$this->addCustomHeader('Authorization: Basic '. base64_encode( $client_id . ':' . $client_secret ) );
		}

		/**
		 * Sets oAuth2 Bearer access token
		 * @param $token - Access token
		 */
		function setAccessToken($token)
		{
			$this->addCustomHeader('Authorization: Bearer '. $token );
		}

		/**
		 * GET data from server
		 * @param string $url - URL
		 * @return bool|mixed - true or false
		 */
		function get($url)
		{
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content

			$results = curl_exec($curl);

			if( !$this->_http_cleanup($curl) )
				return false;
			
			return $results;
		}

		/**
		 * POST data from associative array to server
		 * @param string $url - URL
		 * @param array $post_array - Array of name value pairs
		 * @param array $files_array - Array of files
		 * @return bool|mixed - true or false
		 */
		function post($url, $post_array = array(), $files_array = array() )
		{
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content

			curl_setopt($curl, CURLOPT_POST, 1);
			if( count($files_array) > 0 )
			{
				while( list($key,$file) = each($files_array) )
					$post_array[ $key ] = '@'.$file;
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post_array );
			}
			else
			{
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_array) );
			}

			$results = curl_exec($curl);

			if( !$this->_http_cleanup($curl) )
				return false;

			return $results;
		}

		/**
		 * POST data that does not require urlencoding to server
		 * @param string $url - URL
		 * @param $post_raw - data that is not URL encoded
		 * @param string $content_type - Content Type
		 * @return bool|mixed - true or false
		 */
		function postString($url, $post_string, $content_type = '' )
		{
			if( !empty($content_type) )
				$this->addCustomHeader('Content-Type: '.$content_type);
				
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content
			
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string );
			
			$results = curl_exec($curl);
			
			if( !$this->_http_cleanup($curl) )
				return false;
			
			return $results;
		}

		/**
		 * Retrieves headers in the form of an associated array
		 * @param $url
		 * @return array|false
		 */
		function head($url)
		{
			
			$curl = $this->_http_init($url);
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 1); // header will be at output
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request 
			curl_setopt($curl, CURLOPT_NOBODY, 1);
			
			$results = curl_exec($curl);

			if( !$this->_http_cleanup($curl) )
				return false;
			
			// Return headers as an array here...
			$headers = array();
			$lines = explode("\n", $results);
			while( list($null,$header) = each($lines) )
			{
				if( preg_match('/([^:]*):(.*)/', $header, $matches) )
					$headers[ strtolower(trim($matches[1])) ] = trim($matches[2]);
			}
			return $headers;
		}

		/**
		 * PUT specified file to server (e.g. WebDAV method)
		 * @param string $url - URL
		 * @param string $file - file
		 * @return bool - true or false
		 */
		function put($url, $file)
		{
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content
			
			$filesize = filesize($file);
			$fp = fopen($file, 'rb');
			curl_setopt($curl, CURLOPT_PUT, true);
			curl_setopt($curl, CURLOPT_INFILE, $fp);
			curl_setopt($curl, CURLOPT_INFILESIZE, $filesize );
			
			$results = curl_exec($curl);
			fclose($fp);
			
			return $this->_http_cleanup($curl);
		}
		
		/**
		 * PUT specified value to server (e.g. WebDAV method)
		 * @param string $url - URL
		 * @param $value - data
		 * @param string $content_type - Content Type
		 * @return bool - true or false
		 */
		function putString($url, $value, $content_type='')
		{
			if( !empty($content_type) )
				$this->addCustomHeader('Content-Type: '.$content_type);
			$this->addCustomHeader('Content-Length: '. strlen($value) );
			
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content
			
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($curl, CURLOPT_POSTFIELDS, $value);
			
			$results = curl_exec($curl);
			
			return $this->_http_cleanup($curl);
		}

		/**
		 * DELETE specified url from server (e.g. WebDAV method)
		 * @param string $url - URL
		 * @return bool - true or false
		 */
		public function delete($url)
		{
			$curl = $this->_http_init($url);
			$this->_http_init_common($curl); // capture headers and content
			
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			
			$results = curl_exec($curl);
			
			return $this->_http_cleanup($curl);
		}

		/**
		 * Initializes CURL
		 * @param string $url - URL
		 * @return resource
		 */
		private function _http_init($url)
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow location redirection
			curl_setopt($curl, CURLOPT_MAXREDIRS, 12); // Location redirection limit
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->m_http_connect_timeout ); // Connect time out
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->m_http_timeout ); // The maximum number of seconds to execute.
			curl_setopt($curl, CURLOPT_USERAGENT, $this->m_http_user_agent );
			curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate'); // If the server requires this, then we need to specify it
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 ); // Make sure we're using a modern user agent for byte range requests
			
			// Secure HTTP
			if( strtolower(substr($url, 0, 5)) == 'https' )
			{
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			}
			
			// HTTP Authentication
			if( count($this->m_http_auth_info) == 3 )
			{
				curl_setopt($curl, CURLOPT_HTTPAUTH, $this->m_http_auth_info['method'] );
				curl_setopt($curl, CURLOPT_USERPWD, $this->m_http_auth_info['user'] .':'. $this->m_http_auth_info['password'] );
			}
			
			// Custom headers:
			if( count($this->m_http_send_headers) > 0 )
			{
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->m_http_send_headers); 
			}
			
			// Reset the returned results:
			$this->m_http_body = '';
			$this->m_http_headers = '';
			$this->m_http_bytes_total = 0;
			
			//curl_setopt($cUrl, CURLOPT_LOW_SPEED_TIME,  10); // seconds that speed is below limit before aborting
			//curl_setopt($cUrl, CURLOPT_LOW_SPEED_LIMIT, 1); // bytes/second that must equal or be exceeded every x seconds specified in the line above
			return $curl;
		}

		/**
		 * Initializes CURL with common parameters
		 * @param $curl
		 */
		private function _http_init_common($curl)
		{
			//curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FAILONERROR, 0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_HEADERFUNCTION, array( $this, '_http_stream_headers' ) );
			curl_setopt($curl, CURLOPT_WRITEFUNCTION, array( $this, '_http_stream_body' ) ); // Must come last
		}

		/**
		 * Callback function for CURLOPT_HEADERFUNCTION
		 * @param $handle
		 * @param $headers
		 * @return int
		 */
		private function _http_stream_headers( $handle, $headers ) {
			$this->m_http_headers .= $headers;
			return strlen( $headers );
		}

		/**
		 * Callback function for CURLOPT_WRITEFUNCTION
		 * @param $handle
		 * @param $data
		 * @return int
		 */
		private function _http_stream_body($handle, $data ) {
			$bytes = strlen( $data );

			$this->m_http_body .= $data;
			$this->m_http_bytes_total += $bytes;

			// Upon event of this function returning less than strlen( $data ) curl will error with CURLE_WRITE_ERROR.
			return $bytes;
		}

		/**
		 * Cleans up the CURL
		 * @param $curl
		 * @return bool
		 */
		private function _http_cleanup($curl)
		{
			$return = true;
			$this->m_http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$this->m_http_content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
			$this->m_http_content_length = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
			
			if( curl_errno($curl) )
			{
				$return = false;
				$this->m_error = curl_error($curl); //  .' ('.curl_errno($curl).')';
			}
			
			curl_close($curl);

			if( $this->m_http_code > 399 ) {
				return false;
			}
			
			return $return;
		}
	
	};

// eof