<?php

/**
 * Lassie API implementation library for PHP
 */
class LassieApi {

    /**
     * Source type constants
     */
    const CASH_SOURCE_TYPE_ID = 1;
    const PIN_SOURCE_TYPE_ID = 2;
    const BANK_SOURCE_TYPE_ID = 3;
    const FIRST_ACCOUNT_SOURCE_TYPE_ID = 4;
    const SECOND_ACCOUNT_SOURCE_TYPE_ID = 5;
    const GENERAL_ACCOUNT_SOURCE_TYPE_ID = 6;
    const UPDATE_STOCK_SOURCE_TYPE_ID = 7;
    const TICKET_SOURCE_TYPE_ID = 8;

    /**
     * Post name constants
     */
    const API_KEY_POST_NAME = 'api_key';
    const API_HASH_POST_NAME = 'api_hash';
    const API_HASH_CONTENT_POST_NAME = 'api_hash_content';
    const TRANSACTION_SIGNATURE_POST_NAME = 'transaction_signature';
    const TRANSACTION_PRODUCTS_POST_NAME = 'transaction_products';
    const TRANSACTION_ACCOUNT_NAME_POST_NAME = 'transaction_account_name';
    const TRANSACTION_ACCOUNT_ID_POST_NAME = 'transaction_account_id';
    const TRANSACTION_UPGRADE_DELTA_BALANCE_POST_NAME = 'transaction_upgrade_delta_balance';

    /**
     * URI constants
     */
    const POST_TRANSACTION_URI = 'transaction';
    const POST_UPGRADE_ACCOUNT_URI = 'transaction_upgrade_account';

    /**
     * Array of all the supported formats
     */
    protected $SUPPORTED_FORMATS = array(
        'xml'               => 'application/xml',
        'json'              => 'application/json',
        'serialize'         => 'application/vnd.php.serialized',
        'php'               => 'text/plain',
        'csv'               => 'text/csv'
    );

    /**
     * Array of all the supported formats and their
     * auto-detect identifiers
     */
    protected $AUTO_DETECT_FORMATS = array(
        'application/xml'   => 'xml',
        'text/xml'          => 'xml',
        'application/json'  => 'json',
        'text/json'         => 'json',
        'text/csv'          => 'csv',
        'application/csv'   => 'csv',
        'application/vnd.php.serialized' => 'serialize'
    );

    /**
     * Curl library
     */
    private $curl = NULL;

    /**
     * Host parameters
     */
    private $host = NULL;
    private $port = NULL;

    /**
     * API key parameters
     */
    private $api_key_name = NULL;
    private $api_key = NULL;
    private $api_secret = NULL;

    /**
     * Format parameters
     */
    private $format = NULL;
    private $mime_type = NULL;

    /**
     * SSL parameters
     */
    private $ssl_verify_peer = NULL;
    private $ssl_cainfo = NULL;

    /**
     * Other parameters
     */
    private $send_cookies = NULL;
    private $response_string = NULL;

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                              ADMINISTRATIVE                                               **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Constructor
     */
    public function __construct($config = array())
    {

        // initialize the curl library
        $this->curl = new LassieCurl();

        // establish the connection
        $this->connect($config);
    }

