<?php
/**
 * Template for the task editing form
 *
 * @var $result \app\Result Result
 * @var $this \app\View View
 * @var $task \app\models\Task Task
 */
?>

<div class="container">
    <form action="/edit/<?= $task->id; ?>" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">User name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $task->name; ?>" placeholder="User name">
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $task->email; ?>" placeholder="Email">
                </div>

                <div class="form-group">
                    <label for="text">Text</label>
                    <textarea class="form-control" rows="3" id="text" name="text" placeholder="Enter text"><?= $task->text; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image">

                    <?php if ('' !== $task->image) { ?>
                        <?php if (!$task->imageExists()) { ?>
                            <?= $this->render('blocks/alert.php', ['result' => app\Result::createFail('File ' . $task->image . ' not found.')]); ?>
                        <?php } else { ?>
                            <p class="text-info mt-10">
                                <img src="/<?= $task->image; ?>" class="img-responsive" alt="Image">
                            </p>

                            <input type="hidden" name="image" value="<?= $task->image; ?>">

                            <input type="hidden" name="remove_image" value="0">
                            <label>
                                <input type="checkbox" name="remove_image" value="1"> Remove image
                            </label>
                        <?php } ?>
                    <?php } ?>
                    <p class="help-block">You can attach a picture to the task. Requirements for images - JPG / GIF / PNG format. The image is automatically converted to 320x240 pixels.</p>
                </div>

                <div class="checkbox">
                    <input type="hidden" name="is_completed" value="0">

                    <label>
                        <input type="checkbox" name="is_completed" value="1"<?= ($task->isCompleted ? ' checked="checked"' : ''); ?>> Completed
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <button type="submit" name="submit" value="send" class="btn btn-success">Edit</button>
            </div>
        </div>
        <input type="hidden" name="id" value="<?= $task->id; ?>">
    </form>
</div>

<?= $this->render('blocks/alert.php', ['result' => $result]); ?>