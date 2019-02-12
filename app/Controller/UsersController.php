<?php
App::uses('AppController', 'Controller');
App::uses('Security', 'Utility');
 require_once(ROOT . DS . 'app' . DS .'Vendor' . DS  . 'autoload.php');

// App::import('Vendor', 'autoload.phpssss');

use Firebase\JWT\JWT;

class UsersController extends AppController {


	public function get_info() {
		if($this->request->is('post') && !empty($this->request->data['user_id'])) {
			$conditions = array('User.id' => $this->request->data['user_id']);
			$user = $this->User->find('first', compact('conditions'));
			if(!empty($user)) {
				$user   = $this->clearFields($user);
				$result = $this->getResult($user);
				return $this->out($result, $user, 'UserInfo');
			}
		}
		return $this->outFalseResponse();
	}

	public function get_basic_info() {
		if($this->request->is('post') && !empty($this->request->data['user_id'])) {
			$recursive  = -1;
			$conditions = array('User.id' => $this->request->data['user_id']);
			$user = $this->User->find('first', compact('conditions', 'recursive'));
			if(!empty($user)) {
				$user   = $this->clearFields($user);
				$user   = Set::extract($user, 'User');
				$result = $this->getResult($user);
				return $this->out($result, $user);
			}
		}
		return $this->outFalseResponse();
	}

	public function login() {
		if($this->request->is('post') && !empty($this->request->data['email']) 
			&& !empty($this->request->data['password'])) {
			App::uses('AuthComponent', 'Controller/Component');
			$conditions = array(
				'User.email'    => $this->request->data['email'],
				'User.password' => AuthComponent::password($this->request->data['password']),
			);
			$recursive = 0;
			$user = $this->User->find('first', compact('conditions','recursive'));

			if (!empty($user)) {
				$user = array('User' => $user['User']);
				$user = $this->clearFields($user);
				$result = $this->getResult($user);

				$time = time();

				$token = array(
				    'iat' => $time, // Tiempo que inició el token
				    'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
				    'data' => $user["User"]
				);

				$jwt = JWT::encode($token, Configure::read("SECRET_KEY"));


				return $this->out($result, array("access_token" => $jwt), 'Response');
			}
		}
		return $this->outFalseResponse();
	}


}