    /**
     * Initialize the connection.
     */
    public function connect($config = array())
    {

        // initialize all the configurable variables
        $this->client_id = $this->get_from_array('id', $config);
        $this->type_id = $this->get_from_array('type_id', $config);
        $this->host = $this->get_from_array('host', $config);
        $this->port = $this->get_from_array('port', $config);
        $this->http_auth_type = $this->get_from_array('http_auth_type', $config, 'basic');
        $this->http_username = $this->get_from_array('http_username', $config);
        $this->http_password = $this->get_from_array('http_password', $config);
        $this->api_key = $this->get_from_array('api_key', $config);
        $this->api_secret = $this->get_from_array('api_secret', $config);
        $this->api_key_name = $this->get_from_array('api_key_name', $config, self::API_KEY_POST_NAME);
        $this->send_cookies = $this->get_from_array('send_cookies', $config);
        $this->ssl_verify_peer = $this->get_from_array('ssl_verify_peer', $config);
        $this->ssl_cainfo = $this->get_from_array('ssl_cainfo', $config);

        // format the host name with a trailing slash when needed
        if (substr($this->host, -1, 1) != '/') {
            $this->host .= '/';
        }

        // format the host to have http or https
        // SOURCE: http://stackoverflow.com/questions/6240414/add-http-prefix-to-url-when-missing
        if  (!$ret = parse_url($this->host, PHP_URL_SCHEME)) {
            $this->host = $this->get_from_array('protocol', $config, 'http') .'://'. $this->host;
        }

        // add the API controller when not added
        if (substr($this->host, -4, 1) != 'api/') {
            $this->host .= 'api/';
        }
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                REQUESTERS                                                 **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Get
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function get($uri, $params = array(), $format = NULL)
    {

        // extend the parameters
        $params = $this->extend_params($params);

        // check for any parameters
        if ($params) {
            $uri .= '?'.(is_array($params) ? http_build_query($params) : $params);
        }

        // perform get
        return $this->_call('get', $uri, NULL, $format);
    }

    /**
     * Post
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function post($uri, $params = array(), $format = NULL)
    {

        // variables
        $formatted_uri = str_replace('/', '', $uri);
        $params = $this->extend_params($params);

        // check for a post transaction
        if ($formatted_uri == self::POST_TRANSACTION_URI) {

            // variables
            $products = $this->get_from_array(self::TRANSACTION_PRODUCTS_POST_NAME, $params);

            // check if the products is not a string
            if (!is_string($products)) {
                $products = json_encode($products);
            }

            // calculate the hash
            $transaction_signature = $this->generate_transaction_signature($this->api_key, $this->api_secret, $products);

            // set the api signature
            $params[self::TRANSACTION_SIGNATURE_POST_NAME] = base64_encode($transaction_signature);
        }

        // check for a upgrade account
        if ($formatted_uri == self::POST_UPGRADE_ACCOUNT_URI) {

            // variables
            $transaction_account_name = $this->get_from_array(self::TRANSACTION_ACCOUNT_NAME_POST_NAME, $params);
            $transaction_account_id = $this->get_from_array(self::TRANSACTION_ACCOUNT_ID_POST_NAME, $params);
            $transaction_upgrade_delta_balance = $this->get_from_array(self::TRANSACTION_UPGRADE_DELTA_BALANCE_POST_NAME, $params);

            // construct the api hash content
            $signature_content = $transaction_account_name .':'. $transaction_account_id .':'. $transaction_upgrade_delta_balance;

            // calculate the hash
            $transaction_signature = $this->generate_transaction_signature($this->api_key, $this->api_secret, $signature_content);

            // set the api signature
            $params[self::TRANSACTION_SIGNATURE_POST_NAME] = base64_encode($transaction_signature);
        }

        // perform post
        return $this->_call('post', $uri, $params, $format);
    }

    /**
     * Put
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function put($uri, $params = array(), $format = NULL)
    {

        // extend the parameters
        $params = $this->extend_params($params);

        // perform put
        return $this->_call('put', $uri, $params, $format);
    }

    /**
     * Path
     *
     * @access  public
     * @author  Dmitry Serzhenko
     * @version 1.0
     */
    public function patch($uri, $params = array(), $format = NULL)
    {

        // extend the parameters
        $params = $this->extend_params($params);

        // perform patch
        return $this->_call('patch', $uri, $params, $format);
    }

    /**
     * Delete
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function delete($uri, $params = array(), $format = NULL)
    {

        // extend the parameters
        $params = $this->extend_params($params);

        // perform delete
        return $this->_call('delete', $uri, $params, $format);
    }

    /**
     * _call
     *
     * @access  protected
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _call($method, $uri, $params = array(), $format = NULL)
    {

        // always set the format to make sure it is reset
        // from the one of previous requests
        $this->set_format($format);

        // set the HTTP header
        $this->set_http_header('Accept', $this->mime_type);

        // initialize cURL session
        $this->curl->create($this->host . $uri);

        // if using ssl set the ssl verification value and cainfo
        // contributed by: https://github.com/paulyasi
        if ($this->ssl_verify_peer === FALSE) {

            // set SSL to false
            $this->curl->ssl(FALSE);
        } else if ($this->ssl_verify_peer === TRUE) {

            // set the SSL info
            $this->curl->ssl(TRUE, 2, getcwd() . $this->ssl_cainfo);
        }

        // if authentication is enabled use it
        if (!empty($this->http_username) && !empty($this->http_username)) {
            $this->curl->http_login($this->http_username, $this->http_password, $this->http_auth_type);
        }

        // if we have an API Key, then use it
        if (!empty($this->api_key)) {
            $this->curl->http_header($this->api_key_name, $this->api_key);
        }

        // send cookies with curl
        if (!empty($this->send_cookies)) {
            $this->curl->set_cookies($_COOKIE);
        }

        // set the Content-Type (contributed by https://github.com/eriklharper)
        $this->set_http_header('Content-type', $this->mime_type);

        // we still want the response even if there is an error code over 400
        $this->curl->option('failonerror', FALSE);

        // call the correct method with parameters
        $this->curl->{$method}($params);

        // execute and return the response from the REST server
        $response = $this->curl->execute();

        // format and return
        return $this->_format_response($response);
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                  HASHING                                                  **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Expand the parameters with the API key, API hash content and API hash
     */
    public function extend_params($params)
    {

        // variables
        $api_key = $this->api_key;
        $api_secret = $this->api_secret;
        $api_hash_content = md5(intval(microtime() + rand(0, 1000000)));

        // create the hash
        $api_hash = $this->generate_api_hash($api_key, $api_secret, $api_hash_content);

        // expand the parameters
        $params[self::API_KEY_POST_NAME] = $api_key;
        $params[self::API_HASH_CONTENT_POST_NAME] = $api_hash_content;
        $params[self::API_HASH_POST_NAME] = base64_encode($api_hash);

        return $params;
    }

    /**
     * Generate a API hash from the required parameters using the HMAC hashing method
     * SOURCE: http://websec.io/2013/02/14/API-Authentication-Public-Private-Hashes.html
     */
    public function generate_api_hash($api_key, $api_secret, $hash_content)
    {
        return hash_hmac('sha256', $api_key .':'. $hash_content, $api_secret);
    }

    /**
     * Generate a new API transaction signature
     */
    public function generate_transaction_signature($api_key, $api_secret, $hash_content)
    {
        return $this->generate_api_hash($api_key, $api_secret, $hash_content);
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                  SETTERS                                                  **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Set the accepted language as response
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function set_language($lang)
    {
        if (is_array($lang))
        {
            $lang = implode(', ', $lang);
        }

        $this->curl->http_header('Accept-Language', $lang);
    }

    /**
     * Set the header for the request
     *
     * @access  public
     * @author  David Genelid
     * @version 1.0
     */
    public function set_header($header)
    {
        $this->curl->http_header($header);
    }

    /**
     * Set format
     *
     * If a type is passed in that is not supported, use it as a mime type
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function set_format($format)
    {
        if (array_key_exists($format, $this->SUPPORTED_FORMATS)) {
            $this->format = $format;
            $this->mime_type = $this->SUPPORTED_FORMATS[$format];
        }

        else {
            $this->mime_type = $format;
        }

        return $this;
    }

    /**
     * Set an option
     *
     * Set custom CURL options
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function set_option($code, $value)
    {
        $this->curl->option($code, $value);
    }

    /**
     * Set the HTTP header
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function set_http_header($header, $content = NULL)
    {

        // did they use a single argument or two?
        $params = $content ? array($header, $content) : array($header);

        // pass these attributes on to the curl library
        call_user_func_array(array($this->curl, 'http_header'), $params);
    }

    /**
     * Get the current status
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    // Return HTTP status code
    public function get_status()
    {
        return $this->get_info('http_code');
    }

    /**
     * Get a specific piece of info from the cURL library
     *
     * Return curl info by specified key, or whole array
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    public function get_info($key = null)
    {
        return $key === null ? $this->curl->info : @$this->curl->info[$key];
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                FORMATTER                                                  **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * _format_response
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _format_response($response)
    {
        $this->response_string =& $response;

        // It is a supported format, so just run its formatting method
        if (array_key_exists($this->format, $this->SUPPORTED_FORMATS))
        {
            return $this->{"_".$this->format}($response);
        }

        // Find out what format the data was returned in
        $returned_mime = @$this->curl->info['content_type'];

        // If they sent through more than just mime, strip it off
        if (strpos($returned_mime, ';'))
        {
            list($returned_mime) = explode(';', $returned_mime);
        }

        $returned_mime = trim($returned_mime);

        if (array_key_exists($returned_mime, $this->AUTO_DETECT_FORMATS))
        {
            return $this->{'_'.$this->AUTO_DETECT_FORMATS[$returned_mime]}($response);
        }

        return $response;
    }

    /**
     * _xml
     *
     * Format XML for output
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _xml($string)
    {
        return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    /**
     * _csv
     *
     * Format HTML for output.  This function is DODGY! Not perfect CSV support but works
     * with my REST_Controller (https://github.com/philsturgeon/codeigniter-restserver)
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _csv($string)
    {
        $data = array();

        // Splits
        $rows = explode("\n", trim($string));
        $headings = explode(',', array_shift($rows));
        foreach( $rows as $row )
        {
            // The substr removes " from start and end
            $data_fields = explode('","', trim(substr($row, 1, -1)));

            if (count($data_fields) === count($headings))
            {
                $data[] = array_combine($headings, $data_fields);
            }

        }

        return $data;
    }

    /**
     * _json
     *
     * Encode as JSON
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _json($string)
    {
        return json_decode(trim($string));
    }

    /**
     * _serialize
     *
     * Encode as Serialized array
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _serialize($string)
    {
        return unserialize(trim($string));
    }

    /**
     * _php
     *
     * Encode raw PHP
     *
     * @access  public
     * @author  Phil Sturgeon
     * @version 1.0
     */
    protected function _php($string)
    {
        $string = trim($string);
        $populated = array();
        eval("\$populated = \"$string\";");
        return $populated;
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                 HELPERS                                                   **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Get an value from an array while checking the existance of the key.
     * Return FALSE if the key is not set.
     */
    public function get_from_array($key, $array, $default_value = FALSE)
    {

        // guard: check if we are dealing with an object
        if (is_object($array)) {

            // get the object result
            return isset($array->$key) ? $array->$key : $default_value;
        }

        // get the array result
        return isset($array[$key]) ? $array[$key] : $default_value;
    }

    /**-----------------------------------------------------------------------------------------------------------**/
    /**                                                 GETTERS                                                   **/
    /**-----------------------------------------------------------------------------------------------------------**/

    /**
     * Get the host name of the sync input or output
     */
    public function get_host()
    {
        return $this->host;
    }

    /**
     * Get the host name of the sync input or output
     */
    public function get_port()
    {
        return $this->port;
    }

    /**
     * Get the amount of milliseconds before a request times out
     */
    public function get_timeout()
    {
        return $this->timeout;
    }

    /**
     * Identify how this API should authenticate itself to the
     * external location
     */
    public function get_http_auth_type()
    {
        return $this->http_auth_type;
    }

    /**
     * Get the identifying username for the connection
     */
    public function get_http_username()
    {
        return $this->http_username;
    }

    /**
     * Get the identifying password or secret key
     */
    public function get_http_password()
    {
        return $this->http_password;
    }

    /**
     * Get the API key
     */
    public function get_api_key()
    {
        return $this->api_key;
    }

    /**
     * Get the API secret
     */
    public function get_api_secret()
    {
        return $this->api_secret;
    }

    /**
     * Get the API key post name
     */
    public function get_api_key_name()
    {
        return $this->api_key_name;
    }

    /**
     * Get the API send cookies flag
     */
    public function get_send_cookies()
    {
        return $this->send_cookies;
    }

    /**
     * Get whether SSL verification is on
     */
    public function get_ssl_verify_peer()
    {
        return $this->ssl_verify_peer;
    }

    /**
     * Get SSL verification information
     */
    public function get_ssl_cainfo()
    {
        return $this->ssl_cainfo;
    }
}

/* End of file LassieApi.php */

/**
 * CodeIgniter Curl Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-curl
 */
class LassieCurl {

    // variables
	protected $response = '';       // Contains the cURL response for debug
	protected $session;             // Contains the cURL handler for a session
	protected $url;                 // URL of the session
	protected $options = array();   // Populates curl_setopt_array
	protected $headers = array();   // Populates extra HTTP headers
	public $error_code;             // Error code returned as an int
	public $error_string;           // Error message returned as a string
	public $info;                   // Returned after request (elapsed time, etc)

	function __construct($url = '')
	{

		$url AND $this->create($url);
	}

	public function __call($method, $arguments)
	{
		if (in_array($method, array('simple_get', 'simple_post', 'simple_put', 'simple_delete', 'simple_patch')))
		{
			// Take off the "simple_" and past get/post/put/delete/patch to _simple_call
			$verb = str_replace('simple_', '', $method);
			array_unshift($arguments, $verb);
			return call_user_func_array(array($this, '_simple_call'), $arguments);
		}
	}

	/* =================================================================================
	 * SIMPLE METHODS
	 * Using these methods you can make a quick and easy cURL call with one line.
	 * ================================================================================= */

	public function _simple_call($method, $url, $params = array(), $options = array())
	{
		// Get acts differently, as it doesnt accept parameters in the same way
		if ($method === 'get')
		{
			// If a URL is provided, create new session
			$this->create($url.($params ? '?'.http_build_query($params, NULL, '&') : ''));
		}

		else
		{
			// If a URL is provided, create new session
			$this->create($url);

			$this->{$method}($params);
		}

		// Add in the specific options provided
		$this->options($options);

		return $this->execute();
	}

	public function simple_ftp_get($url, $file_path, $username = '', $password = '')
	{
		// If there is no ftp:// or any protocol entered, add ftp://
		if ( ! preg_match('!^(ftp|sftp)://! i', $url))
		{
			$url = 'ftp://' . $url;
		}

		// Use an FTP login
		if ($username != '')
		{
			$auth_string = $username;

			if ($password != '')
			{
				$auth_string .= ':' . $password;
			}

			// Add the user auth string after the protocol
			$url = str_replace('://', '://' . $auth_string . '@', $url);
		}

		// Add the filepath
		$url .= $file_path;

		$this->option(CURLOPT_BINARYTRANSFER, TRUE);
		$this->option(CURLOPT_VERBOSE, TRUE);

		return $this->execute();
	}

	/* =================================================================================
	 * ADVANCED METHODS
	 * Use these methods to build up more complex queries
	 * ================================================================================= */

	public function post($params = array(), $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&');
		}

		// Add in the specific options provided
		$this->options($options);

		$this->http_method('post');

		$this->option(CURLOPT_POST, TRUE);
		$this->option(CURLOPT_POSTFIELDS, $params);
	}

	public function put($params = array(), $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&');
		}

		// Add in the specific options provided
		$this->options($options);

		$this->http_method('put');
		$this->option(CURLOPT_POSTFIELDS, $params);

		// Override method, I think this overrides $_POST with PUT data but... we'll see eh?
		$this->option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
	}

	public function patch($params = array(), $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&');
		}

		// Add in the specific options provided
		$this->options($options);

		$this->http_method('patch');
		$this->option(CURLOPT_POSTFIELDS, $params);

		// Override method, I think this overrides $_POST with PATCH data but... we'll see eh?
		$this->option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PATCH'));
	}

