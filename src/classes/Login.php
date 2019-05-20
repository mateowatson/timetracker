<?php

class Login {
	function show($f3, $args) {
		Utils::redirect_logged_in_user($f3, $args);
		$db = $f3->get('DB');
		$session = $f3->get('SESSION_INSTANCE');
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Login');
		$view = new View;
		echo $view->render('login.php');
	}

	function post_login($f3, $args) {
		$db = $f3->get('DB');
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/login');

		$db_users = new \DB\SQL\Mapper($db, 'users');
		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];
		$user = $db_users->load(array('username=?', $request_user));

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'login_errors',
				'message' => 'Please fill out all the user creation fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/login');

		if(password_verify($request_password, $user->password)) {
			$f3->set('SESSION.session_username', $user->username);
			$f3->reroute('/');
		} else {
			$f3->push('v_errors', array(
				'element_id' => 'login_errors',
				'message' => 'The username and password you entered are incorrect.'
			));
			Utils::reroute_with_errors($f3, $args, '/login');
		}
	}
}
