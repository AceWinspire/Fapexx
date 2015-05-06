<?php
/**
 * Index controller in default module
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic 	 <milan.marjanovic@golive.rs>
 * 
 * @version  november 2014
 *
 */
class IndexController extends Zend_Controller_Action {

	private $my_service_app;
	private $my_service_contents;
	private $my_service_users;

	public function init(){
		$this->my_service_app  		= new My_Service_App();
		$this->my_service_contents  = new My_Service_Contents();
		$this->my_service_users    	= new My_Service_Users();
	}

	public function indexAction(){
		$session = new Zend_Session_Namespace('no_android');
		$remote_ip = Zend_Registry::get('remote_ip');
		if (!isset($session->$remote_ip)) {
			$session->$remote_ip = (bool)$this->_getParam('no_android', false);
 		} 
		$this->view->featured_content = $this->my_service_contents->getFeaturedContent();
	}

	public function packagesAction(){
		$this->view->cc_sub_packages = $this->my_service_app->getCcSubscriptionPackages();
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

	public function termsAction(){
		
	}

	public function desktopAction(){
	}

	public function androidAction(){
	}

	public function loginAction(){
		if($this->_request->isPost()){
			$email = $this->_getParam('email');
			$password = $this->_getParam('password');
			$ip_address = Zend_Registry::get('remote_ip');
			$this->view->user = $this->my_service_users->auth( $email, $password, $ip_address);
		}
	}

	public function registerAction(){
		if($this->_request->isPost()){
			
		}
	}
}
