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

	

	public function termsAction(){
		
	}

	public function desktopAction(){
	}

	public function androidAction(){
	}

	
}
