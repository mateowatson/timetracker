<?php

class Login {
	function show($f3, $args) {
		$db = $f3->get('DB');
		$session = $f3->get('SESSION_INSTANCE');
		Utils::send_csrf($f3, $args);
		$view = new View;
		echo $view->render('login.php');
	}

	function post_login($f3, $args) {
		$db = $f3->get('DB');
		$session = $f3->get('SESSION_INSTANCE');

		Utils::prevent_csrf($f3, $args);

		$db_users = new \DB\SQL\Mapper($db, 'users');
		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];
		$user = $db_users->load(array('username=?', $request_user));

		if(password_verify($request_password, $user->password)) {
			$f3->set('SESSION.session_username', $user->username);
			$f3->reroute('/');
		} else {
			$f3->reroute('/login');
		}
	}
}
