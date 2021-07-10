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
<textarea name="" id="" cols="30" rows="10"><?php var_dump($v_obj); ?></textarea>
</div>
            <form action="/advanced-report" method="GET">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Project(s)</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_projects_all" name="ar_projects[]" value="ar_all" <?php if(in_array('ar_all',$v_obj->ar_projects)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_projects_all">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_projects_1" name="ar_projects[]" value="Project 1" <?php if(in_array('Project 1',$v_obj->ar_projects)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_projects_1">Project 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_projects_2" name="ar_projects[]" value="Project 2" <?php if(in_array('Project 2',$v_obj->ar_projects)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_projects_2">Project 2</label>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Task(s)</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_tasks_all" name="ar_tasks[]" value="ar_all" <?php if(in_array('ar_all',$v_obj->ar_tasks)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_tasks_all">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_tasks_1" name="ar_tasks[]" value="Task 1" <?php if(in_array('Task 1',$v_obj->ar_tasks)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_tasks_1">Task 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_task_2" name="ar_tasks[]" value="Task 2" <?php if(in_array('Task 2',$v_obj->ar_tasks)): ?>checked<?php endif; ?>>
                                <label class="form-check-label" for="ar_tasks_2">Task 2</label>
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <label for="ar_notes">Filter by Notes Including</label>
                            <input class="form-control" type="text" name="ar_notes" id="ar_notes" placeholder="Search term (case insensitive)" value="<?php if($v_obj->ar_notes): echo $v_obj->ar_notes; endif; ?>">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_begin_date">Begin Date</label>
                            <input class="form-control" type="date" name="ar_begin_date" id="ar_begin_date" value="<?php if($v_obj->ar_begin_date): echo $v_obj->ar_begin_date; endif; ?>">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_end_date">End Date (if different from Begin Date)</label>
                            <input class="form-control" type="date" name="ar_end_date" id="ar_end_date" value="<?php if($v_obj->ar_end_date): echo $v_obj->ar_end_date; endif; ?>">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_report_type">Report Type</label>
                            <select class="custom-select" name="ar_report_type" id="ar_report_type">
                                <option <?php if($v_obj->ar_report_type === 'Total by Day'): ?>selected="selected"<?php endif; ?>>Total by Day</option>
                                <option <?php if($v_obj->ar_report_type === 'Total by Week'): ?>selected="selected"<?php endif; ?>>Total by Week</option>
                                <option <?php if($v_obj->ar_report_type === 'Total by Year'): ?>selected="selected"<?php endif; ?>>Total by Year</option>
                                <option <?php if($v_obj->ar_report_type === 'Individual Logs'): ?>selected="selected"<?php endif; ?>>Individual Logs</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Generate Report">
                        </div>
                    </div>
            </form>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>