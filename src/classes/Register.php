<?php

class Register {
	function show($f3, $args) {
		Utils::send_csrf($f3, $args);
		$view = new View;
		echo $view->render('register.php');
	}

	function post_register($f3, $args) {
		Utils::prevent_csrf($f3, $args);

		$db = $f3->get('DB');

		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];

		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $request_user));

		
		if(!$user->dry()) {
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