<?php

/*
=====================================================
ExpressionEngine - by EllisLab
-----------------------------------------------------
http://expressionengine.com/
-----------------------------------------------------
Copyright (c) 2003 - 2004 EllisLab, Inc.
=====================================================
THIS IS COPYRIGHTED SOFTWARE
PLEASE READ THE LICENSE AGREEMENT
http://expressionengine.com/docs/license.html
=====================================================
File: mcp.fortunes.php
-----------------------------------------------------
Purpose: Fortunes module - CP
=====================================================
*/

class Twitee_CP {

    var $version        = '1.0';

	var $settings			= array();
	
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
					default:	
						$this->twitee_home();
						break;
				}
			}
		}
		

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


		// ----------------------------------------
		//  Module Homepage
		// ----------------------------------------

		function twitee_home()
		{
			
		// We might use this in the future so leave for now
		
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



     
    // ----------------------------------------
    //  Module installer
    // ----------------------------------------

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
    // END
    
    
    // ----------------------------------------
    //  Module de-installer
    // ----------------------------------------

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
    // END

	function settings_form()
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
		
		$DSP->body .= $DSP->form_open(
								array(
									'action' => 'C=admin'.AMP.'M=twitee'.AMP.'P=save_settings'
								)
		);
	



		// Twitter Account Details
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
			. $DSP->input_pass('twitter_username', $settings['password'])
			. $DSP->td_c()
			. $DSP->tr_c();

		$DSP->body .= $DSP->table_c();

		$DSP->body .= $DSP->qdiv('itemWrapperTop', $DSP->input_submit())
					. $DSP->form_c();
	}



}
// END CLASS
?>