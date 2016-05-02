<?php
	/**
	 * Author: Winnie Verzosa <winnie.verzosa@gmail.com>
	 */

	/**
	 * Class BlubrryApiOauth2 - API for Blubrry OAuth2
	 */
	require_once('BlubrryApi.php');
	class BlubrryApiOauth2 extends BlubrryApi
	{
		var $m_api_url = '';

		/**
		 * BlubrryApiOauth2 constructor
		 * @param string $url - API URL
		 */
		public function __construct($url='https://api.blubrry.com/oauth2/') {
			$this->setApiUrl($url);
		}

		/**
		 * Sets the API URL
		 * @param string $url - API URL
		 */
		public function setApiUrl($url)	
		{
			$this->m_api_url = $url;
		}

		/**
		 * API call to get the client credentials
		 * @return bool|mixed - true or false
		 */
		public function clientCredentials()
		{
			$args = array();
			$args['grant_type'] = 'client_credentials';

			return $this->post($this->m_api_url.'token', $args);
		}

		/**
		 * API call to get the user credentials
		 * @param string $username - Username
		 * @param string $password - Password
		 * @return bool|mixed - true or false
		 */
		public function userCredentials($username, $password)
		{
			$args = array();
			$args['username'] = $username;
			$args['password'] = $password;
			$args['grant_type'] = 'password';

			return $this->post($this->m_api_url.'token', $args);

		}

		/**
		 * API call to get the refresh token
		 * @param string $refresh_token - Refresh Token
		 * @return bool|mixed - true or false
		 */
		public function refreshToken($refresh_token)
		{
			$args = array();
			$args['refresh_token'] = $refresh_token;
			$args['grant_type'] = 'refresh_token';

			return $this->post($this->m_api_url.'token', $args);
		}

		/**
		 * API call to get the Access token from the Authorization Code
		 * @param string $code - Authorization Code generated
		 * @param string $redirect_uri - URL
		 * @return bool|mixed - true or false
		 */
		public function authorizationCode($code, $redirect_uri)
		{
			$args = array();
			$args['code'] = $code;
			$args['redirect_uri'] = $redirect_uri;
			$args['grant_type'] = 'authorization_code';

			return $this->post($this->m_api_url.'token', $args);
		}

		/**
		 * API call to get the Access token from the Authorization Code of a registered client
		 * @param string $code - Authorization code generated
		 * @param string $redirect_uri - URL
		 * @return bool|mixed - true or false
		 */
		public function registerClientAuthorizationCode($code, $redirect_uri)
		{
			$args = array();
			$args['code'] = $code;
			$args['redirect_uri'] = $redirect_uri;
			$args['grant_type'] = 'authorization_code';

			return $this->post($this->m_api_url.'token', $args);
		}

		/**
		 * Issues Temporary Client Credentials
		 * @return bool|mixed - true or false
		 */
		public function issueTempClient()
		{
			return $this->get($this->m_api_url.'temporary');
		}

		/**
		 * Issues Client Credentials
		 * @param string $code -  Authorization code generated
		 * @return bool|mixed - true or false
		 */
		public function issueClient($code)
		{
			return $this->get($this->m_api_url.'issue?code='.$code);
		}
	}

?>