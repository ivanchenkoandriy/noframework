<?php
/**
 * Template for view task
 *
 * @var $task \app\models\Task
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>User name:</th>
                            <td><?= $task->name; ?></td>
                        </tr>

                        <tr>
                            <th>Email:</th>
                            <td><?= $task->email; ?></td>
                        </tr>

                        <tr>
                            <th>Text:</th>
                            <td><?= $task->text; ?></td>
                        </tr>

                        <?php if ($task->image) { ?>
                            <tr>
                                <th>Image:</th>
                                <td><img src="/<?= $task->image; ?>" class="img-responsive" alt="Image"></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <th>Completed:</th>
                            <td><?= $task->isCompleted ? 'yes' : 'no'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>