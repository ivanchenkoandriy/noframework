<?php
/**
 * Template to display block for alert
 * 
 * @var \app\Result $result Result
 */
?>

<div class="container mt-10">
    <div class="row">
        <div class="col-md-12">
            <?php if ($result && $result instanceof app\Result) { ?>
                <div class = "alert alert-<?= ($result->isSuccess() ? 'success' : 'danger'); ?>" role = "alert"><?= $result->getMessage(); ?></div>
            <?php } ?>
        </div>
    </div>
</div>