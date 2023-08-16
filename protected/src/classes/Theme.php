<?php
class Theme {
	function change($f3, $args) {
		$req = $f3->get('REQUEST');
		Utils::redirect_logged_out_user($f3, $args);

        $req_theme = !empty($req['changetheme']) ? $req['changetheme'] : 'light';

        if($req_theme === 'dark')
            $req_theme = 'dark';
        else
            $req_theme = 'light';

        $f3->set('COOKIE.savedtheme', $req_theme, 60*60*24*365);

        $f3->reroute($f3->get('REQUEST.global_back_to'));
    }
}