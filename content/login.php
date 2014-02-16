<?php

$this->layout = 'promo';

// Displays the login form.
function print_login_form()
{
	echo '
	<div>
		<form method="post" action="?p=login">
			<div>Log In</div>
			<div>
				Username: <input type="text" name="username">
			</div>
			<div>
				Password: <input type="password" name="password">
			</div>
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


	
if ( empty($_POST) || !isset($_POST['password']) )
	print_login_form();
else
{
	// Process form's login request.
	$username = clean_text($_POST['username']);
	$password = clean_text($_POST['password']);
	$remember_me = clean_text($_POST['remember_me']); // = "true" if true

	$login_qry = $Mysql->query("SELECT `id` FROM `users` 
			WHERE lower(`username`)='". strtolower($username) ."' AND 
				`password`=password('". $password ."')");
	if ( $login_qry->num_rows == 0 )
	{
		// User login failed.
		$login_failed = true;
		print_login_form();
		
		// TODO: rate-limit failed requests.
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
			header('Location: /');
	}
}


?>