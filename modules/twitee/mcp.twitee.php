<?php
/**
 * ExpressionEngine
 *
 * LICENSE
 *
 * ExpressionEngine by EllisLab is copyrighted software
 * The licence agreement is available here http://expressionengine.com/docs/license.html
 * 
 * Module Control Panel File for Twit-ee module
 *
 * Fetches data from Twitter for display in templates
 *
 * 
 * @version    0.0.4
 * @author     George Ornbo <george@shapeshed.com>
 * @license    http://opensource.org/licenses/bsd-license.php
 */
 
/**
 * Twit-ee module
 *
 * @category   Modules
 * @package    Twit-ee
 */

class Twitee_CP {
	
	/**
	* Version number of the module
	* @var string
	*/
    var $version        = '0.0.4';

	/**
	* Settings used in this module
	* @var array
	*/
	var $settings			= array();
	
	/**
	* Constructor - decides which function to show based on URL
	*
	* @param  bool $switch Twitter account username
	*/	
	function Twitee_CP( $switch = TRUE )
		{
			global $IN;
						
			if ($switch)
			{
				switch($IN->GBL('P'))
				{
					case 'account_details':	
						$this->settings_form();
						break;	
					case 'update_account':	
						$this->update_account();
						break;
					default:	
						$this->settings_form();
						break;
				}
			}
		}
		
	/**
	* Gets settings from the database
	*/
	function get_settings()
	{
		global $DB;
		
		$settings = FALSE;
		
		$query = $DB->query("SELECT * FROM exp_twitee LIMIT 1");
			
		if ($query->num_rows > 0)
		{
			$settings['username'] = $query->row['username'];
			$settings['password'] = $query->row['password'];
		}
		else
		{
			$settings['username'] = "";
			$settings['password'] = "";			
		}
		return $settings;
	}


	/**
	* Module homepage - not currently used but will be needed for OAuth
	*
	* @param  bool $switch Twitter account username
	*/
	function twitee_home()
	{
		global $DSP, $LANG;
		
		$DSP->title = $LANG->line('twitee_module_name');
		
		$DSP->crumb = $DSP->anchor(BASE.
		                          AMP.'C=modules'.
		                          AMP.'M=twitee',
		                          $LANG->line('twitee_module_name'));

		$DSP->crumb .= $DSP->crumb_item($LANG->line('twitee_home')); 

		$DSP->body .= $DSP->heading($LANG->line('twitee_module_name'));

		$DSP->body .= $DSP->qdiv('itemWrapper', $DSP->heading($DSP->anchor(BASE.
		                                                                  AMP.'C=modules'.
		                                                                  AMP.'M=twitee'.
		                                                                  AMP.'P=account_details', 
		                                                                  $LANG->line('twitee_account_details')),
		                                                                  5));

	}

	function settings_form($response)
	{
		global $DB, $DSP, $LANG, $IN, $PREFS;
		
		$settings = $this->get_settings();
		
		$DSP->crumbline = TRUE;
		
		$DSP->title  = $LANG->line('twitee_account_details');
		
		$DSP->crumb = $DSP->anchor(BASE.
		                           AMP.'C=modules'.
		                           AMP.'M=twitee',
		                           $LANG->line('twitee_module_name'));
		
		$DSP->crumb .= $DSP->crumb_item($LANG->line('twitee_account_details'));
		
		$DSP->body = '';
		
		$DSP->body .= $DSP->heading($LANG->line('twitee_account_details'));
		
		if($response['success'])
		{
			$DSP->body .= $response['success'];	
		}
		
		if($response['create_success'])
		{
			$DSP->body .= $response['create_success'];	
		}
		
		$DSP->body .= $DSP->form_open(
								array(
									'action' => 'C=modules'.AMP.'M=twitee'.AMP.'P=update_account'
								)
		);
		
		$DSP->body .= $DSP->table_open(array('class' => 'tableBorder', 'border' => '0', 'style' => 'margin-top:18px; width:100%'));
		
		$DSP->body .= $DSP->tr()
			. $DSP->td('tableHeading', '', '2')
			. $LANG->line("twitee_account_details")
			. $DSP->td_c()
			. $DSP->tr_c();
			
		$DSP->body .= $DSP->tr()
			. $DSP->td('tableCellOne', '40%')
			. $DSP->qdiv('defaultBold', $LANG->line('twitee_username_label'))
			. $DSP->td_c();
			
		$DSP->body .= $DSP->td('tableCellOne')
			. $DSP->input_text('twitter_username', $settings['username'])
			. $DSP->td_c()
			. $DSP->tr_c();
			
		$DSP->body .= $DSP->tr()
			. $DSP->td('tableCellTwo', '40%')
			. $DSP->qdiv('defaultBold', $LANG->line('twitee_password_label'))
			. $DSP->td_c();
			
		$DSP->body .= $DSP->td('tableCellTwo')
			. $DSP->input_pass('twitter_password', str_rot13($settings['password']))
			. $DSP->td_c()
			. $DSP->tr_c();
			
		$DSP->body .= $DSP->table_c();
		
		$DSP->body .= $DSP->qdiv('itemWrapperTop', $DSP->input_submit())
					. $DSP->form_c();
					
	}

