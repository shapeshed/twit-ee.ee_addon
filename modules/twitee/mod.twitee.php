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
class Twitee{
				
	const API_URL             = 'http://twitter.com';
	
	// API URLs
	const PATH_STATUS_PUBLIC	= 	'/statuses/public_timeline';
	const PATH_STATUS_FRIENDS	= 	'/statuses/friends_timeline';
	const PATH_STATUS_USER		= 	'/statuses/user_timeline';
	const PATH_STATUS_SHOW		= 	'/statuses/show';
	const PATH_STATUS_UPDATE	= 	'/statuses/update';
	const PATH_STATUS_REPLIES	= 	'/statuses/replies';
	const PATH_STATUS_DESTROY	= 	'/statuses/destroy';

	const PATH_USER_FRIENDS		= 	'/statuses/friends';
	const PATH_USER_FOLLOWERS	= 	'/statuses/followers';
	const PATH_USER_FEATURED	= 	'/statuses/featured';
	const PATH_USER_SHOW		= 	'/users/show';

	const PATH_DM_MESSAGES		= 	'/direct_messages';
	const PATH_DM_SENT			= 	'/direct_messages/sent';
	const PATH_DM_NEW			= 	'/direct_messages/new';
	const PATH_DM_DESTROY		= 	'/direct_messages/destroy';

	const PATH_FRIEND_CREATE	= 	'/friendships/create';
	const PATH_FRIEND_DESTROY	= 	'/friendships/destroy';
	const PATH_FRIEND_EXISTS	= 	'/friendships/exists';

	const PATH_ACCT_VERIFY		= 	'/account/verify_credentials';
	const PATH_ACCT_END_SESS	= 	'/account/end_session';
	const PATH_ACCT_ARCHIVE		= 	'/account/archive';
	const PATH_ACCT_LOCATION	= 	'/account/update_location';
	const PATH_ACCT_DEVICE		= 	'/account/update_delivery_device';
	const PATH_ACCT_STATUS		= 	'/account/rate_limit_status';

	const PATH_FAV_FAVORITES	= 	'/favorites';
	const PATH_FAV_CREATE		= 	'/favorites/create';
	const PATH_FAV_DESTROY		= 	'/favorites/destroy';

	const PATH_NOTIF_FOLLOW		= 	'/notifications/follow';
	const PATH_NOTIF_LEAVE		= 	'/notifications/leave';

	const PATH_BLOCK_CREATE		= 	'/blocks/create';
	const PATH_BLOCK_DESTROY	= 	'/blocks/destroy';

	const PATH_HELP_TEST		= 	'/help/test';
	const PATH_HELP_DOWNTIME	= 	'/help/downtime_schedule';
	
	
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
	* The cache folder to be used for caching responses. Set using $PREF->ini variable in __construct
	* @var string
	* @var string
	*/
	var $cache_folder = "";
	
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
	* Constructs a new Twitter Web Service Client.
	*
	* @param  string $username Twitter account username
	* @param  string $password Twitter account password
	*/
	public function __construct($username =null, $password =null)
	{
		global $DB, $LANG, $OUT, $TMPL, $PREFS;
		
		$this->cache_path	= 	$PREFS->ini('system_folder', TRUE). 'cache/twitter_cache/';
		
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
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function public_timeline() 
	{		
		return $this->getData('public_timeline', self::PATH_STATUS_PUBLIC,  false, 'status');
	}
	
	/**
	* Returns Twitter Friends Timeline
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function friends_timeline() 
	{   
		return $this->getData('friends_timeline', self::PATH_STATUS_FRIENDS, true, 'status');	
	}
	
	/**
	* Returns Twitter User Timeline
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function user_timeline() 
	{
		return $this->getData('user_timeline', self::PATH_STATUS_USER, true, 'status');		
	}
	
	/**
	* Returns Twitter Replies for the authenticated user
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	function replies() 
	{
		return $this->getData('friends_timeline', self::PATH_STATUS_REPLIES, true, 'status');	
	}
	
	/**
	* Returns Twitter Friends for the authenticated user
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function friends() 
	{		
		return $this->getData('friends', self::PATH_USER_FRIENDS, true, 'basic_user');
	}
	
	/**
	* Returns Twitter Followers for the authenticated user
	*
	* @see getData
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	public function followers() 
	{		
		return $this->getData('followers', self::PATH_USER_FOLLOWERS, true, 'basic_user');
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
	* The heavy lifting part of the class. Checks if the cache is stale by calling checkCache(), makes the request if necessary 
	* by calling makeRequest(), get the cache if not by calling getCache() and returns parsed xml by calling output().
	*
	* @param string $filename The filename for the request. Also used for reading / writing cache
	* @param string $url The twitter URL. Used to build cURL request to Twitter API
	* @param bool $auth Sets whether the request should use authorisation or not.
	* @param string $xml_parser Sets which parser should be used for the returned xml
	* @see function checkCache()
	* @see function makeRequest()
	* @see function getCache()
	* @see function output()
	* @return string Returns parsed data from Twitter API ready for display in templates
	*/
	function getData($filename, $url, $auth, $xml_parser)
	{			
		if ($this->checkCache($filename))
		{    
			$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, $auth, $data, $filename));
		}
				
