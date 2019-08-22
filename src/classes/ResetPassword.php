<?php

class ResetPassword {
    function show($f3, $args) {
        Utils::redirect_logged_in_user($f3, $args);
        Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Reset Password');
		$view=new \View;
        echo $view->render('reset-password.php');
    }

    function finish_password_reset($f3, $args) {
        $db = $f3->get('DB');
        Utils::prevent_csrf_from_tab_conflict($f3, $args, '/forgot-password');
        
        $request_username = $f3->get('REQUEST')['username'];
        $request_password_reset_code = $f3->get('REQUEST')['password_reset_code'];
        $request_password = $f3->get('REQUEST')['password'];

        if(!$request_username || !$request_password_reset_code || !$request_password) {
            $f3->push('v_errors', array(
				'element_id' => 'reset_password_errors',
				'message' => 'You must fill in all the fields.'
            ));
            Utils::reroute_with_errors($f3, $args, '/reset-password');
        }

        $user = new \DB\SQL\Mapper($db, 'users');
        $user->load(array('username=?', $request_username));

        if(!$user->password_reset_verification_hash) {
            $f3->push('v_errors', array(
				'element_id' => 'reset_password_errors',
				'message' => 'Your password is not eligible for reset. You must fill out the forgot password form available on the login page.'
            ));
            Utils::reroute_with_errors($f3, $args, '/reset-password');
        }

        if(password_verify($request_password_reset_code, $user->password_reset_verification_hash)) {
            $user->password = password_hash($request_password, PASSWORD_DEFAULT);
            $user->password_reset_verification_hash = '';
            $user->save();
            $f3->push('v_confirmations', array(
				'element_id' => 'reset_password_confirmations',
				'message' => 'You have successfully reset your password! You may now login.'
			));
			Utils::reroute_with_confirmations($f3, $args, '/login');
        } else {
            $f3->push('v_errors', array(
				'element_id' => 'reset_password_errors',
				'message' => 'Your password reset verification code was incorrect.'
            ));
            Utils::reroute_with_errors($f3, $args, '/reset-password');
        }
    }
}