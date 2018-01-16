<?php
/**
 * Template for the task adding form
 *
 * @var \app\Result $result Result
 * @var \app\View $this View
 * @var \app\models\Task $task Task
 */
?>

<div class="container" id="container-form-add">
    <form action="/add" method="post" enctype="multipart/form-data">
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
                    <p class="help-block">You can attach a picture to the task. Requirements for images - JPG / GIF / PNG format. The image is automatically converted to 320x240 pixels.</p>
                </div>
            </div>
        </div>

        <div class="row mt-10">
            <div class="col-md-6">
                <a id="button-task-preview" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Order">Preview</a>
            </div>

            <div class="col-md-6 text-right">
                <button type="submit" name="submit" value="send" class="btn btn-success">Add</button>
            </div>
        </div>
    </form>
</div>

<div class="container hidden" id="container-preview-add"></div>

<?= $this->render('blocks/alert.php', ['result' => $result]); ?>