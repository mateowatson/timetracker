<?php
class Logout {
	function post_logout($f3, $args) {
		// Should I protect against csrf for logging out?
		Utils::prevent_csrf_on_logout($f3, $args);

		// Kinda pointless but whatever
		$session_username = $f3->get('SESSION.session_username');
		if(!$session_username) {
			$f3->reroute('/login');
			return;
		}

		// Logout -- aka set username to null in db
		$f3->set('SESSION.session_username', '');
		$f3->set('SESSION.csrf', '');
		$f3->reroute('/login');
	}

	function confirm($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);

		$f3->set('v_page_title', 'Confirm Logout');
		$view = new View;
		echo $view->render('confirm-logout.php');
	}
}