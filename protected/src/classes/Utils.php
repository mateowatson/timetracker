<?php
class Utils {
	static function prevent_csrf($f3, $args) {
		$request_csrf = $f3->get('POST.csrf');
		$session_csrf = $f3->get('SESSION.csrf');
		if (empty($request_csrf) || empty($session_csrf) ||
			$request_csrf !== $session_csrf)
		{
			$f3->reroute('/login');
			return;
		}
	}

	static function prevent_csrf_on_logout($f3, $args) {
		$request_csrf = $f3->get('POST.csrf');
		$session_csrf = $f3->get('SESSION.csrf');
		if (empty($request_csrf) || empty($session_csrf) ||
			$request_csrf !== $session_csrf)
		{
			$f3->reroute('/confirm-logout');
			return;
		}
	}

	static function prevent_csrf_from_tab_conflict($f3, $args, $redirect_path) {
		$request_csrf = $f3->get('POST.csrf');
		$session_csrf = $f3->get('SESSION.csrf');
		if (empty($request_csrf) || empty($session_csrf) ||
			$request_csrf !== $session_csrf)
		{
			$f3->push('v_errors', array(
				'element_id' => 'csrf_error',
				'message' => 'Your request may have failed due a conflict with another tab or window. Simply refresh the page on this tab to renew your session and try again.'
			));
			self::reroute_with_errors($f3, $args, $redirect_path);
			return;
		}
	}

	static function send_csrf($f3, $args) {
		$f3->set('CSRF', $f3->get('SESSION_INSTANCE')->csrf());
		$f3->copy('CSRF','SESSION.csrf');
	}

	static function redirect_logged_out_user($f3, $args) {
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($f3->get('DB'), 'users');
		$session_user = $db_users->load(array('username=?', $session_username));
		if(!$session_username || !$session_user) {
			$f3->reroute('/login');
			return;
		}
	}

	static function redirect_logged_in_user($f3, $args) {
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($f3->get('DB'), 'users');
		$session_user = $db_users->load(array('username=?', $session_username));
		if($session_username && $session_user) {
			$f3->reroute('/dashboard');
			return;
		}
	}

	static function reroute_with_errors($f3, $args, $reroute_path) {
		if(count($f3->get('v_errors'))>0) {
			$f3->set('SESSION.errors', json_encode($f3->get('v_errors')));
			$f3->set('SESSION.confirmations', json_encode($f3->get('v_confirmations')));
			$f3->reroute($reroute_path);
		}
	}

	static function reroute_with_confirmations($f3, $args, $reroute_path) {
		if(count($f3->get('v_confirmations'))>0) {
			$f3->set('SESSION.confirmations', json_encode($f3->get('v_confirmations')));
			$f3->set('SESSION.errors', json_encode($f3->get('v_errors')));
			$f3->reroute($reroute_path);
		}
	}

	static function chunk_datetime($time) {
		$parts['yyyy'] = substr($time, 0, 4);
		$parts['mm'] = substr($time, 5, 2);
		$parts['dd'] = substr($time, 8, 2);
		$parts['hour'] = substr($time, 11, 2);
		$parts['min'] = substr($time, 14, 2);
		$parts['sec'] = substr($time, 17, 2);

		return $parts;
	}

	/**
	 * Takes array of 6 strings to return a sql datetime stamp/string
	 * 
	 * @param array $time Array of datetime strings in order.
	 * [''YYYY',DD','MM','hh','mm','ss']
	 * @return string SQL valid datetime stamp, eg. 2019-07-04 12:02:19
	 */
	static function datetime_chunks_to_sql($time) {
		if(gettype($time) !== 'array') return false;
		if(count($time) < 6) return false;
	
		$unchunked = '';
		
		foreach($time as $i => $unit) {
			if(gettype($unit) !== 'string') return false;
			if(!is_numeric($unit)) return false;
			// Year must be 4 characters
			if($i === 0 && iconv_strlen($unit) !== 4) return false;
			// All other units must be 2 characters
			if($i !== 0 && iconv_strlen($unit) !== 2) return false;
	
			if($i < 2) $unchunked .= $unit . '-';
			if($i === 2) $unchunked .= $unit . ' ';
			if($i > 2 && $i < 5) $unchunked .= $unit . ':';
			if($i === 5) $unchunked .= $unit;
		}
	
		return $unchunked;
	}

