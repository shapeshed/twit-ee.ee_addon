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
	* Returned string
	* @var array
	*/
    var $return_data;


	function public_timeline() 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/public_timeline.json");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);	
		curl_close($ch);
	}

	function friends_timeline() 
	{


	}
	
	function user_timeline() 
	{


	}
	
	function show_status() 
	{


	}
	
	function replies() 
	{


	}
	
	function friends() 
	{


	}
	
	function followers() 
	{


	}

	function show_user_status() 
	{


	}
	
	function favorites() 
	{


	}
 
	/**
	* Get the document size
	* @access	public
	*/
	function get_status() 
	{


	}

	/**
	* Plugin usage documentation
	*
	* @return	string Plugin usage instructions
	*/

	function usage()
	{
		
	return "This plugin returns the size of a file in human readable format (e.g 101.34 KB, 10.41 GB )
	
	Wrap the absolute path filename in these tags to have it processed

	{exp:doc_size}/uploads/documents/your_document.pdf{/exp:doc_size}

	If you are using Mark Huot's File extension you can just use the EE tag you chose for the file field

	{exp:ss_human_file_size}{your_file_field}{/exp:ss_human_file_size}
	
	The function calculates whether to show KB, MB or GB depending on the file size. 
	
	";		

	}
	
}

?>