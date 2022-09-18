<?php

class ManageProjects {
    public $db = null;
    public $projects = [];
    public $user = null;

    function init(\Base $f3, array $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::send_csrf($f3, $args);

        // GET DB, SESSION AND USER
        $this->db = $f3->get('DB');
        $session_username = $f3->get('SESSION.session_username');
        $this->user = new \DB\SQL\Mapper($this->db, 'users');
        $this->user->load(array('username=?', $session_username));
        $f3->set('v_username', $session_username);
        $f3->set('v_user_email', $user->email);
        $f3->set('v_user_email_verified', $user->email_verified);
    }

    function show_all(\Base $f3, array $args) {
        $this->init($f3, $args);

        // set the page title
		$f3->set('v_page_title', 'Manage Projects');

        // Get all Projects
        $this->projects = $this->db->exec('
            SELECT * FROM projects
            LEFT JOIN users_projects
                ON (users_projects.user_id = ? AND
                users_projects.project_id = projects.id)
        ', $this->user->id);

        $f3->set('v_projects', $this->projects);

        $view = new \View;
        echo $view->render('manage-projects.php');
    }

    function show_project(\Base $f3, array $args) {
        $this->init($f3, $args);

        $project_mapper = new \DB\SQL\Mapper($this->db, 'projects');
        $project_mapper->load(['id=?', $args['id']]);

        if ($project_mapper->dry()) {
            $f3->error(404);
        }

		$f3->set('v_page_title', 'Edit Project');

        $f3->set('v_project', ['id' => $project_mapper->id, 'name' => $project_mapper->name]);

        $view = new \View;
        echo $view->render('project-single.php');
    }

    function save_project(\Base $f3, array $args) {
        $this->init($f3, $args);

        $req = $f3->get('REQUEST');
        $name = $req['project_name'];

        if (!$name) {
            $f3->push('v_errors', [
                'element_id' => 'save_project_errors',
                'message' => 'Project name cannot be empty.'
            ]);

            Utils::reroute_with_errors($f3, $args, "/projects/{$args['id']}");
        }

        $project_mapper = new \DB\SQL\Mapper($this->db, 'projects');
        $project_mapper->load(['id=?', $args['id']]);
        $project_mapper->name = $name;
        $project_mapper->save();
        
        $f3->reroute('/projects');
    }
}