<?php

class ForgotPassword {
    function show($f3, $args) {
        Utils::redirect_logged_in_user($f3, $args);
        Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Reset Password');
		$view=new \View;
        echo $view->render('forgot-password.php');
    }

    function start_password_reset($f3, $args) {
        $db = $f3->get('DB');
        Utils::prevent_csrf_from_tab_conflict($f3, $args, '/forgot-password');
        
        $db_users = new \DB\SQL\Mapper($db, 'users');
		$request_user = $f3->get('REQUEST')['username'];
        $user = $db_users->load(array('username=?', $request_user));
        
        if($user->email && $user->email_verified) {
            $password_reset_verification_hash = Utils::send_password_reset_verification(
                $f3, $user->email,$user->username, 'forgot_password_errors'
            );
            Utils::reroute_with_errors($f3, $args, '/reset-password');
            $user->password_reset_verification_hash = $password_reset_verification_hash;
            $user->save();

            $f3->push('v_confirmations', array(
				'element_id' => 'password_reset_confirmations',
				'message' => 'Reset password code has been sent to your email'
			));
			Utils::reroute_with_confirmations($f3, $args, '/reset-password');
        }

        $f3->push('v_errors', array(
			'element_id' => 'forgot_password_errors',
			'message' => 'Could not send password reset code to user\'s email. If you did not register an email, then password reset is not possible without contacting your site administrator.'
		));
    }
}