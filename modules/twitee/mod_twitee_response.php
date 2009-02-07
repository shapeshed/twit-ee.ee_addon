<?php
/**
 * ExpressionEngine
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * 
 * Module Class File for Twit-ee module
 *
 * Fetches data from Twitter for display in templates
 *
 * @version    0.1
 * @author     George Ornbo <george@shapeshed.com>
 * @license    http://opensource.org/licenses/bsd-license.php
 */
 
/**
 * Twit-ee module
 *
 * @category   Modules
 * @package    Twit-ee
 */

/**
 * @see Arc90_Service_Twitter_Exception
 */
//require_once('Arc90/Service/Twitter/Exception.php');

/**
 * Arc90_Service_Twitter_Response represents a response to a {@link http://twitter.com Twitter} API call.
 *
 * @package    Arc90_Service
 * @subpackage Twitter
 * @author     Matt Williams <matt@mattwilliamsnyc.com>
 * @copyright  Copyright (c) 2008 {@link http://arc90.com Arc90 Inc.}
 * @license    http://opensource.org/licenses/bsd-license.php
 */
class Twitee_response
{
    /**
     * Metadata related to the HTTP response collected by cURL
     * @var array
     */
    public $_metadata = array();

    /**
     * Response body (if any) returned by Twitter
     * @var string
     */
    public $_data     = '';

    /**
     * Data type of the response body
     * @var string
     */
    protected $_format   = '';

    /**
     * Creates a new twitee_response instance.
     *
     * @param array  $metadata HTTP response {@link http://us3.php.net/curl_getinfo curl_getinfo() metadata}
     * @param string $data     Response body (if any) returned by Twitter
     * @param string $format   Data type of the response body (JSON, XML, RSS, ATOM, none)
     */
    public function __construct($data, array $metadata, $format)
    {
        $this->_data     = $data;
        $this->_metadata = $metadata;
        $this->_format   = $format;
    }

    /**
     * Overloads retrieval of object properties to allow read-only access.
     *
     * @param  string $name Name of the property to be accessed
     * @return mixed
     */
    public function __get($name)
    {
        if('data' == $name)
        {
            return $this->_data;
        }

        if(isset($this->_metadata[$name]))
        {
            return $this->_metadata[$name];
        }

        // throw new twitee_response(
        //     "Response property '{$name}' does not exist!"
        // );
    }

    /**
     * Overloads checking for existence of object properties to allow read-only access.
     *
     * @param  string  $name Name of the property to be accessed
     * @return boolean
     */
    public function __isset($name)
    {
        if('data' == $name)
        {
            return isset($this->_data);
        }

        return isset($this->_metadata[$name]);
    }

    /**
     * Returns the content body (if any) returned by Twitter.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Checks the HTTP status code of the response for 4xx or 5xx class errors.
     *
     * @return boolean
     */
    public function isError()
    {
        $type = floor($this->_metadata['http_code'] / 100);
		exit;
        return 4 == $type || 5 == $type;
    }

    /**
     * Does this response contain JSON data?
     *
     * @return boolean
     */
    public function isJson()
    {
        return 'json' == $this->_format;
    }

    /**
     * Does this response contain XML data?
     *
     * @return bool
     */
    public function isXml()
    {
        return 'xml' == $this->_format;
    }

    /**
     * Returns response data (and metadata) as an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        $array         = $this->_metadata;
        $array['data'] = $this->_data;

        return $array;
    }
}
