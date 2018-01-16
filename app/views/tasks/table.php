<?php
/**
 * Data table template
 *
 * @var array $sortData Sort data
 * @var \app\models\Task $task Task
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-sortable" id="table-tasks">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="<?= $sortData['name']['class']; ?>"><a href="<?= $sortData['name']['url']; ?>">User</a></th>
                        <th class="<?= $sortData['email']['class']; ?>"><a href="<?= $sortData['email']['url']; ?>">E-mail</a></th>
                        <th>Image</th>
                        <th>Text</th>
                        <th class="<?= $sortData['is_completed']['class']; ?>"><a href="<?= $sortData['is_completed']['url']; ?>">Completed</a></th>
                        <th>View</th>
                        <?php if (\app\Auth::autorized()) { ?>
                            <th>Edit</th>
                            <th>Remove</th>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tasks as $task) { ?>
                        <tr<?= $task->is_completed ? ' class="success"' : ''; ?>>
                            <td><?= $task->id; ?></td>
                            <td><?= $task->name; ?></td>
                            <td><?= $task->email; ?></td>
                            <td><?= $task->image ? 'present' : 'missing'; ?></td>
                            <td><?= $task->text; ?></td>
                            <td>
                                <?php if ($task->is_completed) { ?>
                                    <span class="glyphicon glyphicon-ok text-success"></span>
                                <?php } else { ?>
                                    <span class="glyphicon glyphicon-remove text-danger"></span>
                                <?php } ?>
                            </td>

                            <td><a class="btn btn-sm btn-primary" href="/view/<?= $task->id; ?>">View</a></td>
                            <?php if (\app\Auth::autorized()) { ?>
                                <td><a class="btn btn-sm btn-primary" href="/edit/<?= $task->id; ?>">Edit</a></td>
                                <td><a class="btn btn-sm btn-danger" href="/remove/<?= $task->id; ?>">Remove</a></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $pagination; ?>
    </div>
</div>