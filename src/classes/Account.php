<?php

class Account {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Account');
		
		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

		$f3->set('v_is_user_admin', true);
		if(!$user->admin) {
			$f3->set('v_is_user_admin', false);
		}

		$site_options = new \DB\SQL\Mapper($db, 'site_options');
		$open_registration = $site_options->load(array('option_key = \'open_registration\''));
		if($open_registration->option_value === 'false') {
			$f3->set('v_open_registration', false);
		} else {
			$f3->set('v_open_registration', true);
		}
		
		// RENDER
		$view = new \View;
        echo $view->render('account.php');
	}

	function post_account_settings($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/account');

		$req = $f3->get('REQUEST');
		$req_username = $req['account_username'];
		$req_password = $req['account_password'];
		$req_registration = $req['account_registration'];
		$req_add_username = $req['account_add_username'];
		$req_add_password = $req['account_add_password'];

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));

		if($req_add_username || $req_add_password || $req_registration) {
			// Kick out fake admins
			if($user->admin !== 1) {
				$f3->push('v_errors', array(
					'element_id' => 'account_errors',
					'message' => 'You do not have permissions to perform this action.'
				));

				Utils::reroute_with_errors($f3, $args, '/account');
			}
		}

		if($req_add_username || $req_add_password) {
			if(!$req_add_username || !$req_add_password) {
				$f3->push('v_errors', array(
					'element_id' => 'account_errors',
					'message' => 'Could not add new user because all relevant fields were not completed.'
				));

				Utils::reroute_with_errors($f3, $args, '/account');
			}

			Utils::validate_username($f3, $req_add_username, 'account_errors');

			Utils::validate_password($f3, $req_add_password, 'account_errors');

			Utils::reroute_with_errors($f3, $args, '/account');
		}

		if($req_username) {
			Utils::validate_username($f3, $req_username, 'account_errors');
			Utils::reroute_with_errors($f3, $args, '/account');
			$user->username = $req_username;
		}

		if($req_password) {
			Utils::validate_password($f3, $req_password, 'account_errors');
			Utils::reroute_with_errors($f3, $args, '/account');
			$user->password = password_hash($req_password, PASSWORD_DEFAULT);
		}

		$site_options = new \DB\SQL\Mapper($db, 'site_options');
		$open_registration = $site_options->load(array('option_key = \'open_registration\''));
		if($req_registration && $req_registration === 'closed') {
			$open_registration->option_value = 'false';
		} else if($req_registration && $req_registration === 'open') {
			$open_registration->option_value = 'true';
		} else if($req_registration) {
			$f3->push('v_errors', array(
				'element_id' => 'account_errors',
				'message' => 'Sorry, you did not submit a valid "Registration of new users" option.'
			));
			Utils::reroute_with_errors($f3, $args, '/account');
		}


		if($req_add_username && $req_add_password) {
			$new_user = $db->exec(
				'INSERT INTO users (username, password) VALUES (?, ?)',
				array(
					$req_add_username,
					password_hash($req_add_password, PASSWORD_DEFAULT)
				)
			);

			if($new_user === 1) {
				$f3->push('v_confirmations', array(
					'element_id' => 'account_confirmations',
					'message' => 'You have successfully added a new user!'
				));
			} else {
				$f3->push('v_errors', array(
					'element_id' => 'account_errors',
					'message' => 'Your request failed.'
				));
				Utils::reroute_with_errors($f3, $args, '/account');
			}
		}

		if($req_username) {
			// Update the session name so user doesn't get auto logged out.
			$f3->set('SESSION.session_username', $req_username);
		}

		if($req_registration) {
			$open_registration->save();
		}

		if($req_username || $req_password) {
			$user->save();
		}

		$f3->push('v_confirmations', array(
			'element_id' => 'account_confirmations',
			'message' => 'Your settings have been saved.'
		));
		Utils::reroute_with_confirmations($f3, $args, '/account');
	}
}