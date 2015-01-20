<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 *
 * Modifications by Oscar Bajner dated 8 March 2007: Initial release.
 *               by OB 14 March. Change function names back to "sess_" instead of "session_"
 *               by OB 14 March. Change class and constructor name to CI_Session. (was Session)
 * Kudos to Dariusz Debowczyk & Dready for code I used from NativeSession and DBSession
 */

// ------------------------------------------------------------------------


/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/sessions.html
 */
// ------------------------------------------------------------------------

class CI_Session {

	var $CI;
	var $now;
	var $encryption		    = FALSE;
	var $session_storage	= 'cookie';
	var $session_table	    = FALSE;
	var $session_length	    = 0; 
	var $session_cookie	    = 'ci_session';
	var $userdata		    = array();
    var $session_data       = array();
	var $gc_probability	    = 10;
    var $session_timeout    = 0; 
    var $session_start      = 0; 
	var $flash_key 		    = 'flash'; 

// ------------------------------------------------------------------------

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically whenever the class is instantiated.
	 * 
	 */		
	function CI_Session()
	{
		$this->CI =& get_instance();

		log_message('debug', "OB Modified Session Class Initialized");
		$this->sess_run();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the session routines
	 *
	 * @access	public
	 * @return	void
	 */		
	function sess_run()
	{
		/*
		 *  Set the "now" time
		 *
		 * It can either set to GMT or time(). The pref is set in the config file.
		 * If the developer is doing any sort of time localization they might want to set the session time to GMT so
		 * they can offset the "last_activity" and "last_visit" times based on each user's locale.
		 *
		 * "last_visit" has been removed from this session class. The GMT functionality has been retained.
		 * A new session variable, "session_start" replaces it, see timeout comment.
		 */
		if (strtolower($this->CI->config->item('time_reference')) == 'gmt')
		{
			$now = time();
			$this->now = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
	
			if (strlen($this->now) < 10)
			{
				$this->now = time();
				log_message('error', 'The session class could not set a proper GMT timestamp so the local time() value was used.');
			}
		}
		else
		{
			$this->now = time();
		}
		
		/*
		 *  Set the session length
		 *
		 * If the session expiration is set to zero in the config file we will set the cookie expiration to 0.
		 * That will provide a non-persistent session which ends when the browser closes.
		 * This is effectively done by setting the session length to 24 hours. (ie if the browser session lasts
		 * longer than 24 hours the session will be terminated.
         *
         * NOTE: The key to the session class is the "session_last_activity". As this is updated it effectively
         * advances the actual expiry time of the session. A new "timeout" setting allows the developer to set a
         * fixed duration for the session lifetime.
		 */
		$expiration = $this->CI->config->item('sess_expiration');

		if (is_numeric($expiration))
		{
			if ($expiration > 0)
			{
				$this->session_length = $this->CI->config->item('sess_expiration');
			}
			else
			{
				$this->session_length = (60*60*24);
			}
		}
        
		/*
		 *  Set the session timeout
         *
		 * A timeout setting is provided in the config file.
		 * This allows the session to be "timed out" based on a fixed elapse of time. The session may be given a fixed
		 * duration or "time-to-live" after which it will be regenerated (with a new session id) or optionally, destroyed.
		 * 
		 * To prevent any timeout, we use a config setting of 0. Effectively this means two years.
		 */
		$timeout = $this->CI->config->item('sess_timeout');
		
		if (is_numeric($timeout))
		{
			if ($timeout > 0)
			{
				$this->session_timeout = $this->CI->config->item('sess_timeout');
			}
			else
			{
				$this->session_timeout = (60*60*24*365*2);
			}
		}
        
		// Do we need encryption?
		$this->encryption = $this->CI->config->item('sess_encrypt_cookie');
	
		if ($this->encryption == TRUE)	
		{
			$this->CI->load->library('encrypt');
		}		

		// Set the cookie name
		if ($this->CI->config->item('sess_cookie_name') != FALSE)
		{
			$this->session_cookie = $this->CI->config->item('cookie_prefix').$this->CI->config->item('sess_cookie_name');
		}
	    
        // Set the session storage medium, default is cookie
        if ($this->CI->config->item('sess_storage') != FALSE)
        {
            $this->session_storage = $this->CI->config->item('sess_storage');
        }
        
		if ($this->session_storage == 'database' AND $this->CI->config->item('sess_table_name') != '')
		{
			$this->session_table = $this->CI->config->item('sess_table_name');
			$this->CI->load->database();
		}      
        
		/*
		 *  Fetch the current session
		 *
		 * If a session doesn't exist we'll create a new one.  If it does, we'll update it.
		 *
		 */
		if ( ! $this->sess_read())
		{
			$this->sess_create();
		}
		else
		{	
            // The session is only updated periodically, (based on the config setting) to ease system usage.
            $interval = $this->CI->config->item('sess_update_interval');
            if (! is_numeric($interval))
            {
                $interval = 300; 
            }
			if (($this->session_data['session_last_activity'] + $interval) < $this->now) 
			{
				$this->sess_update();
			}
		}
		
		// Delete expired sessions if necessary
		if ($this->session_storage == 'database')
		{		
			$this->sess_gc();
		}
		// Delete old flashdata (from last request)
        	$this->_flashdata_sweep();
        
        // Mark all new flashdata as old (data will be deleted before next request)
        	$this->_flashdata_mark();
	}
	// END sess_run()	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current session data if it exists
     * Session data now consists of two parts.
     * "session_data": An array, always written to the cookie. Items are prefixed with "session_". Session control info.
	 * These are: session_id, session_start, session_last_activity, session_ip_address, session_user_agent.
     * "userdata": User data written to the session. Maintains backward compatibility with CI_Session class usage.
     * User data is stored either in the cookie or other storage medium, depending on config setting.
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{	
		// Fetch the cookie
		$session = $this->CI->input->cookie($this->session_cookie);
		
		if ($session === FALSE)
		{
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}
		
		// Decrypt and unserialize the data
		if ($this->encryption == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}

		$session = @unserialize($this->strip_slashes($session));
		
        // Basic reality checks.
		if ( ! is_array($session) )
		{
			log_message('error', 'The session cookie data did not contain a valid array. This could be a possible hacking attempt.');
			return FALSE;
		}
        
		if ( ! isset($session['session_last_activity']) )
		{
			log_message('error', 'The session_last_activity is not set. This could be a possible hacking attempt.');
			return FALSE;
		}
        
		if ( ! isset($session['session_start']) )
		{
			log_message('error', 'The session_start is not set. This could be a possible hacking attempt.');
			return FALSE;
		}
        
		// Is the session current?
		if (($session['session_last_activity'] + $this->session_length) < $this->now)
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->CI->config->item('sess_match_ip') == TRUE AND $session['session_ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}
		
		// Does the User Agent Match?
		if ($this->CI->config->item('sess_match_useragent') == TRUE AND $session['session_user_agent'] != substr($this->CI->input->user_agent(), 0, 50))
		{
			$this->sess_destroy();
			return FALSE;
		}
	
        // Is the session Timed Out? 
        $sess_destroy = $this->CI->config->item('sess_destroy_on_timeout');
		if (($session['session_start'] + $this->session_timeout) < $this->now)
		{
            // We destroy the session here. If the config setting is FALSE, we regenerate on next update.
            if ($sess_destroy == TRUE)
            {
			    $this->sess_destroy();
			    return FALSE;
            }
		}    

		// Is there a corresponding session in the DB? Provides some extra protection against cookie tampering.
		if ($this->session_storage == 'database')
		{
			$this->CI->db->where('session_id', $session['session_id']);
					
			if ($this->CI->config->item('sess_match_ip') == TRUE)
			{
				$this->CI->db->where('session_ip_address', $session['session_ip_address']);
			}

			if ($this->CI->config->item('sess_match_useragent') == TRUE)
			{
				$this->CI->db->where('session_user_agent', $session['session_user_agent']);
			}
			
			$query = $this->CI->db->get($this->session_table);

			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}
			else
			{
				$row = $query->row();
				if (($row->session_last_activity + $this->session_length) < $this->now) 
				{
					$this->CI->db->where('session_id', $session['session_id']);
					$this->CI->db->delete($this->session_table);
					$this->sess_destroy();
					return FALSE;
				}
                else 
                {
				    $db_userdata = @unserialize($row->session_data);
				    if ( ! is_array($db_userdata) ) 
                    {
					    $db_userdata = array();
				    }
			    }
			}
		}

		// Session is valid! 
        // If we are using a database, add the userdata to the "session" array read from the cookie.
        if ($this->session_storage == 'database')
        {
            foreach ($db_userdata as $name => $value)
            {
                $session[$name] = $value;
            }
        }
        // Now we process the "session" array, splitting session_data and userdata into seperate entities.
        foreach ($session as $key => $val)
        {
            if (substr($key, 0, 8) == 'session_')
            {
                $this->session_data[$key] = $val;
            }
            else
            {
                $this->userdata[$key] = $val;
            }
        }

		unset($session);
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session cookie
     *
     * If we are using session storage other than "cookie" , write it there also.
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{								
        $cookie_data = array();
        $cookie_data = $this->session_data;
        
        // If userdata is to be stored in the cookie, add it to the cookie array.
		if ($this->session_storage == 'cookie')
        {
            foreach ($this->userdata as $key => $val)
            {
                $cookie_data[$key] = $val;
            }
        }
        
        $cookie_data = serialize($cookie_data);
		
		if ($this->encryption == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);            
		}
        
        // Check if we need to set a non-persistent cookie.
        $cookie_expiry = $this->CI->config->item('sess_expiration');
        if ($cookie_expiry > 0)
        {
            $cookie_expiry = $this->session_length + time();
        }
        else
        {
            $cookie_expiry = 0;
        }
        
		setcookie(
					$this->session_cookie,
					$cookie_data,
					$cookie_expiry,
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
                
		// If we are using a database, we update it with session_data from the cookie and the userdata.
		if ($this->session_storage == 'database')
        {
            $db_user_data = $this->userdata;
		    $query_array = array('session_start'            => $this->session_data['session_start'],
                                'session_last_activity'     => $this->session_data['session_last_activity'],
					            'session_ip_address'        => $this->session_data['session_ip_address'], 
                                'session_user_agent'        => $this->session_data['session_user_agent']
                                );

		    $query_array['session_data'] = serialize($db_user_data);
            $this->CI->db->query($this->CI->db->update_string($this->session_table, $query_array, array('session_id' => $this->session_data['session_id'])));
        }
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{	
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}
	
		$this->session_data = array(
							'session_id' 	        => md5(uniqid($sessid, TRUE)),
                            'session_start'         => $this->now, 
							'session_last_activity'	=> $this->now,
							'session_ip_address' 	=> $this->CI->input->ip_address(),
							'session_user_agent' 	=> substr($this->CI->input->user_agent(), 0, 50)
							);
                            
        $this->userdata = array();
		
		// Save the session in the DB if needed
		if ($this->session_storage == 'database')
		{
            $cookie_data = array();
            $cookie_data = $this->session_data;
            
			$this->CI->db->query($this->CI->db->insert_string($this->session_table, $cookie_data));
		}
			
		// Write the cookie
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{	
        // Is the session Timed Out? If so we regenerate the session, new id,  keep the old data.
        $sess_destroy = $this->CI->config->item('sess_destroy_on_timeout');
		if (($this->session_data['session_start'] + $this->session_timeout) < $this->now)
		{
            if ($sess_destroy == FALSE)
            {
			    $this->regenerate_id();
			    return;
            }
		}
        
		$this->session_data['session_last_activity'] = $this->now;
        
		if ($this->session_storage == 'database')   
		{		
			$this->CI->db->query($this->CI->db->update_string($this->session_table, array('session_last_activity' => $this->now), array('session_id' => $this->session_data['session_id'])));
		}        
		
		// Write the cookie
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		setcookie(
					$this->session_cookie,
					addslashes(serialize(array())),
					($this->now - 31500000),
					$this->CI->config->item('cookie_path'),
					$this->CI->config->item('cookie_domain'),
					0
				);
	}
	
	// --------------------------------------------------------------------
	
	/** 
	 * Generate a new session_id, saving the old userdata and session_data
	 *
	 * @access	public
	 * @return	void
	 */
	function regenerate_id()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}
        
        // Update the session data in the session data array. 
		$this->session_data['session_id'] = md5(uniqid($sessid, TRUE));
        $this->session_data['session_start'] = $this->now;
        $this->session_data['session_last_activity'] = $this->now;

        // The session_id has changed, so add a new row to the database.
		if ($this->session_storage == 'database')
		{
            $db_sess_data = array();
            $db_sess_data = $this->session_data;
            // session_write will update userdata into the row, for now just insert the new row
			$this->CI->db->query($this->CI->db->insert_string($this->session_table, $db_sess_data));
		}

		// Write the cookie
		$this->sess_write();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_gc()
	{
		srand(time());
		if ((rand() % 100) < $this->gc_probability)
		{
			$expire = $this->now - $this->session_length;
			$this->CI->db->where("session_last_activity < {$expire}");
			$this->CI->db->delete($this->session_table);

			log_message('debug', 'Session garbage collection performed.');
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch a specific item from  the session array
     *
	 * If the item is prefixed with "session_" drop it into the session_data array.
	 * @access	public
	 * @param	string
	 * @return	string
	 */
     
	function userdata($item)
	{
        if (substr($item, 0, 8) == 'session_')
        {
            return ( ! isset($this->session_data[$item])) ? FALSE : $this->session_data[$item];
        }
        else
        {
            return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
        }
	}

	// --------------------------------------------------------------------
	
	/**
	 * Fetch all userdata from the session array
     *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
     
	function all_userdata()
	{
        return ( ! isset($this->userdata)) ? FALSE : $this->userdata;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */		
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}
	
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */		
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}
	
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}
	
		$this->sess_write();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Return the Session ID string
     *
	 * This is a convenience function (saves typing).
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function id()
	{
        return ( ! isset($this->session_data['session_id'])) ? FALSE : $this->session_data['session_id'];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Strip slashes
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	 function strip_slashes($vals)
	 {
	 	if (is_array($vals))
	 	{	
	 		foreach ($vals as $key=>$val)
	 		{
	 			$vals[$key] = $this->strip_slashes($val);
	 		}
	 	}
	 	else
	 	{
	 		$vals = stripslashes($vals);
	 	}
	 	
	 	return $vals;
	}
    
	// --------------------------------------------------------------------

    /**
    * Sets "flash" data which will be available only in next request (then it will
    * be deleted from session). You can use it to implement "Save succeeded" messages
    * after redirect.
    */
    function set_flashdata($key, $value)
    {
        $flash_key = $this->flash_key.':new:'.$key;
        $this->set_userdata($flash_key, $value);
    }
    
    /**
    * Keeps existing "flash" data available to next request.
    */
    function keep_flashdata($key)
    {
        $old_flash_key = $this->flash_key.':old:'.$key;
        $value = $this->userdata($old_flash_key);

        $new_flash_key = $this->flash_key.':new:'.$key;
        $this->set_userdata($new_flash_key, $value);
    }

    /**
    * Returns "flash" data for the given key.
    */
    function flashdata($key)
    {
        $flash_key = $this->flash_key.':old:'.$key;
        return $this->userdata($flash_key);
    }
    
    /**
    * PRIVATE: Internal method - marks "flash" session attributes as 'old'
    */
    function _flashdata_mark()
    {
	$userdata = $this->all_userdata();
        foreach ($userdata as $name => $value)
        {
            $parts = explode(':new:', $name);
            if (is_array($parts) && count($parts) == 2)
            {
                $new_name = $this->flash_key.':old:'.$parts[1];
                $this->set_userdata($new_name, $value);
                $this->unset_userdata($name);
            }
        }
    }

    /**
    * PRIVATE: Internal method - removes "flash" session marked as 'old'
    */
    function _flashdata_sweep()
    {
	$userdata = $this->all_userdata();
        foreach ($userdata as $name => $value)
        {
            $parts = explode(':old:', $name);
            if (is_array($parts) && count($parts) == 2 && $parts[0] == $this->flash_key)
            {
                $this->unset_userdata($name);
            }
        }
    }
}
// END Session Class

/*
CREATE TABLE IF NOT EXISTS  `ci_sessions` (
session_id varchar(40) DEFAULT '0' NOT NULL,
session_start int(10) unsigned DEFAULT 0 NOT NULL,
session_last_activity int(10) unsigned DEFAULT 0 NOT NULL,
session_ip_address varchar(16) DEFAULT '0' NOT NULL,
session_user_agent varchar(50) NOT NULL,
session_data text default '' NOT NULL,
PRIMARY KEY (session_id)
);
*/
?>