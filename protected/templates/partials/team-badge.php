<?php if($v_team): ?>
<div class="col-lg-12">
    <a href="<?php echo $SITE_URL.'/team/'.$v_team['id']; ?>" class="badge badge-secondary"><?php echo $v_team['name']; ?></a>
</div>
<?php endif; ?>