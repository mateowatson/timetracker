<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
		<?php require_once('partials/team-badge.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-12">
			<h2 class="sr-only">Advanced Report</h2>
<div>
<textarea name="" id="" cols="80" rows="30"><?php var_dump($v_obj->user_projects_tasks['tasksQuery']); ?></textarea>
</div>
            <form action="/advanced-report" method="GET">
                <div class="row">
                    <div class="col-12 mt-3">
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Generate Report">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Project(s)</legend>
                            <?php if(!empty($v_obj->user_projects_tasks['projects'])): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_projects_all" name="ar_projects[]" value="ar_all" <?php if(in_array('ar_all',$v_obj->ar_projects)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_projects_all">All</label>
                            </div>
                            <?php foreach($v_obj->user_projects_tasks['projects'] as $project): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_projects_<?= $project['id'] ?>" name="ar_projects[]" value="<?= $project['id'] ?>" <?php if(in_array($project['id'], $v_obj->ar_projects)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_projects_<?= $project['id'] ?>"><?= $project['name'] ?></label>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p>You do not yet have any projects.</p>
                            <?php endif; ?>
                        </fieldset>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Task(s)</legend>
                            <?php if(!empty($v_obj->user_projects_tasks['tasks'])): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_tasks_all" name="ar_tasks[]" value="ar_all" <?php if(in_array('ar_all',$v_obj->ar_tasks)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_tasks_all">All</label>
                            </div>
                            <?php foreach($v_obj->user_projects_tasks['tasks'] as $task): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_tasks_<?= $task['id'] ?>" name="ar_tasks[]" value="<?= $task['id'] ?>" <?php if(in_array($task['id'], $v_obj->ar_tasks)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_tasks_<?= $task['id'] ?>"><?= $task['name'] ?></label>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p>You do not yet have any tasks.</p>
                            <?php endif; ?>
                        </fieldset>
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <label for="ar_begin_date">Begin Date</label>
                            <input class="form-control" type="date" name="ar_begin_date" id="ar_begin_date" value="<?php if($v_obj->ar_begin_date): echo $v_obj->ar_begin_date; endif; ?>">
                        </div>
                        <div class="form-group">
                            <label for="ar_end_date">End Date (if different from Begin Date)</label>
                            <input class="form-control" type="date" name="ar_end_date" id="ar_end_date" value="<?php if($v_obj->ar_end_date): echo $v_obj->ar_end_date; endif; ?>">
                        </div>
                        <div class="form-group">
                            <label for="ar_report_type">Report Type</label>
                            <select class="custom-select" name="ar_report_type" id="ar_report_type">
                                <option <?php if($v_obj->ar_report_type === 'Total by Day'): ?>selected="selected"<?php endif; ?>>Total by Day</option>
                                <option <?php if($v_obj->ar_report_type === 'Total by Week'): ?>selected="selected"<?php endif; ?>>Total by Week</option>
                                <option <?php if($v_obj->ar_report_type === 'Total by Year'): ?>selected="selected"<?php endif; ?>>Total by Year</option>
                                <option <?php if($v_obj->ar_report_type === 'Individual Logs'): ?>selected="selected"<?php endif; ?>>Individual Logs</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ar_notes">Filter by Notes Including</label>
                            <input class="form-control" type="text" name="ar_notes" id="ar_notes" placeholder="Search term (case insensitive)" value="<?php if($v_obj->ar_notes): echo $v_obj->ar_notes; endif; ?>">
                        </div>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>