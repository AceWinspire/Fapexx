<?php
/**
 * Category controller in default module
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic 	 <milan.marjanovic@golive.rs>
 * 
 * @version  november 2014
 *
 */
class ContentController extends Zend_Controller_Action {

	private $user;
	private $user_auth;
	private $auth_storage;

	private $my_service_contents;
	private $my_service_log;
	private $my_service_app;

	private $app_settings;
	private $memcache_options;

	public function init(){
		$this->user_auth 	= Zend_Auth::getInstance();
		$this->auth_storage = $this->user_auth->getStorage();
		$this->user = $this->user_auth->getIdentity();

		$this->my_service_contents  = new My_Service_Contents();
		$this->my_service_log  		= new My_Service_Log();
		$this->my_service_app  		= new My_Service_App();

		$this->app_settings	= $this->my_service_app->getInfo();

		$this->memcache_options = Zend_Registry::get('memcache');
	}

	public function categoryAction(){	
		$this->view->cat_id = $this->_getParam('id');
		$this->view->videos = $this->my_service_contents->getVideos($this->view->cat_id);
	}

	public function videoAction(){
		$ip_address = Zend_Registry::get('remote_ip');

		$this->view->cat_id = $this->_getParam('cat_id', 0);
		$video_id = $this->_getParam('id');

		$video  =  $this->my_service_contents->getContent($video_id);

		if(!($this->user && $this->user->charged == true) && $video->is_premium){
			$video->url = $video->preview_url;
		}
		
		$this->view->existing = $video;

		if($this->view->cat_id == 0){
			$this->view->videos = $this->my_service_contents->getFeaturedContent();
		}else{
			$this->view->videos = $this->my_service_contents->getVideos($this->view->cat_id);
		}
		$this->my_service_log->contentViewed($this->view->existing->id, $ip_address);
	}

	public function streamAction(){
		$ip_address = Zend_Registry::get('remote_ip');
		
		/*try {
				$memcache = new Memcache;
				$memcache->connect($this->memcache_options['host'], $this->memcache_options['port']);
			} catch (Exception $e) {
				throw new Exception('Error conecting memcache server ' . $e->getMessage());
			}		

		$code_try_count = $memcache->get($this->memcache_options['prefix'] . $ip_address);

		$memcache->set($this->memcache_options['prefix'] . $ip_address, (($code_try_count) ? ++$code_try_count : 1), false);

		if ($code_try_count > $this->app_settings->show_subscribe_prompt_value) {
			$this->_redirect('/index/packages/');
		}*/
		
		$video_id = $this->_getParam('id');
		$this->view->existing = $this->my_service_contents->getContent($video_id);
		$this->my_service_log->contentViewed($this->view->existing->id, $ip_address);
	}
}
