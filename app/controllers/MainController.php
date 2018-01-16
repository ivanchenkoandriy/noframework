<?php

namespace app\controllers;

use app\Auth;
use app\models\Task;
use app\Result;
use Illuminate\Database\Capsule\Manager;
use League\Url\Url;
use function app\helpers\Html\pagination;
use function app\helpers\Http\redirect;

/**
 * Main controller
 *
 * @author Andriy Ivanchenko
 */
class MainController extends BaseController {

    /**
     * Main page, Task table
     *
     * @return string Returns the HTML
     */
    public function index() {
        $this->addLayoutParam('title', 'BeeGee | Main page');
        $this->addLayoutParam('description', 'Main page and Task table');
        $this->addLayoutParam('breadcrumbs', [
            [
                'title' => 'Main',
            ]
        ]);

        $order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $direction = filter_input(INPUT_GET, 'direction', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $sortData = [
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
        ];

        $currDirection = '';
        $currOrder = '';
        if (array_key_exists($order, $sortData)) {
            $currOrder = $order;
            if ('asc' === $direction) {
                $currDirection = 'asc';
                $nextDirection = 'desc';
            } else {
                $currDirection = 'desc';
                $nextDirection = 'asc';
            }

            $sortData[$order]['direction'] = $nextDirection;
            $sortData[$order]['class'] = 'sorting_' . $currDirection;
        }

        $url = Url::createFromServer($_SERVER);
        $baseUrl = $url->getBaseUrl();
        $query = $url->getQuery();
        $sortingUrl = [];
        foreach ($sortData as $order => &$data) {
            $query->modify([
                'order' => $order,
                'direction' => $data['direction']
            ]);

            $sortData[$order]['url'] = (string) $url;
        }

        $totalResults = $this->databaseManager->table('tasks')->count();
        $resultsPerPage = 3;
        $maxRange = ceil($totalResults / $resultsPerPage);
        $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT, [
            'options' => [
                'default' => 1,
                'max_range' => $maxRange
            ]
        ]);

        /* @var $db Manager */
        $db = $this->databaseManager;
        $offset = ($currentPage - 1) * $resultsPerPage;
        $query = $db
                ->table('tasks')
                ->offset($offset)
                ->limit($resultsPerPage);

        if ('' !== $currDirection && '' !== $currOrder) {
            $query->orderBy($currOrder, $currDirection);
            $paginationParams = ['order' => $currOrder, 'direction' => $currDirection];
        }

        $tasks = $query->get()->toArray();

        $pagination = pagination($baseUrl, $totalResults, $resultsPerPage, $currentPage, $paginationParams);

        return $this->view->render('tasks/index.php', [
                    'tasks' => $tasks,
                    'sortData' => $sortData,
                    'pagination' => $pagination
        ]);
    }

    /**
     * Add task
     *
     * @return string Returns the HTML
     */
    public function add() {
        $this->addLayoutParam('title', 'BeeGee | Add a task');
        $this->addLayoutParam('description', 'The page to add a task.');
        $this->addLayoutParam('breadcrumbs', [
            [
                'title' => 'Main',
                'url' => '/'
            ],
            [
                'title' => 'Add task'
            ],
        ]);

        $isSubmitted = 'send' === filter_input(INPUT_POST, 'submit');

        $task = new Task($this->databaseManager);

        $result = false;
        if ($isSubmitted) {
            $task->loadFromForm();
            $result = $task->validate();
            if ($result->isSuccess()) {
                $result = $task->add();
            }
        }

        if ($result && $result->isSuccess()) {
            redirect('/', true);
            return;
        } else {
            return $this->view->render('tasks/add.php', [
                        'result' => $result,
                        'task' => $task,
            ]);
        }
    }

    /**
     * Edit task
     *
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function edit(int $id) {
        if (!Auth::autorized()) {
            return $this->view->render('tasks/access-denied.php');
        }

        $this->addLayoutParam('title', 'BeeGee | Edit task');
        $this->addLayoutParam('description', 'Edit task.');
        $this->addLayoutParam('breadcrumbs', [
            [
                'title' => 'Main',
                'url' => '/'
            ],
            [
                'title' => 'Edit task'
            ],
        ]);

        $isSubmitted = 'send' === filter_input(INPUT_POST, 'submit');

        $task = new Task($this->databaseManager);

        $result = false;
        if ($isSubmitted) {
            $task->id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT, [
                'options' => [
                    'default' => 0
                ]
            ]);

            $result = $task->loadFromForm();
            if ($result->isSuccess()) {
                $result = $task->validate();
            }
            if ($result->isSuccess()) {
                $result = $task->edit();
            }
        } else {
            $task->loadFromDb($id);
        }

        return $this->view->render('tasks/edit.php', [
                    'task' => $task,
                    'result' => $result
        ]);
    }

    /**
     * Remove task
     *
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function remove(int $id) {
        if (!Auth::autorized()) {
            return $this->view->render('tasks/access-denied.php');
        }

        $this->addLayoutParam('breadcrumbs', [
            [
                'title' => 'Main',
                'url' => '/'
            ],
            [
                'title' => 'Remove task'
            ],
        ]);

        $isSubmitted = 'send' === filter_input(INPUT_POST, 'submit');

        $task = new Task($this->databaseManager);
        $task->loadFromDb($id);
        $result = false;
        if ($isSubmitted) {
            if (!$task->remove()) {
                $result = Result::createFail('Failed to delete task!');
            } else {
                return $this->view->render('tasks/successful-removal.php', [
                            'task' => $task,
                            'result' => $result
                ]);
            }
        }

        return $this->view->render('tasks/remove.php', [
                    'task' => $task,
                    'result' => $result
        ]);
    }

    /**
     * Look at the task
     * 
     * @param int $id Task identifier
     * @return string Returns the HTML
     */
    public function view(int $id) {
        $this->addLayoutParam('breadcrumbs', [
            [
                'title' => 'Main',
                'url' => '/'
            ],
            [
                'title' => 'Look at the task'
            ],
        ]);

        $task = new Task($this->databaseManager);
        $task->loadFromDb($id);

        return $this->view->render('tasks/view.php', [
                    'task' => $task,
        ]);
    }

    /**
     * Preview the task
     *
     * @return string Returns a JSON string
     */
    public function preview() {
        $task = new Task($this->databaseManager);
        $task->loadFromForm();

        $result = new Result('success', 'Success!', [
            'html' => $this->view->render('tasks/preview.php', [
                'task' => $task,
            ])
        ]);

        return json_encode($result->toArray());
    }

}
