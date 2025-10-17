<?php
class Migration {
	function show($f3, $args) {
		Utils::send_csrf($f3, $args);
		$f3->set('v_page_title', 'Install');
		$view=new View;
        echo $view->render('migration.php');
	}

	function beforeRoute($f3, $args) {
		$db = $f3->get('DB');
		$users = $db->exec('SHOW TABLES LIKE \'users\'');

		if(count($users)) {
			$f3->reroute('/');
		}
	}

	function migrate($f3, $args) {
		Utils::prevent_csrf_from_tab_conflict($f3, $args, '/install');

		$request_user = $f3->get('REQUEST')['username'] ?? null;
		$request_email = $f3->get('REQUEST')['email'] ?? null;
		$request_password = $f3->get('REQUEST')['password'] ?? null;

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'installation_errors',
				'message' => 'Please fill out at least the username and password fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/migration');

		$db = $f3->get('DB');

		Utils::validate_username($f3, $request_user, 'installation_errors');

		Utils::validate_password($f3, $request_password, 'installation_errors');

		if($request_email && $f3->get('EMAIL_ENABLED')) {
			Utils::validate_email($f3, $request_email, 'installation_errors');
		} else {
			$request_email = false;
		}
		
		Utils::reroute_with_errors($f3, $args, '/install');
		
		$migration = Utils::get_migration_query_string($f3->get('DB_NAME'));

		$db->begin();

		$db->exec($migration);

		$db->exec(
			'INSERT INTO users (username, password, admin, email) VALUES (?, ?, 1, ?)',
			array(
				$request_user,
				password_hash($request_password, PASSWORD_DEFAULT),
				$request_email ? : null
			)
		);

		$db->exec(
			'INSERT INTO site_options (option_key, option_value) VALUES (\'open_registration\', \'false\')'
		);

		$db->commit();

		if($request_email && $f3->get('EMAIL_ENABLED')) {
			$email_verification_hash = Utils::send_email_verification($f3, $request_email, $request_user, 'installation_errors');
			Utils::reroute_with_errors($f3, $args, '/install');
			$db = $f3->get('DB');
			$user = new \DB\SQL\Mapper($db, 'users');
			$user->load(array('username=?', $request_user));
			$user->email_verification_hash = $email_verification_hash;
			$user->email_verification_hash_expires = time() + (60 * 60 * 24);
			$user->save();
		}

		$f3->push('v_confirmations', array(
			'element_id' => 'installation_confirmations',
			'message' => $f3->get('SITE_NAME').' was installed successfully!'
		));

		Utils::reroute_with_confirmations($f3, $args, '/login');
	}

	function getLatestMigration() {
		$migrations = [];
		foreach (new FilesystemIterator(ROOT_DIR . '/migrations') as $file) {
			$migrations[] = $file->getBasename('.php');
		}
		return $migrations[count($migrations) - 1];
	}
}