	public function delete($params, $options = array())
	{
		// If its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&');
		}

		// Add in the specific options provided
		$this->options($options);

		$this->http_method('delete');

		$this->option(CURLOPT_POSTFIELDS, $params);
	}

	public function set_cookies($params = array())
	{
		if (is_array($params))
		{
			$params = http_build_query($params, NULL, '&');
		}

		$this->option(CURLOPT_COOKIE, $params);
		return $this;
	}

	public function http_header($header, $content = NULL)
	{
		$this->headers[] = $content ? $header . ': ' . $content : $header;
		return $this;
	}

	public function http_method($method)
	{
		$this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		return $this;
	}

	public function http_login($username = '', $password = '', $type = 'any')
	{
		$this->option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
		$this->option(CURLOPT_USERPWD, $username . ':' . $password);
		return $this;
	}

	public function proxy($url = '', $port = 80)
	{
		$this->option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
		$this->option(CURLOPT_PROXY, $url . ':' . $port);
		return $this;
	}

	public function proxy_login($username = '', $password = '')
	{
		$this->option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
		return $this;
	}

	public function ssl($verify_peer = TRUE, $verify_host = 2, $path_to_cert = NULL)
	{
		if ($verify_peer)
		{
			$this->option(CURLOPT_SSL_VERIFYPEER, TRUE);
			$this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
			if (isset($path_to_cert)) {
				$path_to_cert = realpath($path_to_cert);
				$this->option(CURLOPT_CAINFO, $path_to_cert);
			}
		}
		else
		{
			$this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		return $this;
	}

	public function options($options = array())
	{
		// Merge options in with the rest - done as array_merge() does not overwrite numeric keys
		foreach ($options as $option_code => $option_value)
		{
			$this->option($option_code, $option_value);
		}

		// Set all options provided
		curl_setopt_array($this->session, $this->options);

		return $this;
	}

	public function option($code, $value, $prefix = 'opt')
	{
		if (is_string($code) && !is_numeric($code))
		{
			$code = constant('CURL' . strtoupper($prefix) . '_' . strtoupper($code));
		}

		$this->options[$code] = $value;
		return $this;
	}

	// Start a session from a URL
	public function create($url)
	{
		$this->url = $url;
		$this->session = curl_init($this->url);

		return $this;
	}

	// End a session and return the results
	public function execute()
	{
		// Set two default options, and merge any extra ones in
		if ( ! isset($this->options[CURLOPT_TIMEOUT]))
		{
			$this->options[CURLOPT_TIMEOUT] = 30;
		}
		if ( ! isset($this->options[CURLOPT_RETURNTRANSFER]))
		{
			$this->options[CURLOPT_RETURNTRANSFER] = TRUE;
		}
		if ( ! isset($this->options[CURLOPT_FAILONERROR]))
		{
			$this->options[CURLOPT_FAILONERROR] = TRUE;
		}

		// Only set follow location if not running securely
		if ( ! ini_get('safe_mode') && ! ini_get('open_basedir'))
		{
			// Ok, follow location is not set already so lets set it to true
			if ( ! isset($this->options[CURLOPT_FOLLOWLOCATION]))
			{
				$this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
			}
		}

		if ( ! empty($this->headers))
		{
			$this->option(CURLOPT_HTTPHEADER, $this->headers);
		}

		$this->options();

		// Execute the request & and hide all output
		$this->response = curl_exec($this->session);
		$this->info = curl_getinfo($this->session);

		// Request failed
		if ($this->response === FALSE)
		{
			$errno = curl_errno($this->session);
			$error = curl_error($this->session);

			curl_close($this->session);
			$this->set_defaults();

			$this->error_code = $errno;
			$this->error_string = $error;

			return FALSE;
		}

		// Request successful
		else
		{
			curl_close($this->session);
			$this->last_response = $this->response;
			$this->set_defaults();
			return $this->last_response;
		}
	}

	public function is_enabled()
	{
		return function_exists('curl_init');
	}

	public function debug()
	{
		echo "=============================================<br/>\n";
		echo "<h2>CURL Test</h2>\n";
		echo "=============================================<br/>\n";
		echo "<h3>Response</h3>\n";
		echo "<code>" . nl2br(htmlentities($this->last_response)) . "</code><br/>\n\n";

		if ($this->error_string)
		{
			echo "=============================================<br/>\n";
			echo "<h3>Errors</h3>";
			echo "<strong>Code:</strong> " . $this->error_code . "<br/>\n";
			echo "<strong>Message:</strong> " . $this->error_string . "<br/>\n";
		}

		echo "=============================================<br/>\n";
		echo "<h3>Info</h3>";
		echo "<pre>";
		print_r($this->info);
		echo "</pre>";
	}

	public function debug_request()
	{
		return array(
			'url' => $this->url
		);
	}

	public function set_defaults()
	{
		$this->response = '';
		$this->headers = array();
		$this->options = array();
		$this->error_code = NULL;
		$this->error_string = '';
		$this->session = NULL;
	}

}

/* End of file Curl.php */
