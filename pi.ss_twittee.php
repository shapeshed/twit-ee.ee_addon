<?php
/**
* Plugin File for SS Twittee plugin
*
* Fetches data from Twitter for display in templates
*
* This file must be placed in the
* /system/plugins/ folder in your ExpressionEngine installation.
* 
* @version 1.0.0
* @author George Ornbo <http://shapeshed.com/>
* @license {@link http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported} All source code commenting and attribution must not be removed. This is a condition of the attribution clause of the license.
*/

/**
* Plugin information used by ExpressionEngine
* @global array $plugin_info
*/
$plugin_info = array(
						'pi_name'			=> 'SS Twittee',
						'pi_version'		=> '1.0.0',
						'pi_author'			=> 'George Ornbo, Shape Shed',
						'pi_author_url'		=> 'http://shapeshed.com/',
						'pi_description'	=> 'Fetches data from Twitter for display in templates',
						'pi_usage'			=> Ss_twittee::usage()
					);

class Ss_twittee{

	/**
	* Settings for class
	* @var array
	*/

	var $username = "";
	var $password = "";
	var $format = "xml";

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

	/**
	* Returned string
	* @var array
	*/
    var $return_data;


	function public_timeline() 
	{
		$url = self::PATH_STATUS_PUBLIC;				
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);		
	}

	function friends_timeline() 
	{					
		$url = self::PATH_STATUS_FRIENDS;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
	
	function user_timeline() 
	{
		$url = self::PATH_STATUS_USER;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
	
	function replies() 
	{
		$url = self::PATH_STATUS_REPLIES;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
	
	function show_status() 
	{
		$url = self::PATH_USER_SHOW;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
		
	function friends() 
	{
		$url = self::PATH_USER_FRIENDS;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
	
	function followers() 
	{
		$url = self::PATH_USER_FOLLOWERS;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
	
	function favorites() 
	{
		$url = self::PATH_FAV_FAVORITES;		
		$xml = new SimpleXMLElement($this->makeRequest($url, $this->format, true));
		$this->output($xml);
	}
 
	function makeRequest($url, $format = 'xml', $auth = false, $data = '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL . $url .'.'. $format);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($auth)
		{
			curl_setopt($ch, CURLOPT_USERPWD, $this->username .':'. $this->password);
		}
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		return $data;
	}
	
	function output($xml)
	{
		foreach ($xml->status as $status) {
		  echo "<h2>" . $status->user->name . "</h2>";
		  echo "<p>" . $status->text . "</p>";
		}
	}

	/**
	* Plugin usage documentation
	*
	* @return	string Plugin usage instructions
	*/

	function usage()
	{
		

	
	";		

	}
	
}

?>