	/**
	 * Converts range in seconds to hours:minutes:seconds format.
	 * 
	 * @param string|integer $seconds Only numeric characters if string
	 * @return string Formattted time diff, eg 12:02:19
	 */
	static function timediff_from_seconds($seconds) {
		$hr_min_s = array(
			$seconds/60/60,
			$seconds/60 - (intval($seconds/60/60) * 60),
			$seconds - (intval($seconds/60) * 60)
		);

		$pretty_hr_min_s = array_map(function($unit) {
			return intval($unit) < 10 ? '0'.intval($unit) : intval($unit);
		}, $hr_min_s);
		
		return $pretty_hr_min_s[0].':'.$pretty_hr_min_s[1].':'.$pretty_hr_min_s[2];
	}

	/**
	 * Converts a SQL-formatted datetime to display markup.
	 * 
	 * @param string $formatted_datetime, a SQL-formatted datetime (eg, "Sep 2, 2019 07:11:46 PM")
	 * @return string The datetime converted into markup for display
	 */
	static function formatted_datetime_to_html($formatted_datetime) {
		// if this is an empty datetime, just show an em dash
		if (!$formatted_datetime) {
			return '—';
		}

		$datetime = date_create($formatted_datetime);
		return sprintf(
			'<span class="formattedCell"><span>%s&nbsp;%s</span><span class="formattedCell_date">%s</span>',
			date_format($datetime, 'g:i:s'),
			date_format($datetime, 'A'),
			date_format($datetime, 'M j, Y')
		);
	}

	/**
	 * Decodes HTML entities. This function is intended to be used in templates.
	 * 
	 * @param string $html_with_entities, a string that contains HTML entities
	 * @return string The given string with HTML entities decoded
	 */
	static function html($html_with_entities) {
		return html_entity_decode($html_with_entities);
	}

