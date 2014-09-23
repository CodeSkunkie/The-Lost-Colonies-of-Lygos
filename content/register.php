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


	
if ( empty($_POST) || (empty($_POST['email']) && empty($_POST['username']) && empty($_POST['password']) && empty($_POST['re_password'])) )
	print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
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
	if (empty($email) || strlen($email) < 4) {
		// Email too short to be valid
		$register_error_message = "Please enter in a proper email address";
		$email_failed = true;
		print_register_form($username, $email, $user_failed, $email_failed, $password_failed, $register_error_message);
	} else if ($register_email_qry->num_rows != 0) {
		/*while ($row = $register_email_qry->fetch_assoc()){
	        print_r($row);
	    }*/

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
	}
	$register_email_qry->close(); 
	$register_user_qry->close(); 
}


?>
