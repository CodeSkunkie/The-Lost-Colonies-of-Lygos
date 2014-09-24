<?php

$this->layout = 'promo';

// Displays the login form.
function print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message)
{
	echo '
	<div id="register">
		<h3>Registration</h3>
		<form method="post" action="?p=register">
			<div class="inputFields">
				Username: <input type="text" name="username" class="'. ($user_failed ? 'error_border' : '' ) .'" value="'. $username .'">
			</div>
			<div class="inputFields">
				Email: <input type="text" name="email" class="'. ($email_failed ? 'error_border' : '' ) .'" value="'. $email .'">
			</div>
			<div class="inputFields">
				Password: <input type="password" name="password" class="'. ($password_failed ? 'error_border' : '' ) .'">
			</div>
			<div class="inputFields">
				Retype Password: <input type="password" name="re_password" class="'. ($password_failed ? 'error_border' : '' ) .'">
			</div>
			'. (!empty($register_error_message) ? '
				<div class="error_text">
					'. $register_error_message .'
				</div>' 
				: '' ) .'
			<input type="submit" value="Register">
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


	
if ( empty($_POST) || (empty($_POST['email']) && empty($_POST['username']) && empty($_POST['password']) && empty($_POST['re_password'])) ){
	print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
}
else
{
	// Process form's register request.
	$username = clean_text($_POST['username']);
	$email = clean_text($_POST['email']);
	$password = clean_text($_POST['password']);
	$re_password = clean_text($_POST['re_password']);
	$user_failed = false;
	$email_failed = false;
	$password_failed = false;

	$register_user_qry = $Mysql->query("SELECT `id` FROM `players` 
			WHERE lower(`username`)='". strtolower($username) ."'");
	//doesn't recognize case sensitve email
	$register_email_qry = $Mysql->query("SELECT `id` FROM `players` 
			WHERE lower(`email`)='". strtolower($email) ."'");
	if (empty($email) || strlen($email) < 4 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Email too short to be valid
		$register_error_message = "Please enter in a valid email address";
		$email_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if ($register_email_qry->num_rows != 0) {
	    // Email register failed.
		$register_error_message = "Your email has already been registered.";
		$email_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if (empty($username) || strlen($username) < 3) {
		// Usernames have to be at least 3 characters long
		$register_error_message = "Usernames must be at least 3 characters long.";
		$user_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if ($register_user_qry->num_rows != 0) {
	    // User register failed.
		$register_error_message = "Your username has already been taken.";
		$user_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if (empty($password) || strlen($password) < 5) {
	    // Password register failed.
		$register_error_message = "Your password is not long enough.";
		$password_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if ($password != $re_password) {
	    // Password register failed.
		$register_error_message = "Your passwords do not match.";
		$password_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else {
		//Register successful  
		//haven't put it in yet, make colony
		$Mysql->query("INSERT INTO `players` 
			SET `username`='". $username ."',
				`email` = '". $email ."',
				`group` = 'player',
				`password` = password('".  $password ."')");
		$Mysql->query("INSERT INTO `users` 
			SET `username`='". $username ."',
				`email` = '". $email ."',
				`group` = 'player',
				`date_registered` = '". time() . "',
				`password` = password('".  $password ."')");
	
		$maxX = 50;
		$maxY = 50;
		//min distance from any colony
		$min_col_distance = 5;
		//can have max_nearby of colonies within min_rad
		$min_rad = 10;
		$max_nearby = 5;
		$valid_coor = false;
		while(!$valid_coor) {
			$coord_x = rand(-$maxX, $maxX);
			$coord_y = rand(-$maxY, $maxY);
			//find numbers of colonies within min_col
			$coor_qry = $Mysql->query("SELECT id FROM `colonies` WHERE SQRT((POW('".$coord_x."'-'coord_x',2))+(POW('".$coord_y."'-'coord_y',2))) < '".$min_col_distance."'");
			if($coor_qry->num_rows == 0)
				$valid_coor = true;
			//find number of colonies within min_rad
			$nearby_qry = $Mysql->query("SELECT id FROM `colonies` WHERE SQRT((POW('".$coord_x."'-'coord_x',2))+(POW('".$coord_y."'-'coord_y',2))) < '".$min_rad."'");
			if($cnearby_qry->num_rows > $max_nearby)
				$valid_coor = false;
		}
		$pID_qry = $Mysql->query("SELECT id FROM `players` 
			WHERE lower(`username`)='". strtolower($username) ."'");
		$pID_qry->data_seek(0);
		$pID_row = $pID_qry->fetch_assoc();
		$player_id = $pID_row['id'];
		$Mysql->query("INSERT INTO `colonies` 
			SET `player_id`='". $player_id ."',
				`x_coord`='". $coord_x ."',
				`y_coord`='". $coord_y ."',
				`resource1_capacity` = 100,
				`resource1_stock` = 100,
				`resource1_production_rate` = 20,
				`resource1_consumption_rate` = 0,
				`resource2_capacity` = 1000,
				`resource2_stock` = 1000,
				`resource2_production_rate` = 50,
				`resource2_consumption_rate` = 0,
				`resource3_capacity` = 1000,
				`resource3_stock` = 1000,
				`resource3_production_rate` = 25,
				`resource3_consumption_rate` = 0,
				`resource4_capacity` = 100,
				`resource4_stock` = 100,
				`resource4_production_rate` = 5,
				`resource4_consumption_rate` = 2");

		// Check auth credentials against the database.
		$login_qry = $Mysql->query("SELECT `id` FROM `users` 
				WHERE lower(`username`)='". strtolower($username) ."' AND 
					`password`=password('". $password ."')");
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
		else{
			header('Location: /?p=command_center');
		}

	}
	
}


?>
