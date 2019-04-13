<?php
class Logout {
	function post_logout($f3, $args) {
		// Should I protect against csrf for logging out?
		Utils::prevent_csrf($f3, $args);

		// Kinda pointless but whatever
		$session_username = $f3->get('SESSION.session_username');
		if(!$session_username) {
			$f3->reroute('/login');
			return;
		}

		// Logout -- aka set username to null in db
		$f3->set('SESSION.session_username', '');
		$f3->reroute('/login');
	}
}