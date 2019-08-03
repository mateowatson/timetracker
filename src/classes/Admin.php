<?php

class Admin {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Admin');
		
		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

		if(!$user->admin) {
			$f3->reroute('/login');
		}
		
		// RENDER
		$view = new \View;
        echo $view->render('admin.php');
	}

	function post_admin_settings($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/admin');

		$req = $f3->get('REQUEST');
		$req_username = $req['admin_username'];
		$req_password = $req['admin_password'];

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));

		if($req_username) {
			$is_invalid = Utils::username_invalid_check($f3, $req_username);

			if($is_invalid) {
				$f3->push('v_errors', array(
					'element_id' => 'admin_errors',
					'message' => $is_invalid
				));

				Utils::reroute_with_errors($f3, $args, '/admin');
			}

			$user->username = $req_username;
			$user->save();
		}

		if($req_password) {
			$user->password = password_hash($req_password, PASSWORD_DEFAULT);
			$user->save();
		}

		if($req_username) {
			// Update the session name so user doesn't get auto logged out.
			$f3->set('SESSION.session_username', $req_username);
		}

		$f3->push('v_confirmations', array(
			'element_id' => 'admin_confirmations',
			'message' => 'Your settings have been saved.'
		));
		Utils::reroute_with_confirmations($f3, $args, '/admin');
	}
}