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
			$f3->reroute($reroute_path);
		}
	}

	static function reroute_with_confirmations($f3, $args, $reroute_path) {
		if(count($f3->get('v_confirmations'))>0) {
			$f3->set('SESSION.confirmations', json_encode($f3->get('v_confirmations')));
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
	 * Generates the global f3 array variable with the logs to show in the logs table of a
	 * given template/class.
	 * 
	 * @param Base $f3 The base Fat Free Framework instance.
	 * @param int $user_id The base Fat Free Framework instance.
	 * @param string $conditions Conditions to pass into the SQL statement. Must start
	 * with 'AND'. It should be a valid clause, so you'll need to read the code for this
	 * function in order to pass in a valid condition string.
	 * @param bool $paginate Whether to paginate the SQL query by 10 per page..
	 * @param int|null $page_offset How many records to offset. Only relevant if $paginate is
	 * set to `true` and you need the offset to be more than 0.
	 * @return bool true on success, false on failure.
	 */
	static function set_v_logs(
		$f3,
		$user_id = null,
		$conditions = '',
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
				logs.notes, logs.start_time, logs.end_time, logs.id,
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
			WHERE user_id = ? '.$conditions.'
			ORDER BY start_time DESC';

		if($paginate) {
			$query_string .= ' LIMIT ?, 10';

			$logs = $db->exec($query_string, array(
				$user_id,
				$page_offset
			));

			foreach($logs as $idx => $log) {
				$logs[$idx]['time_sum'] = self::timediff_from_seconds($log['time_sum']);
			}
			$f3->set('v_logs', $logs);

			return true;
		}

		$logs = $db->exec($query_string, array(
			$user_id
		));

		foreach($logs as $idx => $log) {
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
	 * @param int $user_id The base Fat Free Framework instance.
	 * @param string $conditions Conditions to pass into the SQL statement. Must start
	 * with 'AND'. It should be a valid clause, so you'll need to read the code for this
	 * function in order to pass in a valid condition string.
	 * @return bool true on success, false on failure.
	 */
	static function set_v_logs_total_time(
		$f3,
		$user_id = null,
		$conditions = ''
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
			WHERE user_id = ? '.$conditions.'
			ORDER BY start_time DESC
		', array($user_id));
		
		$f3->set(
			'v_logs_total_time',
			self::timediff_from_seconds($logs_total_time[0]['total_time'])
		);

		return true;
	}
}
