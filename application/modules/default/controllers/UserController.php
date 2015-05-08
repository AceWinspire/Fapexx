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

	private $email_validator;

	public function init(){
		$this->user_auth 	= Zend_Auth::getInstance();
		$this->auth_storage = $this->user_auth->getStorage();
		$this->user = $this->user_auth->getIdentity();

		$this->my_service_app  		= new My_Service_App();
		$this->my_service_contents  = new My_Service_Contents();
		$this->my_service_users    	= new My_Service_Users();

		$this->email_validator  = new Zend_Validate_EmailAddress();
	}

	public function loginAction(){
		if($this->user ){
			$this->_redirect('index');
		}

		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$session_id =  md5(rand(1000, 10000).time());

			$result = $this->my_service_users->auth( $email, $password, $session_id);
			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				$this->_redirect('index');
			}else{
				$this->view->msg = $result->msg;
			}
		}
	}

	public function forgottenPasswordAction(){

	}

	public function resetPasswordAction(){
		
	}

	public function paymentAction(){
		$this->view->package_id = $this->_getParam('package_id');

		if($this->_request->isPost()){
			$first_name = $this->_getParam('first_name');
			$last_name = $this->_getParam('last_name');
			$card_number = $this->_getParam('card_number');
			$expiration_month = $this->_getParam('expiration_month');
			$expiration_year = $this->_getParam('expiration_year');
			$zip_code = $this->_getParam('zip_code');
			$country = $this->_getParam('country');
			$email = $this->_getParam('email');
			$package_id = $this->_getParam('hidden_package_id');
			
			var_dump('package_id:'.$package_id.'<br/>');
			var_dump($first_name.'<br/>');
			var_dump($last_name.'<br/>');
			var_dump($card_number.'<br/>');
			var_dump($expiration_month.'<br/>');
			var_dump($expiration_year.'<br/>');
			var_dump($zip_code.'<br/>');
			var_dump($country.'<br/>');
			var_dump($email.'<br/>');
			die;
		}
	}

	public function registerAction(){
		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$re_password = $this->_getParam('re_password');

			/*if (!$this->email_validator->isValid($email)) {
				My_Utilities::fmsg($this->view->translate('error_email_not_valid'));
				return;
			}

			if (!$password) {
				My_Utilities::fmsg($this->view->translate('error_password_required'));
				return;
			}
			if (!$re_password) {
				My_Utilities::fmsg($this->view->translate('error_password_repeated_required'));
				return;
			}
			if ($password !== $re_password) {
				My_Utilities::fmsg($this->view->translate('error_password_not_match'));
				return;
			}*/

			/*var_dump($email.'<br/>');
			var_dump($password.'<br/>');
			var_dump($re_password.'<br/>');
			die;*/

			$session_id =  md5(rand(1000, 10000).time());
			$result = $this->my_service_users->register( $email, $password, $session_id);
			var_dump($result);die;
			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				$this->_redirect('user/payment');
			}else{
				$this->view->msg = $result->msg;
			}


		}
	}

	public function logoutAction() {

	}

	public function profileAction() {
		/*if (!$this->user_auth->hasIdentity()) {
			$this->_redirect('user/register');
		}

		$this->view->existing 		= $this->my_service_users->get($this->user->id); //$user_info 
		$this->view->subscription 	= $this->paypal_subscriptions_model->getByUserId($this->user['id']);

		if(isset($this->user['payment_id'])){
			$this->view->payments = $this->ch_packages_model->getByPaymentId($this->user['payment_id']);
		}*/
	}

	public function editProfileAction() {
		
	}

}
