<?php

// Users are visitors who may or may not be logged in.
// If a user logs in, then additional data will be stored in this object.
class User
{
	public $ip;
	public $prev_page;
	// Tho following fields are only used if the user is logged in.
	public $id, $username, $email, $group;
	
	function __construct()
	{
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->prev_page = $_SERVER['HTTP_REFERER'];
		$this->logged_in = $this->logged_in();
		
		if ( $this->logged_in() )
		{
			//console_log('test1');
			$this->id = $_SESSION['user_id'];
			$this->get_user_data_from_database();
		}
	}
	
	public function logged_in()
	{
		global $Mysql;
		
		if ( isset($_SESSION['user_id']) )
		{
			return true;
		}
		else if (isset($_COOKIE['pid']))
		{
			// This user has a persistent-login cookie.
			$user_id = clean_text($_COOKIE['pid']);
			$key = clean_text($_COOKIE['key']);
			$cookie_qry = $Mysql->query("
				SELECT `id` FROM `users` 
				WHERE `id` = '". $user_id ."' AND
					`cookie_login_key` = '". $key ."' AND
					`last_login` > ". (time() - (60*60*24*30)) ." ");
			// Note: Only accepts the cookie-login data if key is less than a month old.
			// Note: Setting a cookie on a new machine will invalidate the old cookie.
			
			if ( $cookie_qry->num_rows != 0 )
			{
				$this->id = $user_id;
				$this->get_user_data_from_database();
				
				// Create a new cookie key.
				$key = $this->generate_cookie_key();
				setcookie('pid', $User->id, (time()+30*24*60*60));
				setcookie('key', $key, (time()+30*24*60*60));
				$Mysql->query("UPDATE `users` 
					SET `cookie_login_key` = '". $key ."',
						`last_login` = ". time() ."
					WHERE `id` = ". $this->id ." ");
				
				return true;
			}
			else
				return false;
		}
		else
		{
			return false;
		}
	} // end method: logged_in
	
	// Retrieves some of the user's data from the database.
	// $this->id must be set before this function can be called.
	public function get_user_data_from_database()
	{
		global $Mysql;
		
		$user_qry = $Mysql->query("SELECT * FROM `users` 
			WHERE `id`=". $this->id);
		$user_qry->data_seek(0);
		$user_row = $user_qry->fetch_assoc();

		$this->id = $user_row['id'];
		$this->username = $user_row['username'];
		$this->email = $user_row['email'];
		$this->group = $user_row['group'];
			
	}
	
	// Generates a "random" text string used for automatic logins.
	public function generate_cookie_key()
	{
		$length = mt_rand(30,42);
		$s = '';
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ".
			"0123456789!@#$%^*(){}[]-_";
		
		for( $i = 0; $i < $length; $i++ )
		{
			$s .= $chars[ mt_rand(0, strlen($chars)-1) ];
		}
		
		// TODO: make sure this id is unique.
		
		return $s;
	}
}

?>