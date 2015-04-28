<?php
/* Service class for communication with backend api extends My_Service class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service_Contents extends My_Service{

	public function __construct(){
		parent::__construct();
	}

	public function getBanners($position){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-videos');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));
			$this->http_client->setParameterGet(array('position' => $position));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function getCategories(){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-categories');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function getVideos($cat_id){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-videos');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));
			$this->http_client->setParameterGet(array('cat_id' => $cat_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function getContent($content_id){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-content');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));
			$this->http_client->setParameterGet(array('content_id' => $content_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}


	public function getStreamChannels(){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-stream-channels');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function getTagVideos($tags){
		try {
			$this->http_client->setUri($this->api_url.'/content/get-tag-videos');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id' => $this->app_id));
			$this->http_client->setParameterGet(array('tags' => $tags));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}
}