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
$f3->set(
	'SESSION_INSTANCE',
	new \DB\SQL\Session(
		$f3->get('DB'),
		'sessions',
		true,
		function($session){
			// Suspect session
			/* $f3 = \Base::instance();
			if ($session->ip() != $f3->get('IP'))
				return true;

			// The default behaviour destroys the suspicious session.
			return false; */
			return true;
		}
	)
);
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

// Set is email enabled global variable
if(
	$f3->get('SMTP_HOST') &&
	$f3->get('SMTP_USERNAME') &&
	$f3->get('SMTP_PASSWORD') &&
	$f3->get('SMTP_PORT')
) {
	$f3->set('EMAIL_ENABLED', true);
} else {
	$f3->set('EMAIL_ENABLED', false);
}

// Remove trailing slash(es) of site name
$original_site_url = $f3->get('SITE_URL');
$f3->set('SITE_URL', rtrim($original_site_url, '/'));


$db = $f3->get('DB');

// Sync PHP and db timezone to admin-defined global
// Credit: https://www.sitepoint.com/synchronize-php-mysql-timezone-configuration/
if($f3->get('SITE_TIMEZONE')) {
	define('TIMEZONE', $f3->get('SITE_TIMEZONE'));
} else {
	define('TIMEZONE', 'America/Chicago');
}
date_default_timezone_set(TIMEZONE);
$tz_now = new DateTime();
$tz_mins = $tz_now->getOffset() / 60;
$tz_sgn = ($tz_mins < 0 ? -1 : 1);
$tz_mins = abs($tz_mins);
$tz_hrs = floor($tz_mins / 60);
$tz_mins -= $tz_hrs * 60;
$tz_offset = sprintf('%+d:%02d', $tz_hrs*$tz_sgn, $tz_mins);
$db->exec("SET time_zone='$tz_offset';");

// Ensure zero dates are enabled
// Solution from: https://stackoverflow.com/questions/60186508/how-to-remove-no-zero-date-from-sql-mode-in-mysql
$db->exec("SET @@sql_mode := REPLACE(@@sql_mode, 'NO_ZERO_DATE', '');");

// prevent only full group by error
// Solution from: https://stackoverflow.com/questions/36207042/error-code-1055-incompatible-with-sql-mode-only-full-group-by#36207611
$db->exec("SET @@sql_mode := REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '');");

$users = $db->exec('SHOW TABLES LIKE \'users\'');

if(!count($users) && $_SERVER['REQUEST_URI'] !== '/install') {
	$f3->reroute('/install');
}

if(!empty($f3->get('REQUEST')['invoice-number'])) {
	$f3->reroute('/');
}

$f3->set(
	'v_honeypot_id',
	'invoice-number'
);

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