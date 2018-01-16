<?php
/**
 * Message template for successfully removal a task
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
                <p class="text-center text-success">Task successfully deleted!</p>
            </div>
        </div>

        <div class="row mt-10">
            <div class="col-md-6">
                <a href="/" class="btn btn-success">Return to task list</a>
            </div>
        </div>
    </form>
</div>

<?= $this->render('blocks/alert.php', ['result' => $result]); ?>