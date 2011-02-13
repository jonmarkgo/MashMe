<?php

/**
 * This file contains the main Fanbridge API connection library.
 *
 * Requeriments:
 * - PHP >= 5.
 * - PHP Extensions: CURL, JSON.
 *
 * @version 1.0
 * @author Fanbridge Inc team.
 */

/* Validations: Required extensions. */

if (!function_exists('curl_init'))
{
	throw new Exception('Fanbridge needs the CURL PHP extension.');
}
if (!function_exists('json_decode'))
{
	throw new Exception('Fanbridge needs the JSON PHP extension.');
}

/**
 * General exception handler.
 */
class FanBridge_Api_Exception extends Exception {
	
	/**
	 * @var int FanBridge Error Code.
	 */
	protected $fanbridge_code;
	
	/**
	 * @var string FanBridge Code Message.
	 */
	protected $fanbridge_message;

	/**
	 * @var int HTTP Error Code.
	 */
	protected $http_code;
	
	/**
	 * Builds a new Fanbridge general exception.
	 *
	 * @param $http_code integer HTTP Error code.
	 * @param $fanbridge_message string FanBridge Error message.
	 * @param $fanbridge_code integer FanBridge Error code.
	 * @return void.
	 */
	public function __construct($http_code, $fanbridge_message = null, $fanbridge_code = null) {

		$this->http_code = $http_code;
		
		parent::__construct($fanbridge_message, $fanbridge_code);
	}
	
	/**
	 * Returns the HTTP error code.
	 * 
	 * @return integer HTTP error code.
	 */
	public function get_http_code() {
		
		return $this->http_code;
	}
}

/**
 * Provides access to Fanbridge functions.
 */
class FanBridge_Api {

	/**
	 * @const Allowed formats.
	 */
	const FORMAT_JSON = 'json';
	const FORMAT_PLAINTEXT = '';

	/**
	 * @var array Main config.
	 */
	public static $cfg = array(

		// CURL defaults
		'curl' => array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_USERAGENT => 'fanbridge-php-1.0'),

		// Fanbridge domains
		'url' => array(
			'api' => 'https://api.fanbridge.com/v3/',
			'site' => 'http://www.fanbridge.com/'),

