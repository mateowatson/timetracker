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
        $f3->set('v_user_email', $this->user->email);
        $f3->set('v_user_email_verified', $this->user->email_verified);
    }

    function show_all(\Base $f3, array $args) {
        $this->init($f3, $args);

        // set the page title
		$f3->set('v_page_title', 'Manage Projects');

        $req = $f3->get('REQUEST');
        $show_archived = $req['archived'] ?? null;

        $f3->set('v_show_archived', $show_archived);

        // Get all Projects
        $this->projects = $this->db->exec('
            SELECT * FROM projects
            LEFT JOIN users_projects
            ON users_projects.project_id = projects.id
            WHERE users_projects.user_id = ?'
            .($show_archived ? '' : ' AND projects.archived IS NULL'),

            $this->user->id
        );

        $f3->set('v_projects', $this->projects);

        $view = new \View;
        echo $view->render('projects-list.php');
    }

    function show_project(\Base $f3, array $args) {
        $this->init($f3, $args);

        $project_mapper = new \DB\SQL\Mapper($this->db, 'projects');
        $project_mapper->load(['id=?', $args['id']]);

        if ($project_mapper->dry()) {
            $f3->error(404);
        }

		$f3->set('v_page_title', 'Edit Project');

        $f3->set('v_project', [
            'id' => $project_mapper->id,
            'name' => $project_mapper->name,
            'archived' => $project_mapper->archived
        ]);

        $view = new \View;
        echo $view->render('project-single.php');
    }

    function save_project(\Base $f3, array $args) {
        $this->init($f3, $args);

        $req = $f3->get('REQUEST');
        $name = $req['project_name'];
        $archive = isset($req['archive']);
        $unarchive = isset($req['unarchive']);

        if (!$name) {
            $f3->push('v_errors', [
                'element_id' => 'save_project_errors',
                'message' => 'Project name cannot be empty.'
            ]);

            Utils::reroute_with_errors($f3, $args, "/projects/{$args['id']}");
        }

        $project_mapper = new \DB\SQL\Mapper($this->db, 'projects');
        $project_mapper->load(['id=?', $args['id']]);

        if($archive) {
            $project_mapper->archived = date("Y-m-d H:i:s");
            $project_mapper->save();

            $f3->push('v_confirmations', [
                'element_id' => 'save_project_confirmations',
                'message' => "Project $name has been archived."
            ]);

            Utils::reroute_with_confirmations($f3, $args, '/projects');
            
            $f3->reroute('/projects');
        }

        if($unarchive) {
            $project_mapper->archived = null;
            $project_mapper->save();

            $f3->push('v_confirmations', [
                'element_id' => 'save_project_confirmations',
                'message' => "Project $name has been unarchived."
            ]);

            Utils::reroute_with_confirmations($f3, $args, '/projects');
            
            $f3->reroute('/projects');
        }
        
        $project_mapper->name = $name;
        $project_mapper->save();
        
        $f3->reroute('/projects');
    }
}