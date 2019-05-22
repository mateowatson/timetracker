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

		$request_user = $f3->get('REQUEST')['username'];
		$request_password = $f3->get('REQUEST')['password'];

		if(!$request_user || !$request_password) {
			$f3->push('v_errors', array(
				'element_id' => 'installation_errors',
				'message' => 'Please fill out all the user creation fields.'
			));
		}

		Utils::reroute_with_errors($f3, $args, '/migration');

		$db = $f3->get('DB');
		
		$migration = require_once(ROOT_DIR . '/migrations/Migration001.php');

		$db->begin();

		$db->exec($migration);

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
			'message' => 'Time Tracker was installed successfully!'
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