<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <?php if (count($v_projects)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($v_projects as $project): ?>
                            <tr>
                                <td><?= $project['id'] ?></td>
                                <td><a href="/projects/<?= $project['id'] ?>"><?= $project['name'] ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>No existing projects.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once('partials/footer.php'); ?>