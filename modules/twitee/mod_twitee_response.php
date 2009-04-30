<?php
/**
 * 
 * Twit-ee Response Class for Twit-ee
 *
 * Pushes the Twitter response to an object
 * 
 * This class is derived from {@link http://code.google.com/p/arc90-service-twitter/ Arc90_Service_Twitter} 
 *
 * @version    1.1
 * @author     George Ornbo <george@shapeshed.com>
 * @license    {@link http://opensource.org/licenses/bsd-license.php BSD License}
 */
 
/**
 * Twit-ee module
 *
 * @category   Modules
 * @package    Twit-ee
 */

/**
 * Twitee_response represents a response to a {@link http://twitter.com Twitter} API call.
 *
 * @category   Modules
 * @package    Twit-ee
 */
class Twitee_response extends Twitee
{
    /**
     * Metadata related to the HTTP response collected by cURL
     * @var array
     */
    protected $_metadata = array();

    /**
     * Response body (if any) returned by Twitter
     * @var string
     */
    protected $_data     = '';

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
}
?>