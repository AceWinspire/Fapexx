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
	private $length_validator;
	private $filter_alnum;
	private $filter_alnum_no_white_sp;

	private $memcache_options;

	public function init(){
		$this->user_auth 	= Zend_Auth::getInstance();
		$this->auth_storage = $this->user_auth->getStorage();
		$this->user = $this->user_auth->getIdentity();

		$this->my_service_app  				= new My_Service_App();
		$this->my_service_contents  		= new My_Service_Contents();
		$this->my_service_users    			= new My_Service_Users();

		$this->email_validator  			= new Zend_Validate_EmailAddress();
		$this->length_validator 			= new Zend_Validate_StringLength(array('min' => 5, 'max' => 10));
		$this->filter_alnum     			= new Zend_Filter_Alnum(array('allowwhitespace' => true));
		$this->filter_alnum_no_white_sp     = new Zend_Filter_Alnum(array('allowwhitespace' => false));

		$this->memcache_options = Zend_Registry::get('memcache');
	}

	public function loginAction(){
		if($this->user ){
			$this->_redirect('index');
		}

		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$session_id =  md5(rand(1000, 10000).time());

			if (!$this->email_validator->isValid($email)) {
				My_Utilities::fmsg('error_email_not_valid', 'error');
				return;
			}

			if (!$password) {
				My_Utilities::fmsg('error_password_required', 'error');
				return;
			}

			$result = $this->my_service_users->auth( $email, $password, $session_id);
			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				My_Utilities::fmsg('Welcome', 'success');
				$this->_redirect('index');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}
		}
	}

	public function forgottenPasswordAction(){
		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			
			if (!$this->email_validator->isValid($email)) {
				My_Utilities::fmsg('error_email_not_valid', 'error');
				return;
			}

			$result = $this->my_service_users->passRecovery($email);

			if($result->success == true){
				My_Utilities::fmsg('Check your inbox.', 'success');
				$this->_redirect('user/reset-password');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}
		}
	}

	public function resetPasswordAction(){
		if ($this->_request->isPost()) {
			$password 		= $this->_getParam('password');
			$repeated_pass 	= $this->_getParam('re_password');
			$code 			= $this->_getParam('code');

			if (!$code) {
				My_Utilities::fmsg('error_verification_code_invalid', 'error');
				return;
			}
			
			if (!trim($this->filter_alnum->filter($code))) {
				My_Utilities::fmsg('error_verification_code_invalid', 'error');
				return;
			}

			if (!$this->length_validator->isValid($password)) {
				My_Utilities::fmsg('error_password_minimum_required', 'error');
				return;
			}

			if ($password != $repeated_pass) {
				My_Utilities::fmsg('error_password_not_match', 'error');
				return;
			}

			$result = $this->my_service_users->resetPassword($code, $password);

			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				My_Utilities::fmsg('data are updated', 'success');
				$this->_redirect('index');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}
		}

	}

	public function paymentAction(){
		$this->view->package_id = $this->_getParam('package_id',0);

		try {
			$memcache = new Memcache;
			$memcache->connect($this->memcache_options['host'], $this->memcache_options['port']);
		} catch (Exception $e) {
			throw new Exception('Error conecting memcache server ' . $e->getMessage());
		}	

		$memcache->set($this->memcache_options['prefix'].'package_id_clicked' . Zend_Registry::get('remote_ip'), $this->view->package_id , false);

		if($this->_request->isPost()){
			$user_id = $this->user->id;

			/*$first_name = $this->_getParam('first_name');
			$last_name = $this->_getParam('last_name');
			$card_number = $this->_getParam('card_number');
			$expiration_month = $this->_getParam('expiration_month');
			$expiration_year = $this->_getParam('expiration_year');
			$zip_code = $this->_getParam('zip_code');
			$country = $this->_getParam('country');
			$email = $this->_getParam('email');
			$package_id = $this->_getParam('hidden_package_id');*/

			$result = $this->my_service_users->charge($user_id);
			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				$this->_redirect('/');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}
		}
	}

	public function registerAction(){
		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$re_password = $this->_getParam('re_password');

			if (!$this->email_validator->isValid($email)) {
				My_Utilities::fmsg('error_email_not_valid', 'error');
				return;
			}

			if (!$password) {
				My_Utilities::fmsg('error_password_required', 'error');
				return;
			}
			if (!$re_password) {
				My_Utilities::fmsg('error_password_repeated_required', 'error');
				return;
			}
			if ($password !== $re_password) {
				My_Utilities::fmsg('error_password_not_match', 'error');
				return;
			}

			$session_id =  md5(rand(1000, 10000).time());
			$result = $this->my_service_users->register( $email, $password, $session_id);
			
			if($result->success == true){
				$user_info = $this->my_service_users->get($result->user_id);
				$this->auth_storage->write($user_info);
				$this->_redirect('user/payment');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}


		}
	}

	public function logoutAction() {
		if($this->user->id){
			$user_id = $this->user->id;
			$session->$user_id = null;

			$this->user_auth->clearIdentity();
			$this->_redirect('user/register');
		}else{
			$this->_redirect('user/login');
		}
	}

	public function profileAction() {
		if (!$this->user_auth->hasIdentity()) {
			$this->_redirect('user/register');
		}
		
		$this->view->cc_sub_packages = $this->my_service_app->getCcSubscriptionPackages();

		try {
			$memcache = new Memcache;
			$memcache->connect($this->memcache_options['host'], $this->memcache_options['port']);
		} catch (Exception $e) {
			throw new Exception('Error conecting memcache server ' . $e->getMessage());
		}	

		$this->view->package_id_clicked = $memcache->get($this->memcache_options['prefix'].'package_id_clicked' . Zend_Registry::get('remote_ip'));

	}

	public function editProfileAction() {
		if($this->_request->isPost()){
			$password = $this->_getParam('password');
			$re_password = $this->_getParam('re_password');
			$email = $this->user->email;
			$old_password = $this->user->password;
			$user_id = $this->user->id;

			if (!$this->length_validator->isValid($password)) {
				My_Utilities::fmsg('error_password_minimum_required', 'error');
				return;
			}

			if ($password != $re_password) {
				My_Utilities::fmsg('error_password_not_match', 'error');
				return;
			}

			$result = $this->my_service_users->update(array('password' => $password, 'old_password' => $old_password), $user_id);

			if($result->success == true){
				$user = $this->my_service_users->get($user_id);
				$this->auth_storage->write($user);
				$this->_redirect('user/profile');
			}else{
				My_Utilities::fmsg($result->msg, 'error');
			}
		}
	}

	public function cancelSubscriptionAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

		$user_id = $this->user->id;

		$result = $this->my_service_users->uncharge($user_id);
		if($result->success == true){
			$user = $this->my_service_users->get($user_id);
			$this->auth_storage->write($user);			
			My_Utilities::fmsg('Success!', 'success');				
		}else{
			My_Utilities::fmsg('Eror!', 'error');
		}
		$this->_redirect('/');
	}
}
