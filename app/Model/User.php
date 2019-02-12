<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {


	public $validate = array(
		'email' => array(
			array('rule'   => array('email'),
				'message'  => '0003',
				'on'       => "create",
				'required' => true,
			),
			array('rule'   => array('isUnique'),
				'message'  => '0004',
				'on'       => "create",
				'required' => true,
			),
		),
		'password' => array(
			array('rule'   => array('notBlank'),
				'message'  => '0005',
				'on'       => "create",
				'required' => true,
			),
		)
	);


	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['password']) 
			&& !empty($this->data[$this->alias]['password'])){
	  			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
	    return true;
	}


	public function findToUser($data = array()) {
		if(!empty($data['email'])) {
			$conditions = array('User.email' => $data['email']);
			$userId = $this->field('id', $conditions);
			if(!empty($userId)) {
				$data['to_user_id'] = $userId;
				$data['status'] = Configure::read('GIFTCARD_SEND');
			}
		}
		return $data;
	}

}
