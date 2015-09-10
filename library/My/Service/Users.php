<?php
/* Service class for communication with backend api extends My_Service class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service_Users extends My_Service{

	public function __construct(){
		parent::__construct();
	}

	public function get($user_id){
		try {
			$this->http_client->setUri($this->api_url.'/segpay-users/get');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('id'	=> $user_id));

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

	public function auth($username, $password, $session_id){
		try {
			$this->http_client->setUri($this->api_url.'/segpay-users/auth');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('identity'	=> $username));
			$this->http_client->setParameterGet(array('password'	=> $password));
			$this->http_client->setParameterGet(array('session_id'	=> $session_id));

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