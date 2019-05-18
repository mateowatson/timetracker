<?php

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

$f3 = \Base::instance();

$f3->config(ROOT_DIR . '/setup.cfg');
$f3->config(ROOT_DIR . '/routes.cfg');

$f3->set('DB', new \DB\SQL(
	'mysql:host='.$f3->get('DB_HOST').
		';port='.$f3->get('DB_PORT').
		';dbname='.$f3->get('DB_NAME'),
	$f3->get('DB_USER'),
	$f3->get('DB_PASSWORD')
));
$f3->set('SESSION_INSTANCE', new \DB\SQL\Session($f3->get('DB')));
$f3->set('UI', ROOT_DIR . '/templates/');
$f3->set('AUTOLOAD', ROOT_DIR . '/src/classes/');
$f3->set('DEBUG',3);

$db = $f3->get('DB');
$users = $db->exec('SHOW TABLES LIKE \'users\'');

if(!count($users) && $_SERVER['REQUEST_URI'] !== '/install') {
	$f3->reroute('/install');
}

// Capture errors in memory
$f3->set('v_errors', json_decode($f3->get('SESSION.errors')) ? : array());
// Creating v_errors_element_ids, simply for ease of checking quickly if there are any
// errors, especially for use in the template files.
foreach ($f3->get('v_errors') as $error) {
	$element_ids = array();
	array_push($element_ids, $error->element_id);
	$f3->set('v_errors_element_ids', array_unique($element_ids));
}
// Capture confirmations in memory
$f3->set('v_confirmations', json_decode($f3->get('SESSION.confirmations')) ? : array());
// Creating v_confirmations_element_ids, simply for ease of checking quickly if there are any
// confirmations, especially for use in the template files.
foreach ($f3->get('v_confirmations') as $confirmation) {
	$element_ids = array();
	array_push($element_ids, $confirmation->element_id);
	$f3->set('v_confirmations_element_ids', array_unique($element_ids));
}
// Reset errors in db
$f3->set('SESSION.errors', '');
$f3->set('SESSION.confirmations', '');



$f3->run();