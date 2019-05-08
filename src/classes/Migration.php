<?php
class Migration {
	function show($f3, $args) {
		//echo 'Hello World';
		$view=new View;
        echo $view->render('migration.php');
	}

	function beforeRoute($f3, $args) {
		//$f3->reroute('/');
	}

	function migrate($f3, $args) {
		Utils::prevent_csrf($f3, $args);

		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];
		error_log('ssssss: ');
		error_log('ssssss: ');

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'installation_errors',
				'message' => 'Please fill out all the user creation fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/migration');

		$db = $f3->get('DB');
		
		$migration = require_once(ROOT_DIR . '/migrations/Migration001.php');

		$db->exec($migration);

		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $request_user));

		
		if(!$user->dry()) {
			$f3->push('v_errors', array(
				'element_id' => 'installation_errors',
				'message' => 'The database must be empty.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/migration');

		$db->exec(
			'INSERT INTO users (username, password) VALUES (?, ?)',
			array(
				$request_user,
				password_hash($request_password, PASSWORD_DEFAULT)
			)
		);

		$db->commit();

		$f3->push('v_confirmations', array(
			'element_id' => 'installation_confirmations',
			'message' => 'Timetracker was installed successfully!'
		));

		$f3->reroute('/login');
	}

	function getLatestMigration() {
		$migrations = [];
		foreach (new FilesystemIterator(ROOT_DIR . '/migrations') as $file) {
			$migrations[] = $file->getBasename('.php');
		}
		return $migrations[count($migrations) - 1];
	}
}