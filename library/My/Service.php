<?php
/* Base Service class for communication with backend api
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service{

	protected $api_url;
	protected $app_id;

	protected $http_client;

	public function __construct(){

		$this->api_url = Zend_Registry::get('backend_api_url');
		$this->app_id  = Zend_Registry::get('app_id');

		$this->http_client = new Zend_Http_Client();
		$this->http_client->setConfig(array('keepalive' => true));
	}
}