<?php
/**
 * Template display deny access
 *
 * @var \app\View $this View
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('blocks/alert.php', ['result' => \app\Result::createFail('Access denied!')]); ?>
        </div>
    </div>
</div>
