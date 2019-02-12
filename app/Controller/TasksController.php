<?php
App::uses('AppController', 'Controller');
/**
 * Tasks Controller
 *
 * @property Task $Task
 * @property PaginatorComponent $Paginator
 */
class TasksController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		if ($this->request->is('post') && !empty($this->request->data["user_id"])) {
			$tasks  = $this->Task->find("all",array("conditions"=>array("Task.user_id" => $this->request->data["user_id"])));
			$tareas = array();
			foreach ($tasks as $key => $value) {
				$tareas[] = $value["Task"];
			}

			$result = $this->getResult($tareas);
			return $this->out($result, $tareas, 'Response');			
		}
		return $this->outFalseResponse();
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
		$this->set('task', $this->Task->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Task->create();
			if ($this->Task->save($this->request->data)) {
				$this->request->data["id"] = $this->Task->id;
				$task   = $this->clearFields($this->request->data);
				$result = $this->getResult($task);
				return $this->out($result, $task, 'Response');
			} else {
				$error = $this->getValidationErrors($this->Task->validationErrors);
				return $this->outFalseResponse($error);
			}
		}
		return $this->outFalseResponse();
	}

	/**
 * add method
 *
 * @return void
 */
	public function change() {
		if ($this->request->is('post')) {
			
			$this->Task->create();
			$task = $this->Task->findById($this->request->data["id"]);
			$task["Task"]["state"] = $task["Task"]["state"] == 0 ? 1 : 0;
			$this->Task->id = $this->request->data["id"];

			if ($this->Task->save($task)) {

				$task   = $this->clearFields($task);
				$result = $this->getResult($task);
				return $this->out($result, $task["Task"], 'Response');

			} else {
				$error = $this->getValidationErrors($this->Task->validationErrors);
				return $this->outFalseResponse($error);
			}
		}
		return $this->outFalseResponse();
	}


}