		else
		{
			$xml = simplexml_load_file($this->getCache($filename));
		}
			
		return $this->output($xml, $xml_parser);	    
	}
	
	/**
	* Call the correct parser and returns results
	*
	* @param string $xml the xml to be parsed
	* @param string $type Sets the type of parser to be used for the xml
	* @see function parse_status()
	* @see function parse_basic_user
	* @return string Returns parsed xml
	*/	
	function output($xml, $type) 
    {
		switch ($type) 
		{
			case "status":
				return $this->parse_status($xml);
			break; 
			
			case "basic_user":
				return $this->parse_basic_user($xml);
			break;			
		}
					
	}
	
	/**
	* Parses XML and returns as ExpressionEngine variables.
	*
	* @param string $xml the xml to be parsed
	* @return string Returns parsed xml
	*/	
	function parse_status($xml)
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
	* Parses XML and returns as ExpressionEngine variables.
	*
	* @param string $xml the xml to be parsed
	* @return string Returns parsed xml
	*/
	function parse_basic_user($xml)
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
	* @param string $filename The filename for the request. Also ued for reading / writing cache
	* @see function updateCache()
	* @return string Returns raw data from the Twitter API request
	*/
	function makeRequest($url, $format = 'xml', $auth = false, $data = '', $filename)
	  {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL . $url .'.'. $format);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($auth)
		{
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->_authUsername}:{$this->_authPassword}");
		}
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		$this->updateCache($data, $filename);
		
		return $data;
	  }
	/**
	* Checks whether user has not reached limit of API calls
	*
	* @see makeRequest()
	* @return bool Returns TRUE if user has not exceeded API call limit
	*/	
	function checkRate()
	{
		$url = self::PATH_ACCT_STATUS; 
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));	
		if ($xml->{'remaining-hits'} != 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* Checks whether the cache is stale
	*
	* @param string $filename The filename of the cache file
	* @return bool Returns TRUE if cache is stale
	*/	
	function checkCache($filename)
	{

		$last_modified = filemtime($_SERVER['DOCUMENT_ROOT'] . $this->cache_path . $filename .'.'. $this->format); 
		
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
	* Returns path to cache file
	*
	* @param string $filename The filename of the cache file
	* @return string Returns path to cache file
	*/	
	function getCache($filename)
	{
		
		$full_cache_path = $_SERVER['DOCUMENT_ROOT'] . $this->cache_path . $filename .'.'. $this->format;
		
		return $full_cache_path;
		
	}
	
	/**
	* Updates the cache
	*
	* @param string $data The data to be written to the cache
	* @param string $filename The filename for the cache file
	* @return null
	*/
	function updateCache($data, $filename)	
	{	
		$cache_dir = $_SERVER['DOCUMENT_ROOT'] . $this->cache_path;
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