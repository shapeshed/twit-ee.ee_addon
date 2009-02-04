<?php
/**
 * ExpressionEngine - Branch test
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * 
 * Plugin File for Twittee plugin
 *
 * Fetches data from Twitter for display in templates
 *
 * This file must be placed in the
 * /system/plugins/ folder in your ExpressionEngine installation.
 * 
 * @version    0.0.4
 * @author     George Ornbo <george@shapeshed.com>
 * @license    http://opensource.org/licenses/bsd-license.php
 */
 
/**
* Plugin information used by ExpressionEngine
* @global array $plugin_info
*/
$plugin_info = array(
            'pi_name'			=> 'Twit-ee',
            'pi_version'		=> '1.0.0',
            'pi_author'			=> 'George Ornbo',
            'pi_author_url'		=> 'http://shapeshed.com/',
            'pi_description'	=> 'Fetches data from Twitter for display in templates',
            'pi_usage'			=> Twitee::usage()
          );

/**
 * Twit-ee Plugin
 *
 * @category   Plugins
 * @package    Twitee
 */
class Twitee{
	
	var $format = "xml";
	var $return_data = "";
	var $cache_time = "30";
		
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

	const CACHE_PATH			= 	'/reshape/cache/twitter_cache/';
	
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
	    $this->setAuth('shapeshed', 'tufnell5');
	}
	
	/**
	* Set client username and password.
	*
	* @param  string $username Twitter account username
	* @param  string $password Twitter account password
	* @return Arc90_Service_Twitter Provides a fluent interface.
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
	* @return string
	*/
	public function public_timeline() 
	{		
		return $this->getData('public_timeline', self::PATH_STATUS_PUBLIC,  false, 'status');
	}
	
	/**
	* Returns Twitter Friends Timeline
	*
	* @return string
	*/
	public function friends_timeline() 
	{   
		return $this->getData('friends_timeline', self::PATH_STATUS_FRIENDS, true, 'status');	
	}
	
	/**
	* Returns Twitter User Timeline
	*
	* @return string
	*/
	public function user_timeline() 
	{
		return $this->getData('user_timeline', self::PATH_STATUS_USER, true, 'status');		
	}
	
	/**
	* Returns Twitter Replies for the authenticated user
	*
	* @return string
	*/
	function replies() 
	{
		return $this->getData('friends_timeline', self::PATH_STATUS_REPLIES, true, 'status');	
	}
	
	/**
	* Returns Twitter Friends for the authenticated user
	*
	* @return string
	*/
	public function friends() 
	{		
		return $this->getData('friends', self::PATH_USER_FRIENDS, true, 'basic_user');
	}
	
	/**
	* Returns Twitter Followers for the authenticated user
	*
	* @return string
	*/
	public function followers() 
	{		
		return $this->getData('followers', self::PATH_USER_FOLLOWERS, true, 'basic_user');
	}
	
	/**
	* Returns Twitter Favorites for the authenticated user
	*
	* @return string
	*/
	public function favorites() 
	{		
		return $this->getData('favorites', self::PATH_FAV_FAVORITES, true, 'status');
	}
	
	/**
	* Sends request and handles response
	*
	* @return string
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
	* @return string
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
	
	function parse_status($xml)
	{
		global $TMPL;
		
		$limit = ( ! $TMPL->fetch_param('limit')) ? '10' : $TMPL->fetch_param('limit');		
		$count = 0;
		
		foreach ($xml->status as $status)
		{
			
			if($count == $limit)
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
	
	function parse_basic_user($xml)
	{
		global $TMPL;
		
		$limit = ( ! $TMPL->fetch_param('limit')) ? '10' : $TMPL->fetch_param('limit');		
		$count = 0;
		
		foreach ($xml->user as $user)		
		{
			
			if($count == $limit)
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
	* @return string
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
	* @return bool
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
	* @return bool
	*/	
	function checkCache($filename)
	{

		$last_modified = filemtime($_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH . $filename .'.'. $this->format); 
		
		if (time() - $this->cache_time > $last_modified)
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
	* @return bool
	*/	
	function getCache($filename)
	{
		
		$cache_path = $_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH . $filename .'.'. $this->format;

		return $cache_path;
		
	}
	
	/**
	* Updates the cache
	*
	* @return null
	*/
	function updateCache($data, $filename)	
	{	
		$cache_dir = $_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH;
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

function usage()
{
ob_start(); 
?>


<?php
$buffer = ob_get_contents();

ob_end_clean(); 

return $buffer;
}
/* END */
	
}

?>