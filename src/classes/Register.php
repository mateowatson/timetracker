<?php

class Register {
	function show($f3, $args) {
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Register');
		$view = new View;
		echo $view->render('register.php');
	}

	function post_register($f3, $args) {
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/register');

		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'registration_errors',
				'message' => 'Please fill out all the user creation fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/register');

		$db = $f3->get('DB');

		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $request_user));

		if($user !== FALSE) {
			$f3->push('v_errors', array(
				'element_id' => 'registration_errors',
				'message' => 'The username '.$request_user.' is already taken.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/register');

		$db->exec(
			'INSERT INTO users (username, password) VALUES (?, ?)',
			array(
				$request_user,
				password_hash($request_password, PASSWORD_DEFAULT)
			)
		);

		$f3->reroute('/login');
	}
}