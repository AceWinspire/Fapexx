<?php
/**
 * User controller in default module
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic 	 <milan.marjanovic@golive.rs>
 * 
 * @version  november 2014
 *
 */
class UserController extends Zend_Controller_Action {

	private $user;
	private $user_auth;
	private $auth_storage;

	private $my_service_app;
	private $my_service_contents;
	private $my_service_users;

	public function init(){
		$this->user_auth 	= Zend_Auth::getInstance();
		$this->auth_storage = $this->user_auth->getStorage();
		$this->user = $this->user_auth->getIdentity();

		$this->my_service_app  		= new My_Service_App();
		$this->my_service_contents  = new My_Service_Contents();
		$this->my_service_users    	= new My_Service_Users();
	}

	public function loginAction(){
		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$session_id =  md5(rand(1000, 10000).time());
			$result = $this->my_service_users->auth( $email, $password, $session_id);
			if($result['success'] == true){
				$user_info = $this->my_service_users->get($result['user_id']);
				$this->auth_storage->write($user_info);
			}else{
				$this->view->msg = $result['msg'];
			}


		}
	}

	public function forgottenPasswordAction(){

	}

	public function resetPasswordAction(){
		
	}

	public function registerAction(){
		
	}

}
