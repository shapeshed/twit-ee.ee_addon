<?php
/**
 * ExpressionEngine
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * Plugin File for Twittee plugin
 *
 * Fetches data from Twitter for display in templates
 *
 * This file must be placed in the
 * /system/plugins/ folder in your ExpressionEngine installation.
 * 
 * @version    0.0.3
 * @author     George Ornbo <george@shapeshed.com>
 * @license    http://opensource.org/licenses/bsd-license.php
 */
 
/**
* Plugin information used by ExpressionEngine
* @global array $plugin_info
*/
$plugin_info = array(
            'pi_name'			=> 'Twittee',
            'pi_version'		=> '1.0.0',
            'pi_author'			=> 'George Ornbo',
            'pi_author_url'		=> 'http://shapeshed.com/',
            'pi_description'	=> 'Fetches data from Twitter for display in templates',
            'pi_usage'			=> Twittee::usage()
          );

/**
 * Twittee Plugin
 *
 * @category   Plugins
 * @package    Twitee
 */
class Twittee{
	
	var $format = "xml";
	var $return_data = "";
	var $cache_time = "300";
		
    const API_URL             = 'http://twitter.com';
 
    // API URLs
    const PATH_STATUS_PUBLIC  = '/statuses/public_timeline';
    const PATH_STATUS_FRIENDS = '/statuses/friends_timeline';
    const PATH_STATUS_USER    = '/statuses/user_timeline';
    const PATH_STATUS_SHOW    = '/statuses/show';
    const PATH_STATUS_UPDATE  = '/statuses/update';
    const PATH_STATUS_REPLIES = '/statuses/replies';
    const PATH_STATUS_DESTROY = '/statuses/destroy';
 
    const PATH_USER_FRIENDS   = '/statuses/friends';
    const PATH_USER_FOLLOWERS = '/statuses/followers';
    const PATH_USER_FEATURED  = '/statuses/featured';
    const PATH_USER_SHOW      = '/users/show';
 
    const PATH_DM_MESSAGES    = '/direct_messages';
    const PATH_DM_SENT        = '/direct_messages/sent';
    const PATH_DM_NEW         = '/direct_messages/new';
    const PATH_DM_DESTROY     = '/direct_messages/destroy';
 
    const PATH_FRIEND_CREATE  = '/friendships/create';
    const PATH_FRIEND_DESTROY = '/friendships/destroy';
    const PATH_FRIEND_EXISTS  = '/friendships/exists';
 
    const PATH_ACCT_VERIFY    = '/account/verify_credentials';
    const PATH_ACCT_END_SESS  = '/account/end_session';
    const PATH_ACCT_ARCHIVE   = '/account/archive';
    const PATH_ACCT_LOCATION  = '/account/update_location';
    const PATH_ACCT_DEVICE    = '/account/update_delivery_device';
 
    const PATH_FAV_FAVORITES  = '/favorites';
    const PATH_FAV_CREATE     = '/favorites/create';
    const PATH_FAV_DESTROY    = '/favorites/destroy';
 
    const PATH_NOTIF_FOLLOW   = '/notifications/follow';
    const PATH_NOTIF_LEAVE    = '/notifications/leave';
 
    const PATH_BLOCK_CREATE   = '/blocks/create';
    const PATH_BLOCK_DESTROY  = '/blocks/destroy';
 
    const PATH_HELP_TEST      = '/help/test';
    const PATH_HELP_DOWNTIME  = '/help/downtime_schedule';

	const CACHE_PATH          = '/reshape/cache/twitter_cache/';
	

	function Twittee() 
    {
										
	}

    /**
     * Returns Twitter Public Timeline
     *
     * @return string
     */
	function public_timeline() 
	{	
		$filename = "public_timeline";
			
		if ($this->checkCache($filename))
		{
			$url = self::PATH_STATUS_PUBLIC;        
			$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, false, $filename));
		}
		else
		{
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH . $filename .'.'. $this->format)) 
			{
			    $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH . $filename .'.'. $this->format);
			} 

		}
		return $this->output($xml);
	}

	function friends_timeline() 
	{          
		$url = self::PATH_STATUS_FRIENDS;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function user_timeline() 
	{
		$url = self::PATH_STATUS_USER;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function replies() 
	{
		$url = self::PATH_STATUS_REPLIES;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function show_status() 
	{
		$url = self::PATH_USER_SHOW;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function friends() 
	{
		$url = self::PATH_USER_FRIENDS;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function followers() 
	{
		$url = self::PATH_USER_FOLLOWERS;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}

	function favorites() 
	{
		$url = self::PATH_FAV_FAVORITES;    
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		return $this->output($xml);
	}
	
    /**
     * Parses XML and returns ExpressionEngine variables
     *
     * @return string
     */	
	function output($xml) 
    {
		global $TMPL;
		
		// Loop through XML file using SimpleXML
		foreach ($xml->status as $status)
		{
			$tagdata = $TMPL->tagdata;
			
			// Push status variables to EE variables
			foreach ($TMPL->var_single as $key => $val)
				{
					if (isset($status->$val))
					{
					$tagdata = $TMPL->swap_var_single($val, $status->$val, $tagdata);
					}
				}
			
			// Descend to user and push user variables to EE variables				
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
				
		}	
		
		return $this->return_data;				
					
	}

    /**
     * Gets data via a cURL request
     *
     * @return string
     */	
	function makeRequest($url, $format = 'xml', $auth = false, $filename, $data = '')
	  {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, self::API_URL . $url .'.'. $format);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	    if($auth)
	    {
	      curl_setopt($ch, CURLOPT_USERPWD, $this->username .':'. $this->password);
	    }

	    $data = curl_exec($ch);
	
	    curl_close($ch);

		$this->updateCache($data, $filename);
		
	    return $data;
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
     * Updates the cache
     *
     * @return null
     */
	function updateCache($data, $filename)	
	{

		$cache_path = $_SERVER['DOCUMENT_ROOT'] . self::CACHE_PATH . $filename .'.'. $this->format;

		if (is_writable($cache_path)) {
			
			// Open file and make it writable
		    if (!$handle = fopen($cache_path, 'w+')) {
		       // file is not writable 
		    }
		
			// Write the status to the cache
		    if (fwrite($handle, $data) === FALSE) {
		        exit;
		    }
		
			//All done so close
		    fclose($handle);
		
		}
		
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