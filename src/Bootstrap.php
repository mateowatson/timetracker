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

if($f3->get('SITE_ENV') === 'development') {
	$f3->set('DEBUG',3);
} else {
	$f3->set('DEBUG',0);
}

$f3->set('ONERROR', function($f3){
	$f3->set('v_page_title', 'Error');
	echo \View::instance()->render('error.php');
});

// Remove trailing slash(es) of site name
$original_site_url = $f3->get('SITE_URL');
$f3->set('SITE_URL', rtrim($original_site_url, '/'));


$db = $f3->get('DB');

// Sync PHP and db timezone to admin-defined global
// Credit: https://www.sitepoint.com/synchronize-php-mysql-timezone-configuration/
define('TIMEZONE', $f3->get('SITE_TIMEZONE'));
date_default_timezone_set(TIMEZONE);
$tz_now = new DateTime();
$tz_mins = $tz_now->getOffset() / 60;
$tz_sgn = ($tz_mins < 0 ? -1 : 1);
$tz_mins = abs($tz_mins);
$tz_hrs = floor($tz_mins / 60);
$tz_mins -= $tz_hrs * 60;
$tz_offset = sprintf('%+d:%02d', $tz_hrs*$tz_sgn, $tz_mins);
$db->exec("SET time_zone='$tz_offset';");

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