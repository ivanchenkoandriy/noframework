<?php

namespace app\models;

use app\helpers\sorting\Sorting;
use app\Response;
use Exception;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Task class
 *
 * @author Andriy Ivanchenko
 */
class Task {

    /**
     * Identifier
     *
     * @var int
     */
    private $id = null;

    /**
     * Email
     *
     * @var string
     */
    private $email = null;

    /**
     * Text
     *
     * @var string
     */
    private $text = null;

    /**
     * Image
     *
     * @var Image
     */
    private $image = null;

    /**
     * Is completed (0, 1)
     *
     * @var int
     */
    private $isCompleted = null;

    /**
     * Database
     *
     * @var Manager
     */
    private $database = null;

    /**
     * Constructor
     *
     * @param Manager $database
     */
    public function __construct(Manager $database) {
        $this->database = $database;
    }

    /**
     * Make table
     *
     * @return LengthAwarePaginator
     */
    public function makeTable(Sorting $sorting, int $currPage = 1): LengthAwarePaginator {
        $query = $this->database->table('tasks');
        $hasSorting = $sorting->hasSorting();
        $currOrder = $sorting->getCurrentOrder();
        $currDirection = $sorting->getCurrentDirection();

        if ($hasSorting) {
            $query->orderBy($currOrder, $currDirection);
        }

        $tasks = $query->paginate(getenv('PAGINATOR_PER_PAGE'), ['*'], 'page', $currPage);
        if ($hasSorting) {
            $tasks->appends('order', $currOrder);
            $tasks->appends('direction', $currDirection);
        }

        return $tasks;
    }

