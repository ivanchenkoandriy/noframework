<?php
/**
 * Template for main page
 *
 * @var array $tasks Tasks
 * @var \app\View $this View
 * @var string $pagination HTML for pagination
 * @var array $sortData Sort data
 */
?>

<div class="container">
    <?=
    !empty($tasks) ? $this->render('tasks/table.php', [
                'tasks' => $tasks,
                'pagination' => $pagination,
                'sortData' => $sortData
            ]) : $this->render('blocks/alert.php', ['result' => \app\Result::createFail('No tasks.')]);
    ?>

    <div class="row">
        <div class="col-md-12">
            <a href="/add" class="btn btn-primary">Add task</a>
        </div>
    </div>
</div>