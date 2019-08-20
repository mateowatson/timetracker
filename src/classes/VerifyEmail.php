<?php

class VerifyEmail {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
        Utils::send_csrf($f3, $args);
        $f3->set('v_page_title', 'Verify Email');

        // GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
        $f3->set('v_username', $session_username);
        $f3->set('v_user_email_verified', $user->email_verified);
        $f3->set('v_user_email', $user->email ? : 'no email address on record');
        // RENDER
		$view = new \View;
        echo $view->render('verify-email.php');
    }

    function verify_email($f3, $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::prevent_csrf_from_tab_conflict($f3, $args, '/verify-login');
        
        $req = $f3->get('REQUEST');
        $req_code = $req['email_verification_code'];
        
        // GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
        $user = $db_users->load(array('username=?', $session_username));
        
        if(password_verify($req_code, $user->email_verification_hash)) {
            $user->email_verified = 1;
            $user->save();
            $f3->push('v_confirmations', array(
				'element_id' => 'verify_email_confirmations',
				'message' => 'You have successfully verified your email address, '.$user->email.'!'
			));
			Utils::reroute_with_confirmations($f3, $args, '/verify-email');
		} else {
			$f3->push('v_errors', array(
				'element_id' => 'verify_email_errors',
				'message' => 'Email verification failed.'
			));
			Utils::reroute_with_errors($f3, $args, '/verify-email');
		}
    }
}