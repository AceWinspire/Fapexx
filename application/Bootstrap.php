<?php
/**
 * Bootstrap class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic      <milan.marjanovic@golive.rs>
 * 
 * @version  november 2014
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Save all values from config to ZendRegistry
	 */
	protected function _initLocalConfigs(){
		$allconfig	 = new Zend_Config_Ini(APPLICATION_PATH.'/configs/config.ini');
		$envconfig	 = $allconfig->toArray();
		$registry	 = new Zend_Registry($envconfig[APPLICATION_ENV], ArrayObject::ARRAY_AS_PROPS);

		foreach($registry as $key => $value){
			Zend_Registry::set($key, $value);
		}
		
		Zend_Registry::set('remote_ip', $_SERVER['REMOTE_ADDR']);
	}
}