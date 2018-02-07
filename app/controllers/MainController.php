<?php

namespace app\controllers;

use app\helpers\sorting\Sorting;
use app\models\Image;
use app\models\SitePage;
use app\models\Task;
use app\Response;
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\HttpFoundation\Request;
use function app\helpers\Http\redirect;

/**
 * Main controller
 *
 * @author Andriy Ivanchenko
 */
class MainController {

    /**
     * Database
     *
     * @var Manager
     */
    private $database;

    /**
     * View
     *
     * @var type
     */
    private $view;

    /**
     * Request
     *
     * @var Request
     */
    private $request;

    /**
     * Constructor
     *
     * @param Manager $database
     * @param \Twig_Environment $view
     */
    public function __construct(Manager $database, \Twig_Environment $view, Request $request) {
        $this->database = $database;
        $this->view = $view;
        $this->request = $request;
    }

    /**
     * Main page, Task table
     *
     * @return string Returns the HTML
     */
    public function index(): string {
        $page = $this->request->get('page', 1);
        $order = $this->request->get('order', '');
        $direction = $this->request->get('direction', '');
        $uri = $this->request->getUri();
        $url = \League\Url\Url::createFromUrl($uri);

        $sorting = new Sorting($url, $order, $direction, [
            'name' => [
                'direction' => 'asc',
                'class' => 'sorting'
            ],
            'email' => [
                'direction' => 'asc',
                'class' => 'sorting'
            ],
            'is_completed' => [
                'direction' => 'asc',
                'class' => 'sorting'
            ],
        ]);

        $task = new Task($this->database);
        $tasks = $task->makeTable($sorting, $page);

        $sitePage = new SitePage('BeeGee | Main page', 'Main page and Task table.', [
            ['title' => 'Main']
        ]);

        return $this->view->render('tasks/index.twig', [
                    'tasks' => $tasks,
                    'sortData' => $sorting->getData(),
                    'sitePage' => $sitePage,
                    'emptyTasks' => Response::createFail('Data not found!')
        ]);
    }

    /**
     * Add task
     *
     * @return string Returns the HTML
     */
    public function add(): string {
        $sitePage = new SitePage('BeeGee | Add task', 'The page for adding task.', [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'Add task'],
        ]);

