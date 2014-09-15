<?php

$this->layout = 'promo';
	echo '
		<div>
			<form method="post" action="?p=register">
				<div>Registration</div>
				<div>
					Username: <input type="text" name="username">
				</div>
				<div>
					Email: <input type="text" name="email">
				</div>
				<div>
					Password: <input type="password" name="password" class="'. ($login_failed ? 'error_border' : '' ) .'">
				</div>
				<div>
					Password: <input type="password" name="password" class="'. ($login_failed ? 'error_border' : '' ) .'">
				</div>
				<input type="submit" value="Register">
			</form>
			<div class="smaller_text">
				<a href="?content=reset_password">forgot password?</a>
			</div>
		</div>';
?>