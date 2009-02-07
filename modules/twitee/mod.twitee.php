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
 * @see twitee_response
 */
require_once PATH_MOD.'twitee/mod_twitee_response.php';
 
/**
 * Twit-ee module
 *
 * @category   Modules
 * @package    Twit-ee
 */
class Twitee{
				
	const API_URL				= 	'http://twitter.com';

	const PATH_STATUS_PUBLIC	= 	'/statuses/public_timeline';
	const PATH_STATUS_FRIENDS	= 	'/statuses/friends_timeline';
	const PATH_STATUS_USER		= 	'/statuses/user_timeline';
	const PATH_STATUS_REPLIES	= 	'/statuses/replies';
	const PATH_USER_FRIENDS		= 	'/statuses/friends';
	const PATH_USER_FOLLOWERS	= 	'/statuses/followers';
	const PATH_FAV_FAVORITES	= 	'/favorites';	
	
	/**
	* Data sent back to calling function
	* @var string
	*/	
	var $return_data = "";

	/**
	* Sets how long data is cached for. Set to a default of 5 mins in __construct
	* @see __construct
	* @var string
	*/	
	var $refresh = "";
	
	/**
	* Sets the limit on how many results are displayed. Set to a default of 10 in __construct
	* @see __construct
	* @var string
	*/	
	var $limit = "";
	
	/**
	* Sets the data format of the response
	* @var string
	*/
	var $format = "xml";
	
	/**
	* The cache folder to be used for caching responses.
	* @var string
	* @var string
	*/
	var $cache_folder = "twitter_cache/";
	
	/**
	 * Twitter account username.
	 * @var string
	 */
	protected $_authUsername ='';
	
	/**
	 * Twitter account password.
	 * @var string
	 */
	protected $_authPassword ='';
	
    /**
     * Response to the most recent API call.
     * @var string
     */
    protected $_lastResponse =null;
	
	/**
	* Constructs a new Twitter Web Service Client and gets template variables
	*
	* @param  string $username Twitter account username
	* @param  string $password Twitter account password
	*/
	public function __construct($username =null, $password =null)
	{
		global $DB, $LANG, $OUT, $TMPL;
		
		$this->refresh = ( ! $TMPL->fetch_param('refresh')) ? '300' : $TMPL->fetch_param('refresh') * 60;
		
		$this->limit = ( ! $TMPL->fetch_param('limit')) ? '10' : $TMPL->fetch_param('limit');
		
		$query = $DB->query("SELECT * FROM exp_twitee LIMIT 0,1");

		if ($query->num_rows != 0)
		{		
	    	$this->setAuth($query->result[0]['username'], str_rot13($query->result[0]['password']));
		}	
		else
		{		
			return $OUT->show_user_error('general', array("You don't seem to have entered a twitter username and password. You can do this in the module settings."));
		}

	}
	
	/**
	* Set client username and password.
	*
	* @param  string $username Twitter account username
	* @param  string $password Twitter account password
	* @return array Adds username and password to the $this array
	*/
	public function setAuth($username, $password)
	{		
		$this->_authUsername = $username;
		$this->_authPassword = $password;
		return $this;		
	}
		
