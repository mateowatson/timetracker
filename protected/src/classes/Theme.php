<?php
class Theme {
	function change($f3, $args) {
		$req = $f3->get('REQUEST');
		Utils::redirect_logged_out_user($f3, $args);

        $req_theme = !empty($req['theme']) ? $req['theme'] : 'light';

        if($req_theme !== 'dark' || $req_theme !== 'light')
            $req_theme !== 'light';

        $f3->set('COOKIE.theme', $req_theme);

        $f3->reroute($f3->get('REQUEST.global_back_to'));
    }
}