    function update_account()
    {
		global $DB, $DSP, $IN, $LANG;
		
		if ( ! $IN->GBL('twitter_username', 'POST') )
		{
			return $DSP->error_message($LANG->line('twitee_username_error'));
		}
		
		if ( ! $IN->GBL('twitter_password', 'POST') )
		{
			return $DSP->error_message($LANG->line('twitee_password_error'));
		}
		
		if ( $IN->GBL('twitter_username', 'POST') && $IN->GBL('twitter_password', 'POST') )
		{
				
			/*
			Because of Twitter's use of the password anti-pattern we need to 
			get the password out as plain text. This means we can't use MD5 or SHA1
			Crap! So provide some limited protection with str_rot13.	
			*/
			
			$data = array(	'username' 	=> $IN->GBL('twitter_username', 'POST'),
							'password'	=> str_rot13($IN->GBL('twitter_password', 'POST')));
							
			$query = $DB->query("SELECT * FROM exp_twitee LIMIT 0,1");
			
			if ($query->num_rows == 0)
			{
				 		
				$DB->query($DB->insert_string('exp_twitee', $data));
				
				$response['create_success'] = $DSP->qdiv('success', $LANG->line('twitee_account_added'));
				
				return $this->settings_form($response);
			}	
			else
			{
				
				$DB->query($DB->update_string('exp_twitee', $data, "account_id = ".$query->result[0]['account_id'].""));
				
				$response['success'] = $DSP->qdiv('success', $LANG->line('twitee_account_updated'));
				
				return $this->settings_form($response);
								
			}
			
		}
	
    }

	/**
	* Module installer
	*
	*/
	function twitee_module_install()
	{
		global $DB;        

		$sql[] = "INSERT INTO exp_modules (module_id,
								module_name,
								module_version,
								has_cp_backend)
								VALUES
								('',
								'Twitee',
								'$this->version',
								'y')";

		$sql[] = "CREATE TABLE IF NOT EXISTS `exp_twitee` (
								`account_id` int(10) unsigned NOT NULL auto_increment,
								`username` varchar(50) NOT NULL,
								`password` varchar(40) NOT NULL,
								PRIMARY KEY (`account_id`));";
							
		foreach ($sql as $query)
		{
			$DB->query($query);
		}
	
		return true;
	}
	   
	/**
	* Module de-installer
	*
	*/
	function twitee_module_deinstall()
	{
		global $DB;    
	
		$query = $DB->query("SELECT module_id
						FROM exp_modules
						WHERE module_name = 'Twitee'");
					
		$sql[] = "DELETE FROM exp_module_member_groups
						WHERE module_id = '".$query->row['module_id']."'";      
					
		$sql[] = "DELETE FROM exp_modules
						WHERE module_name = 'Twitee'";
					
		$sql[] = "DELETE FROM exp_actions
						WHERE class = 'Twitee'";
					
		$sql[] = "DELETE FROM exp_actions
						WHERE class = 'Twitee_CP'";
					
		$sql[] = "DROP TABLE IF EXISTS exp_twitee";
	
		foreach ($sql as $query)
		{
			$DB->query($query);
		}
	
		return true;
    }

}
?>