	/**
	 * Generates the global f3 array variable with the logs to show in the logs table of a
	 * given template/class.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param int $user_id The base Fat Free Framework instance.
	 * @param string $conditions Conditions to pass into the SQL statement. Must start
	 * with 'AND'. It should be a valid clause, so you'll need to read the code for this
	 * function in order to pass in a valid condition string.
	 * @param bool $is_team_filter Is this for a team logs table?
	 * @param bool $paginate Whether to paginate the SQL query by 10 per page.
	 * @param int|null $page_offset How many records to offset. Only relevant if $paginate is
	 * set to `true` and you need the offset to be more than 0.
	 * @return bool true on success, false on failure.
	 */
	static function set_v_logs(
		$f3,
		$user_id = null,
		$conditions = '',
		$is_team_filter = false,
		$paginate = false,
		$page_offset = null
	) {
		if($user_id === null) {
			return false;
		}
			
		if($paginate && $page_offset === null) {
			$page_offset = 0;
		}

		$db = $f3->get('DB');

		$query_string = '
			SELECT
				logs.notes, logs.start_time, logs.end_time, logs.id, logs.team_id,
				projects.name AS project_name,
				tasks.name AS task_name,
				users.username,
				IF(logs.end_time != "0000-00-00 00:00:00",
					TIMESTAMPDIFF(SECOND, logs.start_time, logs.end_time),
					TIMESTAMPDIFF(SECOND, logs.start_time, NOW())
				)
				as time_sum,
				CONCAT(
					DATE_FORMAT(
						DATE(logs.start_time),
						"%b %e, %Y"
					),
					" ",
					TIME_FORMAT(
						TIME(logs.start_time),
						"%r"
					)
				) as start_time_formatted,
				CONCAT(
					DATE_FORMAT(
						DATE(logs.end_time),
						"%b %e, %Y"
					),
					" ",
					TIME_FORMAT(
						TIME(logs.end_time),
						"%r"
					)
				) as end_time_formatted
			FROM logs
			LEFT JOIN projects
				ON logs.project_id = projects.id
			LEFT JOIN tasks
				ON logs.task_id = tasks.id
			LEFT JOIN users
				ON logs.user_id = users.id
			WHERE '. (!$is_team_filter ? 'user_id = ? AND logs.team_id IS NULL ' : 'TRUE ').
			$conditions.
			' ORDER BY start_time DESC';

		if($paginate) {
			$query_string .= ' LIMIT ?, 10';

			if(!$is_team_filter) {
				$logs = $db->exec($query_string, array(
					$user_id,
					$page_offset
				));
			} else {
				$logs = $db->exec($query_string, array(
					$page_offset
				));
			}
			

			foreach($logs as $idx => $log) {
				$logs[$idx]['start_time_formatted'] = self::formatted_datetime_to_html($logs[$idx]['start_time_formatted']);
				$logs[$idx]['end_time_formatted'] = self::formatted_datetime_to_html($logs[$idx]['end_time_formatted']);
				$logs[$idx]['time_sum'] = self::timediff_from_seconds($log['time_sum']);
			}
			$f3->set('v_logs', $logs);

			return true;
		}

		$logs = $db->exec($query_string, array(
			$user_id
		));

		foreach($logs as $idx => $log) {
			$logs[$idx]['start_time_formatted'] = self::formatted_datetime_to_html($logs[$idx]['start_time_formatted']);
			$logs[$idx]['end_time_formatted'] = self::formatted_datetime_to_html($logs[$idx]['end_time_formatted']);
			$logs[$idx]['time_sum'] = self::timediff_from_seconds($log['time_sum']);
		}
		$f3->set('v_logs', $logs);

		return true;
	}

