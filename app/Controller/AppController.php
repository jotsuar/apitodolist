<?php

App::uses('Controller', 'Controller');

require_once(ROOT . DS . 'app' . DS .'Vendor' . DS  . 'autoload.php');

use Firebase\JWT\JWT;

class AppController extends Controller {
  
	public $components = array('RequestHandler', 'Paginator', 'Session', 'Flash');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->response->header('Access-Control-Allow-Origin','*');
		$this->response->header('Access-Control-Allow-Headers','*');	
        $this->response->header('Access-Control-Allow-Methods','*');
        $this->response->header('Access-Control-Max-Age','172800');
		$this->RequestHandler->ext = 'json';
	}

	public function out($result, $values, $reponseKey=null) {
		$reponseKey = ($reponseKey != null) ? $reponseKey : $this->modelClass;
		$result[$reponseKey] = $values;
		$this->set([
		  "Response"   => $result,
		  '_serialize' => "Response"
		]);
	}

	public function getResult($values = null) {
		if(empty($values)) {
			return array('result'=> false);
		} 
		return array('result'=> true);
	}

	public function outFalseResponse($msg = null) {
		$result = array('result' => false);
		if(!empty($msg)) {
			$result = array_merge($result, $msg);
		}
		$this->set([
		  "Response"   => $result,
		  '_serialize' => "Response"
		]);
	}

	/*
    * Poner vacios los campos, cuando los valores sean nulos, para evitar problema de compatibilidad de datos
    * en los lenguajes de programacion mobile que validan los tipos de datos
    * @params Array $resultSet los datos que provienen de la consulta que retorna el modelo
    * @uses $this->__clearHiddenFields
    * @return Array $resultSet
	*/
	public function clearFields($resultSet = array()) {
		foreach ($resultSet as $modelName => &$modelValues) {
			$this->__clearHiddenFields($modelValues);
			if (is_array($modelValues)) {
				foreach ($modelValues as $field => $values) {
					if(is_array($values)) {
						foreach ($values as $valueKey => $value) {
							if($value === null) {
								$resultSet[$modelName][$field][$valueKey] = "";
							}
						}
					}elseif($values === null) {
						$resultSet[$modelName][$field]= "";
					}	
				}
			}
		}
		$this->__clearHiddenFields($resultSet);
		return $resultSet;
	}

	private function __clearHiddenFields(&$resultSet = array()) {
		if(isset($resultSet['User']['password'])) {
			unset($resultSet['User']['password']);
		}

		if(isset($resultSet['User']['role'])) {
			unset($resultSet['User']['role']);
		}

		if(isset($resultSet['password'])) {
			unset($resultSet['password']);
		}

		if(isset($resultSet['role'])) {
			unset($resultSet['role']);
		}
	}

	/*
    * Obtener un mensaje a la vez del listado de mensajes que retorna
    * un modelo, cuando no puede guardar, porque falla una validacion
    * @params Array $listErrors listado de errores que retorna el modelo
    * @return String $error
	*/
	public function getValidationErrors($listErrors = array()) {
		$error = null;
		if(!empty($listErrors)) {
		  foreach ($listErrors as $field => $errors) {
		    foreach ($errors as $errorMessage) {
		      $error = $this->__getErrorCodeMessage($errorMessage);
		      break;
		    }
		  }
		}
		return $error;
	}

	private function __getErrorCodeMessage($code = null) {
		$errors = array(
			'0001' => array('code'=>'0001', 'message'=>__('El nombre es requerido')),
			'0003' => array('code'=>'0003', 'message'=>__('El correo es requerido')),
			'0004' => array('code'=>'0004', 'message'=>__('El correo ya existe')),
			'0005' => array('code'=>'0005', 'message'=>__('La contraseña es requerida')),
			'0006' => array('code'=>'0006', 'message'=>__('Las contraseñas no coinciden')),
			'0007' => array('code'=>'0007', 'message'=>__('El usuario es requerido')),
			'0008' => array('code'=>'0008', 'message'=>__('El usuario no existe')),
			'0009' => array('code'=>'0009', 'message'=>__('Error al guardar, por favor inténtelo más tarde')),
			'0010' => array('code'=>'0010', 'message'=>__('Por favor ingrese un correo válido')),
			'0011' => array('code'=>'0011', 'message'=>__('El mensaje es requerido')),
		);
		if(!empty($errors[$code])) {
			return $errors[$code]; 
		}
		return array('message'=>$code);
	}

	public function Check($token)
    {
        if(empty($token))
        {
            return false;
        }
        
        $decode = JWT::decode(
            $token,
            Configure::read("SECRET_KEY"),
            Configure::read("ENCRYPT_KEY")
        );
        
        if($decode->exp < strtotime(date("Y-m-d h:i:s"). ' 10 hours' ))
        {
            return false;
        }

        return true;
    }

}


