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

	public function init(){
		$this->my_service_app  		= new My_Service_App();
		$this->my_service_contents  = new My_Service_Contents();
	}

	public function indexAction(){
		if (!isset($_SESSION['no_android_'.Zend_Registry::get('remote_ip')])) {
			$_SESSION['no_android_'.Zend_Registry::get('remote_ip')] = (bool)$this->_getParam('no_android', false);
		} 
		
		$this->view->stream_channels = $this->my_service_contents->getStreamChannels();
	}

	public function packagesAction(){
		$this->view->cc_sub_packages = $this->my_service_app->getCcSubscriptionPackages();
	}

	public function registerAction(){
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
}
