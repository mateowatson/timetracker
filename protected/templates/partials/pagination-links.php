<?php if($v_prev_link || $v_next_link): ?>
    <div class="mb-3">
        <?php if($v_prev_link): ?>
        <a class="mr-2" href="<?php echo $SITE_URL . $v_prev_link; ?>" data-action="fragment-loader#load">
            &larr;
        </a>
        <?php endif; ?>
        <span>
            <?php echo 'Page ' . $v_curr_page . '/' . $v_num_pages; ?>
        </span>
        <?php if($v_next_link): ?>
        <a class="ml-2" href="<?php echo $SITE_URL . $v_next_link; ?>" data-action="fragment-loader#load">
            &rarr;
        </a>
        <?php endif; ?>
    </div>
<?php endif; ?>