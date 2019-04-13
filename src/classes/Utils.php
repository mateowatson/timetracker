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
}