	/**
	 * Generates the global f3 variable with the total time of all the logs in the current
	 * table.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param int $user_id The user id.
	 * @param string $conditions Conditions to pass into the SQL statement. Must start
	 * with 'AND'. It should be a valid clause, so you'll need to read the code for this
	 * function in order to pass in a valid condition string.
	 * @param bool $is_team_filter Is this for a team logs table?
	 * @return bool true on success, false on failure.
	 */
	static function set_v_logs_total_time(
		$f3,
		$user_id = null,
		$conditions = '',
		$is_team_filter = false
	) {
		if($user_id === null) {
			return false;
		}

		$db = $f3->get('DB');

		$logs_total_time = $db->exec('
			SELECT
				SUM(
					IF(logs.end_time != "0000-00-00 00:00:00",
						TIMESTAMPDIFF(SECOND, logs.start_time, logs.end_time),
						TIMESTAMPDIFF(SECOND, logs.start_time, NOW())
					)
				) as total_time
			FROM logs
			WHERE '. (!$is_team_filter ? 'user_id = ? AND logs.team_id IS NULL ' : 'TRUE ').
			$conditions.' ORDER BY start_time DESC
		', array($user_id));
		
		$f3->set(
			'v_logs_total_time',
			self::timediff_from_seconds($logs_total_time[0]['total_time'])
		);

		return true;
	}

	/**
	 * Parses user date or date range input for use in SQL
	 * 
	 * @param string $input The user date or date range input.
	 * @return array Returns array where first element is begin date and second is end
	 * date
	 */
	static function parse_search_by_date_input($input) {
		$date_search_arr = explode('to', $input);
		if(count($date_search_arr) > 2) {
			return false;
		}
		for($i = 0; $i < count($date_search_arr); $i++) {
			$date_search_arr[$i] = trim($date_search_arr[$i]);
			$date_fields_arr = explode('/', $date_search_arr[$i]);
			if(
				count($date_fields_arr) !== 3 ||
				!preg_match('/^\d\d\d\d$/', $date_fields_arr[2]) ||
				!preg_match('/^\d\d$/', $date_fields_arr[1]) ||
				!preg_match('/^\d\d$/', $date_fields_arr[0])
			) {
				return false;
			}
			$date_search_arr[$i] = $date_fields_arr[2].'-'.
			$date_fields_arr[0].'-'.$date_fields_arr[1];

			if($i === 0) {
				$date_search_arr[$i] .= ' 00:00:00';
			}

			if($i === 1) {
				$date_search_arr[$i] .= ' 23:59:59';
			}
		}

		if(count($date_search_arr) === 1) {
			$date_search_arr[1] = str_replace('00:00:00', '23:59:59', $date_search_arr[0]);
		}

		return $date_search_arr;
	}

	/**
	 * Returns team list associative array of current session user:
	 * ['team_name', 'team_id', 'creator'].
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @return array Returns team list associative array of current session user:
	 * ['team_name', 'team_id', 'creator']
	 */
	static function get_all_teams_of_logged_in_user($f3) {
		$teams = array();
		$team_ids = array();
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $user->username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);
		$db_teams = new \DB\SQL\Mapper($db, 'teams');
		$db_teams->load(array('creator = ?', $user->id));
		while(!$db_teams->dry()) {
			array_push($teams, array(
				'team_name' => $db_teams->name,
				'team_id' => $db_teams->id,
				'creator' => true
			));
			array_push($team_ids, $db_teams->id);
			$db_teams->next();
		}
		$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
		$db_users_teams->load(array('user_id = ?', $user->id));
		while(!$db_users_teams->dry()) {
			$db_teams->reset();
			$db_teams->load(array('id = ?', $db_users_teams->team_id));
			if(!in_array($db_users_teams->team_id, $team_ids)) {
				array_push($teams, array(
					'team_name' => $db_teams->name,
					'team_id' => $db_users_teams->team_id,
					'creator' => false
				));
				array_push($team_ids, $db_users_teams->id);
			}
			
			$db_users_teams->next();
		}

		return $teams;
	}

