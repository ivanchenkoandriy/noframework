<?php

namespace app\models;

use app\Auth;
use app\Result;
use Exception;
use FileUpload\FileSystem\Simple as FileSystemSimple;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple as PathResolverSimple;
use FileUpload\Validator\Simple as ValidatorSimple;
use Illuminate\Database\Capsule\Manager as Database;

/**
 * Model for Task
 *
 * @author Andriy Ivanchenko
 */
class Task {

    /**
     * Identifier
     *
     * @var int
     */
    public $id;

    /**
     * User name
     *
     * @var string
     */
    public $name = '';

    /**
     * E-mail
     *
     * @var string
     */
    public $email = '';

    /**
     * Text
     *
     * @var string
     */
    public $text = '';

    /**
     * Image path
     *
     * @var string
     */
    public $image = '';

    /**
     * Is the task completed? (0,1)
     *
     * @var int
     */
    public $isCompleted = 0;

    /**
     * Database manager
     * @var Database
     */
    private $db;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Validate the properties of the task
     *
     * @return Result
     */
    public function validate(): Result {
        $this->name = trim($this->name);
        if ('' === $this->name) {
            return Result::createFail('Name required!');
        }

        if (60 < mb_strlen($this->name, APP_CHARSET)) {
            return Result::createFail('The maximum length of a name should not exceed 60 characters!');
        }

        if ('' === $this->email) {
            return Result::createFail('Email required!');
        }

        if (255 < mb_strlen($this->email, APP_CHARSET)) {
            return Result::createFail('The maximum length of email should not exceed 255 characters!');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return Result::createFail('Not a valid a email!');
        }

        if ('' === $this->text) {
            return Result::createFail('Text required!');
        }

        if (pow(2, 16) < mb_strlen($this->text, APP_CHARSET)) {
            return Result::createFail('The maximum length of email should not exceed 2^16 characters!');
        }

        if (512 < mb_strlen($this->image, APP_CHARSET)) {
            return Result::createFail('The maximum length of a image should not exceed 512 characters!');
        }

        if ($this->imageExists()) {

        }

        return Result::createSuccess('Success!');
    }

    /**
     * Add task
     *
     * @return Result
     */
    public function add(): Result {
        try {
            $isAdded = $this->db->table('tasks')->insert([
                'name' => $this->name,
                'email' => $this->email,
                'text' => $this->text,
                'image' => $this->image,
            ]);
        } catch (Exception $ex) {

            return Result::createFail('DB error: ' . $ex->getMessage());
        }

        if ($isAdded) {
            $result = Result::createSuccess('Success!');
        } else {
            $result = Result::createFail('Fail!');
        }

        return $result;
    }

    /**
     * Edit task
     *
     * @param array $attributes
     * @return Result
     */
    public function edit(array $attributes = []): Result {
        try {
            $this->db->table('tasks')->where('id', '=', $this->id)->update([
                'name' => $this->name,
                'email' => $this->email,
                'text' => $this->text,
                'image' => $this->image,
                'is_completed' => $this->isCompleted
            ]);
        } catch (Exception $ex) {
            return Result::createFail('DB error: ' . $ex->getMessage());
        }

        return Result::createSuccess('Success!');
    }

    /**
     * Remove task
     *
     * @return Result
     */
    public function remove(): Result {
        if ($this->id) {
            if ($this->image) {
                $fs = new FileSystemSimple();
                $fs->unlink(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $this->image);
            }

            $this->db->table('tasks')->delete($this->id);
            return Result::createSuccess('Success!');
        }

        return Result::createFail('Fail!');
    }

    /**
     * Upload an image
     *
     * @return Result
     */
    private function uploadImage(): Result {
        // Simple validation (max file size 2MB and only two allowed mime types)
        $validator = new ValidatorSimple('2M', ['image/png', 'image/jpg', 'image/jpeg',
            'image/gif']);

        // Simple path resolver, where uploads will be put
        $pathresolver = new PathResolverSimple(ROOT_PATH . 'public/uploads');

        // The machine's filesystem
        $filesystem = new FileSystemSimple();

        // FileUploader itself
        $fileupload = new FileUpload($_FILES['image'], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        $fileupload->addValidator($validator);

        $fileupload->processAll();
        $files = $fileupload->getFiles();

        $file = null;
        if (count($files) > 0) {
            $file = reset($files);
        }

        if (NO_FILE_WAS_UPLOADED === $file->errorCode) {
            return Result::createSuccess('');
        }


        /* @var $file \FileUpload\File */
        if (empty($file->error)) {
            $imagePathname = $file->getPathname();
            (new \claviska\SimpleImage($imagePathname))
                    ->bestFit(PICTURE_WIDTH, PICTURE_HEIGHT)
                    ->toFile($imagePathname);

            $this->image = 'uploads/' . $file->getFilename();

            $result = Result::createSuccess('Success.');
        } else {
            $result = Result::createFail('Could not load file: ' . $file->error . '.');
        }

        return $result;
    }

    /**
     * Remove image
     *
     * @return Result
     */
    public function removeImage(): Result {
        $image = $this->db->table('tasks')->select(['image'])->where('id', $this->id)->get()->first()->image;
        $fs = new FileSystemSimple();
        try {
            $fs->unlink(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $image);
            $this->db->table('tasks')->where('id', $this->id)->update(['image' => '']);
            $this->image = '';
        } catch (Exception $ex) {
            return Result::createFail('Error: ' . $ex->getMessage() . '!');
        }
        return Result::createSuccess('Success!');
    }

    /**
     * Is there an image
     *
     * @return bool
     */
    public function imageExists(): bool {
        return '' !== $this->image && file_exists(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $this->image);
    }

    /**
     * Load the properties of this task from the form data
     *
     * @return Result
     */
    public function loadFromForm(): Result {
        $this->name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $this->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL, [
            'options' => [
                'default' => ''
            ]
        ]);

        $this->text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $this->image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $removeImage = (int) filter_input(INPUT_POST, 'remove_image', FILTER_SANITIZE_NUMBER_INT, [
                    'options' => [
                        'default' => 0
                    ]
        ]);

        if (1 === $removeImage) {
            $result = $this->removeImage();
        } else {
            $result = $this->uploadImage();
        }

        if (Auth::autorized()) {
            $this->isCompleted = (int) filter_input(INPUT_POST, 'is_completed', FILTER_SANITIZE_NUMBER_INT, [
                        'options' => [
                            'default' => 0
                        ]
            ]);
        }

        return $result;
    }

    /**
     * Load the properties of this task from the database
     *
     * @param int $id Identifier
     * @return bool
     */
    public function loadFromDb(int $id): bool {
        $taskArray = $this->db->table('tasks')->where('id', $id)->get()->first();
        if ($taskArray) {
            $this->id = $taskArray->id;
            $this->name = $taskArray->name;
            $this->email = $taskArray->email;
            $this->image = $taskArray->image;
            $this->text = $taskArray->text;
            $this->isCompleted = $taskArray->is_completed;
            return true;
        }

        return false;
    }

}
