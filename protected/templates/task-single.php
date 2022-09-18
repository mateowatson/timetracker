<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-sm-6 col-md-5">
            <h2><?= $v_task['name'] ?></h2>

            <?php if (in_array('save_task_errors', $v_errors_element_ids ?: [])): ?>

            <div class="alert alert-danger" role="alert">
                <p>The following errors occurred:</p>

                <ul>
                    <?php foreach ($v_errors as $error): ?>
                        <?php if ($error->element_id === 'save_task_errors'): ?>
                            <li><?php echo $error->message; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php endif; ?>

            <form action="/tasks/<?= $v_task['id']; ?>" method="post">
                <input type="hidden" name="csrf" id="csrf_task" value="<?php echo $CSRF; ?>" />

                <div class="form-group">
                    <label for="task_name">Name</label>
                    <input id="task_name" type="text" class="form-control" name="task_name" value="<?= $v_task['name'] ?>" />
                </div>

                <button class="btn btn-primary mr-3" type="submit">Save</button>
                <a href="/tasks">Back to Tasks</a>
            </form>
        </div>
    </div>
</div>