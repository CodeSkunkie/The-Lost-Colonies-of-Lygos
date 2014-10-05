<?php

$this->layout = 'promo';

// Displays the login form.
function print_login_form($login_failed, $login_error_message)
{
	echo '
	<div>
		<form method="post" action="?p=login">
			<div>Log In</div>
			<div>
				Username: <input type="text" name="username">
			</div>
			<div>
				Password: <input type="password" name="password" class="'. ($login_failed ? 'error_border' : '' ) .'">
			</div>
			'. (!empty($login_error_message) ? '
				<div class="error_text">
					'. $login_error_message .'
				</div>' 
				: '' ) .'
			<div>
				Remember me on this computer <input type="checkbox" name="remember_me" value="true">
			</div>
			<input type="submit" value="Log In">
		</form>
		<div class="smaller_text">
			<a href="?content=reset_password">forgot password?</a>
		</div>
	</div>';
}

function print_login_freeze_message()
{
	echo 'You have failed too many login attempts. 
			Please wait a bit before trying again.';
}


// If user is already logged in, redirect to the command center.
if ( $User->logged_in() )
{
	header('Location: /?p=command_center');
	exit();
}

	
if ( empty($_POST) || (empty($_POST['password']) && empty($_POST['username'])) )
	print_login_form($login_failed, $login_error_message);
else
{
	// Process form's login request.
	$username = clean_text($_POST['username']);
	$password = clean_text($_POST['password']);
	$remember_me = clean_text($_POST['remember_me']); // = "true" if true
	
	// Is the login frozen for this IP?
	$frozen_login = NULL;
	if ( isset($_SESSION['login_freeze_until']) )
	{
		if ( $_SESSION['login_freeze_until'] > time() )
			$frozen_login = true;
		else
		{
			unset($_SESSION['login_freeze_until']);
			//unset($_SESSION['login_freeze_username']);
			//unset($_SESSION['login_freeze_ip']);
		}
	}
	// Check the database too just to be sure.
	if ( is_null($frozen_login) )
	{
		// The user's session had no record of a frozen login.
		$frozen_qry = $Mysql->query("SELECT * FROM `login_freeze` 
				WHERE `ip`='". $User->ip ."' AND
					`frozen_until` > ". time() ." ");
		if ( $frozen_qry->num_rows > 0 )
		{
			$frozen_login = true;
		}
		else
		{
			// Note: Not removing the freeze record here. Maybe archive it first?
			//		 Wait and see how much data accumulates in that table over time.
		}
	}
	if ( is_null($frozen_login) )
		$frozen_login = false;
	
	if ( $frozen_login === false )
	{
		
		// Check auth credentials against the database.
		$login_qry = $Mysql->query("SELECT `id` FROM `users` 
				WHERE lower(`username`)='". strtolower($username) ."' AND 
					`password`=password('". $password ."')");
		if ( $login_qry->num_rows == 0 )
		{
			// User login failed.
			$login_error_message = "Your username or password was incorrect.";
			
			// Log this failed login attempt.
			$Mysql->query("INSERT INTO `failed_logins` 
				SET `ip`='". $User->ip ."',
					`username` = '". strtolower($username) ."',
					`time` = ". time() ." ");
			
			//// Too many failed login attempts recently?
			// case 1: too many login attempts on various usernames from a single computer.
			$failed_logins_qry1 = $Mysql->query("SELECT `time` FROM `failed_logins` 
				WHERE `ip`='". $User->ip ."' AND `time` > ". (time() - (15*60)) ."
				GROUP BY `username` ");
			if ( $failed_logins_qry1->num_rows > 20 )
			{
				// Save frozen data to cookie.
				$_SESSION['login_freeze_until'] = time() + (15*60);
				//$_SESSION['login_freeze_ip'] = $User->ip;
				// ^ could be used to distinguish username freezes from ip freezes.
				
				// Save a copy of frozen data to the database.
				$Mysql->query("INSERT INTO `login_freeze` 
					SET `ip`='". $User->ip ."',
						`frozen_until` = ". (time() + (15*60)) ." ");
			}
			else if ( $failed_logins_qry1->num_rows == 19 )
				$login_error_message .= " Two attempts remaining.";
			else if ( $failed_logins_qry1->num_rows == 20 )
				$login_error_message .= " One attempt remaining.";
			
			// case 2: too many login attempts on a single username from a single computer.
			$failed_logins_qry2 = $Mysql->query("SELECT `time` FROM `failed_logins` 
				WHERE `username`='". strtolower($username) ."' AND 
					`ip`='". $User->ip ."' AND `time` > ". (time() - (15*60)) ." ");
			if ( $failed_logins_qry2->num_rows > 10 )
			{
				// Save frozen data to cookie.
				$_SESSION['login_freeze_until'] = time() + (10*60);
				//$_SESSION['login_freeze_ip'] = $username;
				
				// Save a copy of the login-freeze to the database.
				$Mysql->query("INSERT INTO `login_freeze` 
					SET `ip`='". $User->ip ."',
						`frozen_until` = ". (time() + (15*60)) ." ");
			}
			else if ( $failed_logins_qry2->num_rows == 9 )
				$login_error_message .= " Two attempts remaining.";
			else if ( $failed_logins_qry2->num_rows == 10 )
				$login_error_message .= " One attempt remaining.";
			
			// case 3: too many login attempts on a single username from various computers.
			// Correct course of action?
			// Don't want to lock out the legitimate owner or cause them too much trouble... 
			// Alternative 1: During some duration, add a freeze for any ips that fail a login.
			//		Assumes that each computer in an attack network neads to try multiple times.
			//		Butter than doing nothing.
			//$failed_logins_qry3 = $Mysql->query("SELECT `time` FROM `failed_logins` 
			//	WHERE `username`='". strtolower($username) ."' 
			//GROUP BY `ip`");
			// Tell what to successful auth(?): "There has recently been suspicious login-activity on your account. Please... (check email / answer questions / etc)"
			
			if ( isset($_SESSION['login_freeze_until']) )
				print_login_freeze_message();
			else
			{
				$login_failed = true; // This variable will be read by the login-form.
				print_login_form($login_failed, $login_error_message);
			}
			// TODO: rate-limit cookie-login attempts?
		}
		else
		{
			// Fetch the user's row from the previous database query.
			$login_qry->data_seek(0);
			$user_row = $login_qry->fetch_assoc();
			
			// Save the user-id to the User object.
			$User->id = $user_row['id'];
					
			// Save this user's id in the browsing session.
			$_SESSION['user_id'] = $User->id;
			
			// Save some of the other user-data into the user object:
			$User->get_user_data_from_database();
			
			// Save login data to a cookie?
			if ( $remember_me == 'true' )
			{
				// Generate a temporary login key.
				$key = $User->generate_cookie_key();
				
				// Save user-id and key to a cookie.
				setcookie('pid', $User->id, (time()+30*24*60*60));
				setcookie('key', $key, (time()+30*24*60*60));
				
				// Save key to the database for later comparison.
				$Mysql->query("UPDATE `users` 
					SET `cookie_login_key` = '". $key ."',
						`last_login` = ". time() ."
					WHERE `id` = ". $User->id ." ");
			}
			
			// Log-in completed. Redirect to a new page.
			if ( isset($_SESSION['post-login']) )
			{
				// User tried to access a login-required page before logging in.
				// Send them back to that page now.
				header('Location: /?'. $_SESSION['post-login']);
				unset($_SESSION['post-login']);
			}
			else
				header('Location: /?p=command_center');
		}
	}
	else // $frozen_login === true
	{
		print_login_freeze_message();
	}
}


?>