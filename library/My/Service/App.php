<?php
/* Service class for communication with backend api extends My_Service class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service_App extends My_Service{

	public function __construct(){
		parent::__construct();
	}

	public function getInfo(){
		try {
			$this->http_client->setUri($this->api_url.'/app/get-info');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));

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

	public function getTranslate($lang){
		try {
			$this->http_client->setUri($this->api_url.'/app/get-translate');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('lang'	=> $lang));

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
	
	public function getCcSubscriptionPackages(){
		try {
			$this->http_client->setUri($this->api_url.'/app/get-cc-subscription-packages');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));

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