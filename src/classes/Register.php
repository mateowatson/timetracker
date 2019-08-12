<?php

class Register {
	function show($f3, $args) {
		$db = $f3->get('DB');
		$site_options = new \DB\SQL\Mapper($db, 'site_options');
		$open_registration = $site_options->load(array('option_key = \'open_registration\''));
		if($open_registration->option_value === 'false') {
			$f3->reroute('/login');
		}

		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Register');
		$view = new View;
		echo $view->render('register.php');
	}

	function post_register($f3, $args) {
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/register');

		$db = $f3->get('DB');
		$site_options = new \DB\SQL\Mapper($db, 'site_options');
		$open_registration = $site_options->load(array('option_key = \'open_registration\''));
		if($open_registration->option_value === 'false') {
			$f3->reroute('/login');
		}

		$request_user = $f3->get('REQUEST')['username'];
		$request_email = $f3->get('REQUEST')['email'];
		$request_password = $f3->get('REQUEST')['password'];

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'registration_errors',
				'message' => 'Please fill out all the user creation fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/register');

		Utils::validate_username($f3, $request_user, 'registration_errors');

		Utils::validate_password($f3, $request_password, 'registration_errors');

		Utils::reroute_with_errors($f3, $args, '/register');

		$new_user = $db->exec(
			'INSERT INTO users (username, password, email) VALUES (?, ?, ?)',
			array(
				$request_user,
				password_hash($request_password, PASSWORD_DEFAULT),
				$request_email ? : ''
			)
		);

		///error_log(print_r($new_user, true));

		if($new_user === 1) {
			$f3->push('v_confirmations', array(
				'element_id' => 'registration_confirmations',
				'message' => 'You have successfully registered!'
			));
			Utils::reroute_with_confirmations($f3, $args, '/login');
		}

		$f3->push('v_errors', array(
			'element_id' => 'registration_errors',
			'message' => 'Your registration request failed.'
		));
		Utils::reroute_with_errors($f3, $args, '/register');
	}
}