	/**
	 * Validates username by adding any errors to the 'v_errors' global F3 variable. It
	 * does not call reroute_with_errors; that is up to you.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param string $username The desired username.
	 * @param string $error_type What v_errors element_id to use.
	 * @return null Returns NULL
	 */
	static function validate_username($f3, $username, $error_type) {
		$db = $f3->get('DB');
		$users = $db->exec('SHOW TABLES LIKE \'users\'');
		if(count($users)) {
			$db_users = new \DB\SQL\Mapper($db, 'users');
			$user = $db_users->load(array('username=?', $username));
		} else {
			$user = FALSE;
		}

		if($user !== FALSE) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'The username '.$username.' is already taken.'
			));
		}

		if(!ctype_alnum($username)) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'The username must use only alphanumeric characters. You provided '.$username.'.'
			));
		}

		if(strlen($username) > 25) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'The username must not be more than 25 characters long.'
			));
		}
	}

	/**
	 * Validates password by adding any errors to the 'v_errors' global F3 variable. It
	 * does not call reroute_with_errors; that is up to you.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param string $password The desired password.
	 * @param string $error_type What v_errors element_id to use.
	 * @return null Returns NULL
	 */
	static function validate_password($f3, $password, $error_type) {
		if(strlen($password) > 75) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'The password must not be more than 75 characters long.'
			));
		}

		if(strlen($password) < 8) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'The password must be at least 8 characters long.'
			));
		}
	}

	/**
	 * Validates email by adding any errors to the 'v_errors' global F3 variable. It
	 * does not call reroute_with_errors; that is up to you.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param string $email The desired email.
	 * @param string $error_type What v_errors element_id to use.
	 * @return null Returns NULL
	 */
	static function validate_email($f3, $email, $error_type) {
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
			$f3->push('v_errors', array(
				'element_id' => $error_type,
				'message' => 'Not a valid email address.'
			));
		}
	}

	static function send_email_verification($f3, $email, $username, $error_type) {
		if($f3->get('SMTP_SCHEME') !== 'tls' || $f3->get('SMTP_SCHEME') !== 'ssl') {
			$scheme = null;
		} else {
			$scheme = $f3->get('SMTP_SCHEME');
		}

		$smtp = new SMTP (
			$f3->get('SMTP_HOST'),
			$f3->get('SMTP_PORT'),
			$scheme,
			$f3->get('SMTP_USERNAME'),
			$f3->get('SMTP_PASSWORD')
		);

		$site_name = $f3->get('SITE_NAME');
		$site_url = $f3->get('SITE_URL');

		$smtp->set('From', '<'.$f3->get('SMTP_USERNAME').'>');
		$smtp->set('To', '<'.$email.'>');
		$smtp->set('Subject', $site_name.' email verification');

		// creates 12 digit random string
		$email_verification = bin2hex( random_bytes(6) );

		$email_verification_hash = password_hash($email_verification, PASSWORD_DEFAULT);


		$message = <<<MESSAGE
Hello $username,

Your email verification code is:
	
	$email_verification

Log in at $site_url/login with the username:

	$username

Log in with the password you signed up with.

Be sure to log in and go to $site_url/verify-email and enter the verification code. This will allow you to reset your password if you ever forget it.

Sincerely,
The $site_name Team
MESSAGE;

		if($smtp->send($message)) {
			return $email_verification_hash;
		}

		$f3->push('v_errors', array(
			'element_id' => $error_type,
			'message' => 'Could not send email verification. Registration failed.'
		));
	}

	static function send_email_to_added_user($f3, $email, $username, $error_type) {
		if($f3->get('SMTP_SCHEME') !== 'tls' || $f3->get('SMTP_SCHEME') !== 'ssl') {
			$scheme = null;
		} else {
			$scheme = $f3->get('SMTP_SCHEME');
		}

		$smtp = new SMTP (
			$f3->get('SMTP_HOST'),
			$f3->get('SMTP_PORT'),
			$scheme,
			$f3->get('SMTP_USERNAME'),
			$f3->get('SMTP_PASSWORD')
		);

		$site_name = $f3->get('SITE_NAME');
		$site_url = $f3->get('SITE_URL');

		$smtp->set('From', '<'.$f3->get('SMTP_USERNAME').'>');
		$smtp->set('To', '<'.$email.'>');
		$smtp->set('Subject', 'Welcome to '.$site_name);


		$message = <<<MESSAGE
Hello $username,

You have been added to $site_name, available at $site_url.

Log in at $site_url/login with the username:

	$username

Ask your site administrator for your password, or go to $site_url/forgot-password and follow the steps to reset your password.

Sincerely,
The $site_name Team
MESSAGE;

		if($smtp->send($message)) {
			return true;
		}

		$f3->push('v_errors', array(
			'element_id' => $error_type,
			'message' => 'Could not send welcome email to new user.'
		));

		return false;
	}

	static function send_password_reset_verification($f3, $email, $username, $error_type) {
		if($f3->get('SMTP_SCHEME') !== 'tls' || $f3->get('SMTP_SCHEME') !== 'ssl') {
			$scheme = null;
		} else {
			$scheme = $f3->get('SMTP_SCHEME');
		}

		$smtp = new SMTP (
			$f3->get('SMTP_HOST'),
			$f3->get('SMTP_PORT'),
			$scheme,
			$f3->get('SMTP_USERNAME'),
			$f3->get('SMTP_PASSWORD')
		);

		$smtp->set('From', '<'.$f3->get('SMTP_USERNAME').'>');
		$smtp->set('To', '<'.$email.'>');
		$smtp->set('Subject', 'Timetracker password reset verification');

		// creates 12 digit random string
		$pasword_reset_verification = bin2hex( random_bytes(6) );

		$password_reset_verification_hash = password_hash($pasword_reset_verification, PASSWORD_DEFAULT);

		$site_name = $f3->get('SITE_NAME');
		$site_url = $f3->get('SITE_URL');

		$message = <<<MESSAGE
Hello $username,

Your password reset verification is code is: $pasword_reset_verification

Go to $site_url/reset-password and enter the verification code and new password you would like to use.

Sincerely,
The $site_name Team
MESSAGE;

		if($smtp->send($message)) {
			return $password_reset_verification_hash;
		}

		$f3->push('v_errors', array(
			'element_id' => $error_type,
			'message' => 'Could not send email verification. Password reset process failed.'
		));
	}

	static function get_project_and_task_lists($is_team, $team, $db, $user) {
		// GET PROJECT AND TASK LISTS
		if(!$is_team) {
			$projectsQuery = $db->exec('
				SELECT * FROM projects
				LEFT JOIN users_projects
					ON (users_projects.user_id = ? AND
					users_projects.project_id = projects.id)
				LEFT JOIN logs ON TRUE
				WHERE users_projects.user_id IS NOT NULL 
					AND logs.team_id IS NULL AND
					logs.project_id = projects.id
				ORDER BY logs.start_time DESC
			', array($user->id));
			$tasksQuery = $db->exec('
				SELECT * FROM tasks
				LEFT JOIN users_tasks
					ON (users_tasks.user_id = ? AND
					users_tasks.task_id = tasks.id)
				LEFT JOIN logs ON TRUE
				WHERE users_tasks.user_id IS NOT NULL
					AND logs.team_id IS NULL AND
					logs.task_id = tasks.id
				ORDER BY logs.start_time DESC
			', array($user->id));
		} else {
			$projectsQuery = $db->exec('
				SELECT * FROM projects
				LEFT JOIN users_projects
					ON users_projects.project_id = projects.id
				LEFT JOIN logs ON TRUE
				WHERE users_projects.user_id IS NOT NULL 
					AND logs.team_id = ? AND
					logs.project_id = projects.id
				ORDER BY logs.start_time DESC
			', array($team['id']));
			$tasksQuery = $db->exec('
				SELECT * FROM tasks
				LEFT JOIN users_tasks
					ON users_tasks.task_id = tasks.id
				LEFT JOIN logs ON TRUE
				WHERE users_tasks.user_id IS NOT NULL AND
					logs.team_id = ? AND
					logs.task_id = tasks.id
				ORDER BY logs.start_time DESC
			', array($team['id']));
		}
		$projects = array();
		$tasks = array();
		foreach($projectsQuery as $query) {
			$is_dup = false;
			foreach($projects as $project) {
				if($project['id'] === $query['project_id']) {
					$is_dup = true;
				}
			}
			if(!$is_dup) {
				array_push($projects, array(
					'id' => $query['project_id'],
					'name' => $query['name'],
					'preselect_in_dropdown' => false,
					'preselect_in_report_dropdown' => false,
					'preselect_in_search_dropdown' => false
				));
			}
		}
		foreach($tasksQuery as $query) {
			$is_dup = false;
			foreach($tasks as $task) {
				if($task['id'] === $query['task_id']) {
					$is_dup = true;
				}
			}
			if(!$is_dup) {
				array_push($tasks, array(
					'id' => $query['task_id'],
					'name' => $query['name'],
					'preselect_in_dropdown' => false,
					'preselect_in_report_dropdown' => false,
					'preselect_in_search_dropdown' => false
				));
			}
		}

		return array(
			'tasks' => $tasks,
			'projects' => $projects
		);
	}

	static function get_migration_query_string($db_name) {
		return <<<MIGRATION
SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `$db_name`;

CREATE TABLE `site_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_key` text NOT NULL,
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `logs_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `logs_ibfk_4` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `creator` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verification_hash` text DEFAULT NULL,
  `password_reset_verification_hash` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `users_projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `users_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `users_teams_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_teams_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `admins_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `users_teams_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_teams_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
MIGRATION;

	}
}