	/**
	* Returns Twitter Public Timeline
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function public_timeline() 
	{				
		return $this->_get_data("public_timeline", self::PATH_STATUS_PUBLIC, false, "status" );		
	}
	
	/**
	* Returns Twitter Friends Timeline
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function friends_timeline() 
	{   
		return $this->_get_data("friends_timeline", self::PATH_STATUS_FRIENDS, true, "status" );
	}
	
	/**
	* Returns Twitter User Timeline
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function user_timeline() 
	{
		return $this->_get_data("friends_timeline", self::PATH_STATUS_USER, true, "status" );	
	}
	
	/**
	* Returns Twitter Replies for the authenticated user
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	function replies() 
	{
		return $this->_getData('friends_timeline', self::PATH_STATUS_REPLIES, true, 'status');
	}
	
	/**
	* Returns Twitter Friends for the authenticated user
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function friends() 
	{		
		return $this->_getData('friends', self::PATH_USER_FRIENDS, true, 'basic_user');
	}
	
	/**
	* Returns Twitter Followers for the authenticated user
	*
	* @see _getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function followers() 
	{		
		return $this->_getData('followers', self::PATH_USER_FOLLOWERS, true, 'basic_user');
	}
	
	/**
	* Returns Twitter Favorites for the authenticated user
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function favorites() 
	{		
		return $this->getData('favorites', self::PATH_FAV_FAVORITES, true, 'status');
	}
	
	/**
	* The heavy lifting - checks cache via _checkCache, makes request to API if necessary via _makeRequest
	* updates cache if necessary via _updateCache and then parses data with the correct parser. 
	*
	* @param string $filename the name of the cache file
	* @param string $path the Twitter API path for this call
	* @param bool $auth Whether this API request needs authorisation. If yes = TRUE. 
	* @param string $parser Which parser should be used for the response
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/	
	protected function _get_data($filename, $path, $auth, $parser)
	{
		if (!$this->_checkCache($filename))
		{
			$cache_file = PATH_CACHE . $this->cache_folder . $filename .'.'. $this->format;
			$xml = simplexml_load_file($cache_file);				
		}	

		else
		{
			$response = $this->_makeRequest($path, $this->format, $auth);	

			if ($response->_metadata['http_code'] == 200)
			{
				$this->_updateCache($response->_data, $filename);
				$xml = new SimpleXMLElement($response->_data);					
			}
		}
		switch($parser) 
		{
			case 'status':
			return $this->_parse_status($xml);	
			break;
			case 'basic_user':
			return $this->_parse_basic_user($xml);	
			break;
		}
		
	}	
	
	
		
	/**
	* Parses XML and returns as ExpressionEngine variables for the status XML schema
	*
	* @param string $xml the xml to be parsed
	* @return string Returns parsed xml
	*/	
	protected function _parse_status($xml)
	{
		global $TMPL;
				
		$count = 0;
		
		foreach ($xml->status as $status)
		{
			
			if($count == $this->limit)
			{
				break;
			}			
			
			$tagdata = $TMPL->tagdata;
			
			foreach ($TMPL->var_single as $key => $val)
			{
				if (isset($status->$val))
				{
				$tagdata = $TMPL->swap_var_single($val, $status->$val, $tagdata);
				}
			}
				
			foreach ($status->user as $user)
			{
				foreach ($TMPL->var_single as $key => $val)
				{
					if (isset($user->$val))
					{
					$tagdata = $TMPL->swap_var_single($val, $user->$val, $tagdata);
					}
				}
				
			}
			
		$this->return_data .= $tagdata;
		$count++;
		
		}
		
		return $this->return_data;	
		
	}
	
	/**
	* Parses XML and returns as ExpressionEngine variables for the Basic User xml schema
	*
	* @param string $xml the xml to be parsed
	* @return string Returns parsed xml
	*/
	protected function _parse_basic_user($xml)
	{
		global $TMPL;
	
		$count = 0;
		
		foreach ($xml->user as $user)		
		{
			
			if($count == $this->limit)
			{
				break;
			}
			
			$tagdata = $TMPL->tagdata;
			
			foreach ($TMPL->var_single as $key => $val)
			{
				if (isset($user->$val))
				{
				$tagdata = $TMPL->swap_var_single($val, $user->$val, $tagdata);
				}
			}
				
			foreach ($user->status as $status)
			{
				foreach ($TMPL->var_single as $key => $val)
				{
					if (isset($status->$val))
					{
					$tagdata = $TMPL->swap_var_single($val, $status->$val, $tagdata);
					}
				}
				
			}
			
		$this->return_data .= $tagdata;
		
		}
		
		return $this->return_data;	
		$count++;
		
	}
	
	/**
	* Gets data via a cURL request
	*
	* @param string $url The twitter URL. Used to build cURL request to Twitter API
	* @param string $format The format of the outputed data e.g. xml, json, atom, rss
	* @param bool $auth Sets whether the request should use authorisation or not.
	* @param string $data Variable to contain returned data
	* @see function updateCache()
	* @return string Returns raw data from the Twitter API request
	*/
    protected function _makeRequest($url, $format = 'xml', $auth = false, $data = '')
	  {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL . $url .'.'. $format);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($auth)
		{
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->_authUsername}:{$this->_authPassword}");
		}
		
		$data = curl_exec($ch);
		$metadata = curl_getinfo($ch);
		curl_close($ch);
	
		return $this->_lastResponse = new Twitee_response($data, $metadata, $format);			
	  }
	
	/**
	* Checks whether the cache is stale
	*
	* @param string $filename The filename of the cache file
	* @return bool Returns TRUE if cache is stale
	*/	
	protected function _checkCache($filename)
	{
		$last_modified = filemtime(PATH_CACHE . $this->cache_folder . $filename .'.'. $this->format); 
		
		if (time() - $this->refresh > $last_modified)
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
		
	/**
	* Updates the cache
	*
	* Fails silently if it is not able to create cache folder/file or if these are unwritable
	*
	* @param string $data The data to be written to the cache
	* @param string $filename The filename for the cache file
	* @return null
	*/
	protected function _updateCache($data, $filename)	
	{		
		$cache_dir  = PATH_CACHE. $this->cache_folder;				
		$cache_file = $cache_dir . $filename .'.'. $this->format;
						
		if ( ! @is_dir($cache_dir))
		{
			if ( ! @mkdir($cache_dir, 0777))
			{
				return FALSE;
			}
			@chmod($cache_dir, 0777);            
		}	
		
		if ( ! $fp = @fopen($cache_file, 'wb'))
		{
			return FALSE;
		}
		
		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);
		@chmod($cache_file, 0777);
		
		return;		
	}
		
}

?>