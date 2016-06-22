<?php
/* Service class for communication with backend api extends My_Service class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service_Log extends My_Service{

	public function __construct(){
		parent::__construct();
	}

	public function contentViewed($content_id, $ip_address, $user_id = null){
		try {
			$this->http_client->setUri($this->api_url.'/log/content-viewed');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'		=> $this->app_id));
			$this->http_client->setParameterGet(array('content_id'	=> $content_id));
			$this->http_client->setParameterGet(array('user_id'		=> $user_id));
			$this->http_client->setParameterGet(array('ip_address'	=> $ip_address));

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

	public function contentRated($content_id, $rate, $ip_address, $user_id = null){
		try {
			$this->http_client->setUri($this->api_url.'/log/content-rated');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'		=> $this->app_id));
			$this->http_client->setParameterGet(array('content_id'	=> $content_id));
			$this->http_client->setParameterGet(array('user_id'		=> $user_id));
			$this->http_client->setParameterGet(array('rate'		=> $rate));
			$this->http_client->setParameterGet(array('ip_address'	=> $ip_address));


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
	
	public function userLogout($user_id, $ip_address){
		try {
			$this->http_client->setUri($this->api_url.'/log/user-logout');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));
			$this->http_client->setParameterGet(array('ip_address'	=> $ip_address));

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