        return $this->view->render('tasks/add.twig', ['sitePage' => $sitePage]);
    }

    /**
     * The handler of the adding
     *
     * @return string
     */
    public function addHandler(): string {
        $request = $this->request;

        $image = new Image();
        $image->upload();
        if (!$image) {
            $image = Image::createFromString($request->get('image'));
        }

        $task = new Task($this->database);
        $result = $task->add($request->get('name', ''), $request->get('email', ''), $request->get('text', ''), $image);
        if ($result->isSuccess()) {
            redirect('/');
        } else {
            $sitePage = new SitePage('BeeGee | Add task', 'The page for adding task.', [
                ['title' => 'Main', 'url' => '/'],
                ['title' => 'Add task'],
            ]);

            return $this->view->render('tasks/add.twig', [
                        'result' => $result,
                        'task' => $task,
                        'sitePage' => $sitePage
            ]);
        }
    }

    /**
     * The handler of the editing
     *
     * @param int $id
     * @param \app\models\User $user
     * @return string
     */
    public function editHandler(int $id, \app\models\User $user): string {
        $authorized = $user->authorized();
        if (!$authorized) {
            return $this->view->render('tasks/access-denied.twig', ['result' => Response::createFail('Access denied!')]);
        }

        $request = $this->request;

        $image = new Image();
        $image->upload();
        if ('' === $image->getRelativePath()) {
            $image->setRelativePath($request->get('image', ''));
        }

        $task = new Task($this->database);
        $result = $task->edit($request->get('id', ''), $request->get('name', ''), $request->get('email', ''), $request->get('text', ''), $image, $request->get('is_completed', 0), $request->get('remove_image', 0));
        if ($result->isSuccess()) {
            redirect('/');
        } else {
            $sitePage = new SitePage('BeeGee | Edit task', 'The page for editing task.', [
                ['title' => 'Main', 'url' => '/'],
                ['title' => 'Edit task'],
            ]);

            return $this->view->render('tasks/edit.twig', [
                        'task' => $task,
                        'result' => $result,
                        'wrongFile' => Response::createFail('File ' . $task->getImage()->getRelativePath() . ' not found.'),
                        'sitePage' => $sitePage
            ]);
        }
    }

    /**
     * Edit task
     *
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function edit(int $id, \app\models\User $user): string {
        $authorized = $user->authorized();
        if (!$authorized) {
            return $this->view->render('tasks/access-denied.twig', ['result' => Response::createFail('Access denied!')]);
        }

        $task = new Task($this->database);
        $task->loadFromDb($id);

        $sitePage = new SitePage('BeeGee | Edit task', 'The page for editing task.', [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'Edit task'],
        ]);

        return $this->view->render('tasks/edit.twig', [
                    'task' => $task,
                    'sitePage' => $sitePage
        ]);
    }

    /**
     * Remove task
     *
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function remove(int $id, \app\models\User $user): string {
        if (!$user->authorized()) {
            return $this->view->render('tasks/access-denied.twig', ['result' => Response::createFail('Access denied!')]);
        }

        $task = new Task($this->database);
        $task->loadFromDb($id);

        $sitePage = new SitePage('BeeGee | Remove task', 'The page for removing task.', [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'Remove task'],
        ]);

        return $this->view->render('tasks/remove.twig', [
                    'task' => $task,
                    'sitePage' => $sitePage
        ]);
    }

    /**
     * The handler of the removing
     *
     * @param int $id
     * @param \app\models\User $user
     * @return string
     */
    public function removeHandler(int $id, \app\models\User $user): string {
        if (!$user->authorized()) {
            return $this->view->render('tasks/access-denied.twig', ['result' => Response::createFail('Access denied!')]);
        }

        $task = new Task($this->database);
        $result = $task->remove($id);
        if (!$result->isSuccess()) {
            $result = Response::createFail('Failed to delete task!');
            $sitePage = new SitePage('BeeGee | Remove task', 'The page for removing task.', [
                ['title' => 'Main', 'url' => '/'],
                ['title' => 'Remove task'],
            ]);

            return $this->view->render('tasks/remove.twig', [
                        'task' => $task,
                        'result' => $result,
                        'sitePage' => $sitePage
            ]);
        } else {
            $sitePage = new SitePage('BeeGee | Remove task', 'The task was successful removal.', [
                ['title' => 'Main', 'url' => '/'],
                ['title' => 'Successful removal'],
            ]);

            return $this->view->render('tasks/successful-removal.twig', [
                        'task' => $task,
                        'result' => $result,
                        'sitePage' => $sitePage
            ]);
        }
    }

    /**
     * Look at the task
     *
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function view(int $id): string {
        $task = new Task($this->database);
        $task->loadFromDb($id);

        $sitePage = new SitePage('BeeGee | Remove task', 'Look at the task.', [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'View task'],
        ]);

        return $this->view->render('tasks/view.twig', [
                    'task' => $task,
                    'sitePage' => $sitePage
        ]);
    }

    /**
     * Preview the task
     *
     * @return string Returns a JSON string
     */
    public function preview(): string {
        $request = $this->request;

        // TODO To remove pictures that has left
        $image = new Image();
        $image->upload();
        if (!$image) {
            $image = Image::createFromString($request->get('image'));
        }

        $task = new Task($this->database);
        $previewResponse = $task->preview($request->get('name', ''), $request->get('email', ''), $request->get('text', ''), $image);

        $parameters = ['task' => $task];

        if (!$previewResponse->isSuccess()) {
            $parameters['result'] = $previewResponse;
        }

        $html = $this->view->render('tasks/preview.twig', $parameters);

        $result = Response::createSuccess('Success!', ['html' => $html]);

        return json_encode($result->toArray());
    }

}