    /**
     * Validate the properties of the task
     *
     * @return Response
     */
    private function validate(array $attributes): Response {
        foreach ($attributes as $name => $value) {
            /* @var $result Response */
            $result = call_user_func([$this, 'validate' . mb_strtoupper($name, APP_CHARSET)], $value);
            if (!$result->isSuccess()) {
                return $result;
            }
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Add task
     *
     * @return Response
     */
    public function add(string $name, string $email, string $text, Image $image): Response {
        // To prepare values
        $name = trim($name);
        $email = trim($email);
        $text = trim($text);

        // To validate values
        $result = $this->validate([
            'name' => $name,
            'email' => $email,
            'text' => $text,
            'image' => $image
        ]);

        // Try to save
        $id = null;
        if ($result->isSuccess()) {
            try {
                $id = $this->database->table('tasks')->insertGetId([
                    'name' => $name,
                    'email' => $email,
                    'text' => $text,
                    'image' => $image->getRelativePath(),
                ]);
            } catch (Exception $ex) {
                $result = Response::createFail('DB error: ' . $ex->getMessage());
            }
        }

        // To set attributes
        if ($id) {
            $this->id = $id;
        }

        $this->id = 0;
        $this->name = $name;
        $this->email = $email;
        $this->text = $text;
        $this->image = $image;
        $this->isCompleted = 0;

        // To remove the image for fail
        if (!$result->isSuccess()) {
            if ($this->image->exists()) {
                $this->image->remove();
            }
        } else {
            $result = Response::createSuccess('Success!');
        }

        return $result;
    }

    /**
     * Preview task
     *
     * @return Response
     */
    public function preview(string $name, string $email, string $text, Image $image): Response {
        // To prepare values
        $name = trim($name);
        $email = trim($email);
        $text = trim($text);

        // To validate values
        $result = $this->validate([
            'name' => $name,
            'email' => $email,
            'text' => $text,
            'image' => $image
        ]);

        $this->id = 0;
        $this->name = $name;
        $this->email = $email;
        $this->text = $text;
        $this->image = $image;
        $this->isCompleted = 0;

        return $result;
    }

    /**
     * Edit task
     *
     * @return Response
     */
    public function edit(int $id, string $name, string $email, string $text, Image $image, int $isCompleted, int $removeImage = 0): Response {
        // To prepare values
        $name = trim($name);
        $email = trim($email);
        $text = trim($text);

        $attributes = [];
        $this->loadFromDb($id);

        // to save only changes
        if ($this->name !== $name) {
            $attributes['name'] = $name;
            $this->name = $name;
        }

        if ($this->email !== $email) {
            $attributes['email'] = $email;
            $this->email = $email;
        }

        if ($this->text !== $text) {
            $attributes['text'] = $text;
            $this->text = $text;
        }

        // The command to remove a new image
        $oldImage = '';
        if (1 === $removeImage && '' !== $this->image->getRelativePath()) {
            $attributes['image'] = new Image();
            $oldImage = $this->image;
        }

        if (1 !== $removeImage && $this->image->getRelativePath() !== $image->getRelativePath()) {
            $attributes['image'] = $image;
            if ('' !== $this->image->getRelativePath()) {
                $oldImage = $this->image;
            }

            $this->image = $image;
        }

        if ($this->isCompleted !== $isCompleted) {
            $attributes['is_completed'] = $isCompleted;
            $this->isCompleted = (int) $isCompleted;
        }

        if (empty($attributes)) {
            return Response::createSuccess('There is nothing to edit.');
        }

        // to validate new values
        $result = $this->validate($attributes);
        if (!$result->isSuccess()) {
            if ($image->exists()) {
                $image->remove();
            }
            return $result;
        }

        if (array_key_exists('image', $attributes)) {
            $attributes['image'] = $attributes['image']->getRelativePath();
        }

        try {
            // try to update
            $this->database->table('tasks')->where('id', '=', $this->id)->update($attributes);
        } catch (Exception $ex) {
            return Response::createFail('DB error: ' . $ex->getMessage());
        }

        // to remove old image
        if ($oldImage instanceof Image) {
            $oldImage->remove();
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Remove task
     *
     * @param int $id
     * @return Response
     */
    public function remove(int $id): Response {
        if ($id) {
            $this->loadFromDb($id);
            if ($this->image->exists()) {
                $this->image->remove();
            }

            $this->database->table('tasks')->delete($id);
            return Response::createSuccess('Success!');
        }

        return Response::createFail('Fail!');
    }

    /**
     * Load the properties of this task from the database
     *
     * @param int $id Identifier
     * @return bool
     */
    public function loadFromDb(int $id): bool {
        $taskArray = $this->database->table('tasks')->where('id', $id)->get()->first();

        $image = new Image();
        if ($taskArray->image) {
            $image->setRelativePath($taskArray->image);
        }

        if ($taskArray) {
            $this->id = (int) $taskArray->id;
            $this->name = $taskArray->name;
            $this->email = $taskArray->email;
            $this->image = $image;
            $this->text = $taskArray->text;
            $this->isCompleted = (int) $taskArray->is_completed;
            return true;
        }

        $this->image = $image;

        return false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage(): Image {
        return $this->image;
    }

    /**
     * Get the state of completing
     *
     * @return int
     */
    public function getIsCompleted(): int {
        return $this->isCompleted;
    }

    /**
     * Validate name
     *
     * @param string $name
     * @return Response
     */
    private function validateName(string $name): Response {
        if ('' === $name) {
            return Response::createFail('Name required!');
        }

        if (60 < mb_strlen($name, APP_CHARSET)) {
            return Response::createFail('The maximum length of a name should not exceed 60 characters!');
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Validate email
     *
     * @param string $email
     * @return Response
     */
    private function validateEmail(string $email): Response {
        if ('' === $email) {
            return Response::createFail('Email required!');
        }

        if (255 < mb_strlen($email, APP_CHARSET)) {
            return Response::createFail('The maximum length of email should not exceed 255 characters!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::createFail('Not a valid a email!');
        }

        if ($this->database->table('tasks')->select(['id'])->where('email', '=', $email)->exists()) {
            return Response::createFail('The email must be unque!');
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Validate text
     *
     * @param string $text
     * @return Response
     */
    private function validateText(string $text): Response {
        if ('' === $text) {
            return Response::createFail('The text required!!');
        }

        $length = mb_strlen($text, APP_CHARSET);
        if (pow(2, 16) < $length) {
            return Response::createFail('The maximum length of text should not exceed 2^16 characters!');
        }

        if (16 > $length) {
            return Response::createFail('The minumum length of text must be at least 16 characters!');
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Validate image
     *
     * @param \app\models\Image $image
     * @return Response
     */
    private function validateImage(Image $image): Response {
        if (512 < mb_strlen($image->getRelativePath(), APP_CHARSET)) {
            return Response::createFail('The maximum length of an image should not exceed 512 characters!');
        }

        return Response::createSuccess('Success!');
    }

    /**
     * Validate isCompleted
     *
     * @param int $isCompleted
     * @return Response
     */
    private function validateIs_completed(int $isCompleted): Response {
        if (1 !== $isCompleted && 0 !== $isCompleted) {
            return Response::createFail('Value is wrong!');
        }

        return Response::createSuccess('Success!');
    }

}