		// Known packages and methods
		'path' => array(
			'request_access' => 'auth/request_access',
			'request_token' => 'auth/request_token'));

	/**
	 * @var string Client secret.
	 */
	protected $secret;

	/**
	 * @var string Client token.
	 */
	protected $token;
	
	/**
	 * @var string Response Format.
	 */
	protected $response_format = self::FORMAT_JSON;
	
	/**
	 * @var array Request info.
	 */
	protected $request_info;

	/**
	 * Initializes the Fanbridge API library.
	 *
	 * @param $cfg array Configuration for library.
	 * @return void.
	 */
	public function __construct(array $cfg) {

		$this->secret = (isset($cfg['secret']) ? $cfg['secret'] : null);
		$this->token = (isset($cfg['token']) ? $cfg['token'] : null);
	}

	/**
	 * Sets the format for the response.
	 * 
	 * @param $format string The format for the response.
	 * @return void.
	 */
	public function set_response_format($format) {

		if (!in_array($format, array(self::FORMAT_JSON, self::FORMAT_PLAINTEXT)))
		{
			throw new FanBridge_Api_Exception('', 'Invalid response format.');
		}

		$this->response_format = $format;
	}

	/**
	 * Gets the request info.
	 * This could be usefull for debugging, and contains the CURL request info.
	 * 
	 * @return array Info about the request.
	 */
	
	public function get_request_info() {
		
		return $this->request_info;
	}
	
	/**
	 * Sets the transaction token.
	 * 
	 * @param $token string Transaction token.
	 * @return void.
	 */
	public function set_token($token) {
		
		$this->token = $token;
	}
	
	/**
	 * Get the access url (login page).
	 * 
	 * @param $app_key string Application key.
	 * @return string URL to the request_access page.
	 */
	public static function get_request_access_url($app_key) {

		$res = self::$cfg['url']['api'] . self::$cfg['path']['request_access']
			. "?app_key={$app_key}";

		return $res;
	}

	/**
	 * Gets the transaction token.
	 *
	 * @return string A valid transaction token; FALSE otherwise.
	 */
	public function request_token() {

		$res = $this->send(self::$cfg['path']['request_token'], 'POST',
			array('secret' => $this->secret), self::FORMAT_PLAINTEXT);

		return $res;
	}

	/**
	 * Sends a request to FanBridge API server.
	 * Note: JSON is the only response format allowed for now.
	 *
	 * @param $path string The path to get data from (package/method).
	 * @param $params array Params we want to pass to the FanBridge application method.
	 * @param $method string Request method (GET|POST). Default is 'GET'.
	 * @return mixed The retrieved data in JSON format.
	 */
	public function call($path, array $params = array(), $method = null) {

		if (empty($method))
		{
			// Try with the intelligent HTTP method selector
			$method = $this->get_http_method($path);
		}

		// Prepares URI
		$params['token'] = $this->token;
		$params['signature'] = $this->generate_signature($params, $this->secret);

		// Calls to API
		$res = $this->send($path, $method, $params, $this->response_format);

		return $res;
	}

	/**
	 * Invokes the Fanbridge API.
	 *
	 * @param $path string The path to get data from (package/method).
	 * @param $method string Request method (GET|POST). Default is 'POST'.
	 * @param $params array Params we want to pass to the FanBridge application method.
	 * @param $format string The retrieved data format.
	 * @return mixed Server response in desired format.
	 */
	private function send($path, $method, array $params = array(),
		$format = self::FORMAT_JSON) {

		$format = ($format == self::FORMAT_PLAINTEXT ? $format : ".{$format}");
		if ($method == 'POST')
		{
			$curl_opts =
				self::$cfg['curl'] +
				array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => self::$cfg['url']['api'] . "{$path}{$format}",
					CURLOPT_POSTFIELDS => http_build_query($params, null, '&'));
		}
		else // GET assumed
		{
			$curl_opts =
				self::$cfg['curl'] +
				array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => self::$cfg['url']['api'] . "{$path}{$format}?"
						. http_build_query($params, null, '&'));
		}

		// Invokes FanBridge API
		$ch = curl_init();
		curl_setopt_array($ch, $curl_opts);
		$res = curl_exec($ch);

		// Sets debugging info
		$this->request_info = curl_getinfo($ch);
		
		// If something gone wrong, launch exception
		if ($res === false)
		{
			$e = new FanBridge_Api_Exception(curl_errno($ch));
			curl_close($ch);
			throw $e;
		}
		else if ($this->request_info['http_code'] !== 200)
		{
			$res = json_decode($res);
			$e = new FanBridge_Api_Exception($this->request_info['http_code'],
				$res->description, $res->code);
			curl_close($ch);
			throw $e;
		}

		curl_close($ch);
		return $res;
	}

	/**
	 * Gets the proper HTTP method by choosed path.
	 *
	 * @param $path string The path to get data from (package/method).
	 * @return string The proper HTTP method for the function.
	 */
	private function get_http_method($path) {

		// All URLs for 'POST' HTTP method
		$post_urls = array(
			'auth/request_token',
			'email_campaign/cancel_schedule',
			'email_campaign/create',
			'email_campaign/create_targeting',
			'email_campaign/schedule',
			'email_campaign/target_all',
			'email_campaign/update',
			'email_group/create',
			'subscriber/add',
			'socialnet_campaign/create',
			'socialnet_campaign/cancel_schedule');

		$res = 'GET';
		if (in_array($path, $post_urls))
		{
			$res = 'POST';
		}

		return $res;
	}

	/**
	 * Generates the URL signature to ensure data integrity.
	 *
	 * @param $args array All params and values we need to send.
	 * @param $secret string User session secret.
	 * @return string The signature for the URL.
	 */
	private function generate_signature(array $args, $secret) {

		ksort($args);
		$request_str = '';
		foreach ($args as $key => $value)
		{
			if (is_bool($value))
			{
				$value = (int) $value;
			}
			
			$request_str .= $key . '=' . $value;
		}
		$request_str .= $secret;
		$res = md5($request_str);

		return $res;
	}
}
