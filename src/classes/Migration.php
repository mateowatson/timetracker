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
		$db = $f3->get('DB');
		/* var_dump(ROOT_DIR);
		return; */
		$migration = require_once(ROOT_DIR . '/migrations/Migration001.php');
		$db->begin();
		$db->exec($migration);

		// Demo users
		$db->exec('INSERT INTO users (username, password) VALUES ("matt", ?)', password_hash('password1', PASSWORD_DEFAULT));
		$db->exec('INSERT INTO users (username, password) VALUES ("tim", ?)', password_hash('password2', PASSWORD_DEFAULT));
		$db->exec('INSERT INTO users (username, password) VALUES ("blake", ?)', password_hash('password3', PASSWORD_DEFAULT));
		$db->exec('INSERT INTO users (username, password) VALUES ("anna", ?)', password_hash('password4', PASSWORD_DEFAULT));
		$db->commit();
		echo 'Success';
	}

	function getLatestMigration() {
		$migrations = [];
		foreach (new FilesystemIterator(ROOT_DIR . '/migrations') as $file) {
			$migrations[] = $file->getBasename('.php');
		}
		return $migrations[count($migrations) - 1];
	}
}