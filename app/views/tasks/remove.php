<?php
/**
 * Template to prevent removal of the task
 *
 * @var $result \app\Result Result
 * @var $this \app\View View
 * @var $task \app\models\Task Task
 */
?>

<div class="container">
    <form action="/remove/<?= $task->id; ?>" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center text-danger">Are you sure you want to delete the task?</p>
            </div>
        </div>

        <div class="row mt-10">
            <div class="col-md-6">
                <a href="/" class="btn btn-success">Cancel</a>
            </div>

            <div class="col-md-6 text-right">
                <button type="submit" name="submit" value="send" class="btn btn-danger">Remove</button>
            </div>
        </div>
    </form>
</div>

<?= $this->render('blocks/alert.php', ['result' => $result]); ?>