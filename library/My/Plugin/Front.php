<?php
/**
 * Front controller plugin
 * dispatchLoopStartup method is trigered this before every call to controller
 *
 */
class My_Plugin_Front extends Zend_Controller_Plugin_Abstract {
	
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        
        $user_auth = Zend_Auth::getInstance();
        $view->user = $user_auth->getIdentity();    

        $my_service_contents  	= new My_Service_Contents();
        $my_service_app  		= new My_Service_App();
        $view->translate    = $my_service_app->getTranslate();
		$view->categories 	= $my_service_contents->getCategories();
		$view->app_settings	= $my_service_app->getInfo();

        foreach($view->translate as $key=>$value){
            Zend_Registry::set($key, $value);
        }

        $session = new Zend_Session_Namespace('no_android');
        $remote_ip = Zend_Registry::get('remote_ip');
        $no_android = isset($session->$remote_ip) ? $session->$remote_ip : false;
        $session->$remote_ip = true;
        
        $device = new My_Device($_SERVER['HTTP_USER_AGENT']);
        if($device->isMobile() && !$no_android){
            $request->setControllerName('index');
            $request->setActionName('index');
        }
    }
}