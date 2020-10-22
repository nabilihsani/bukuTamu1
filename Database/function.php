<?php 
	function isLoginSessionExpired() {
		$login_session_duration = 21600;
		$current_time = time();
		if(isset($_SESSION['loggedin_time']) and isset($_SESSION["loggedin"])) {
			if(((time() - $_SESSION['loggedin_time']) > $login_session_duration)) {
				return true;
			}
		}
		return false;
	}
 ?>