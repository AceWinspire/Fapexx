<?php
/**
 * Class for static helper function
 *
 * @author aleksandar.markicevic <aleksandar.markicevic@golive.rs>
 * @version  December 2012
 */
class My_Utilities{
	/**
	 * Set message in FlashMessenger Helper to shof in next request
	 *
	 * @param string $message message to show in next request
	 * @param string type for css, available is: success|error|warning|info
	 */
	public static function fmsg($message, $type = 'success'){
		$flash_msg = new Zend_Controller_Action_Helper_FlashMessenger;
		$flash_msg->addMessage(array($type => $message));
	}

	/*
	 * prepere string for css class name - lower case, replace some characters
	 */
	public static function cssClassName($name) {
		$replace = array(" ", "/", "&", "š", "đ", "č", "ć", "ž", "Š", "Đ", "Č", "Ć", "Ž");
		$replace_with = array("-", "-", "-", "s", "dj", "c", "c", "z", "S", "Dj", "C", "C", "Z");

		return strtolower(str_replace($replace, $replace_with, $name));
	}
	
	public static function logAction($action_id, $params = null, $user_id = null){
		$model = new Log_Model_LogUserActions();
		$model->logAction($action_id, Zend_Registry::get('remote_ip'), $params, $user_id);
	}

	public static function sendMail($mail_to, $mail_type, $params){
		$translate_messages_model = new Locale_Model_TranslateMessages();
		$users_model			  = new Admin_Model_Users();

		//potrebno je u translate_messages ubaciti za ove keyeve values, koji ce sadrzati subject odnosno body maila. 
		//Obavezno u body ubaciti i linkove, gde bi se menjao samo verification_code
		switch ($mail_type) {
			case 'send_verification':
			$subject_key = 'send_verification_email_subject';
			$body_key	 = 'send_verification_email_body';
			$log_action_success = Zend_Registry::get('verification_mail_sent_act_id');
			$log_action_fail = Zend_Registry::get('verification_mail_not_sent_act_id');
			break;
			
			case 'password_recovery':
			$subject_key = 'password_recovery_email_subject';
			$body_key	 = 'password_recovery_email_body';
			$log_action_success = Zend_Registry::get('pass_recovery_mail_sent_act_id');
			$log_action_fail = Zend_Registry::get('pass_recovery_mail_not_sent_act_id');
			break;
		}

		$user = $users_model->getUserByEmail($mail_to);
		
		$subject = $translate_messages_model->getTranslateForLocale($subject_key);
		$body = $translate_messages_model->getTranslateForLocale($body_key);
		
		//replaces {verification_code} with code for that user
		$edited_body = str_replace('{verification_code}', $params['code'], $body);
		$edited_body = str_replace('{server_url}', Zend_Registry::get('server_url'), $edited_body);

		$mail = new Zend_Mail('UTF-8');
		$mail->addHeader('X-Mailer:', 'PHP/'.phpversion());
		$mail->addTo($mail_to);

		//pokupiti iz configa setfrom and setreplyto
		$mail->setFrom(Zend_Registry::get('email_verification_sender_email'))
			 ->setReplyTo(Zend_Registry::get('email_verification_sender_email'))
			 ->setSubject($subject)
			 ->setBodyHtml($edited_body);

		//Send it!
		if($mail->send()){
			self::logAction($log_action_success, $mail_to, $user['id']);
			return true;
		} else{
			self::logAction($log_action_fail, $mail_to, $user['id']);
			return false;
		}
	}

	public static function deleteFile($file_name) {
		if(file_exists($file_name))
		unlink($file_name);
		elseif(file_exists(WEB_PATH.$file_name))
		unlink(WEB_PATH.$file_name);
	}
	
	public static function sortMultiDimensionalArrayByValue($array, $key, $order = 'asc') {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		if($order == 'desc'){
			arsort($sorter);
		}else{
			asort($sorter);
		}
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;
		return $array;
	}

	/**
     * Get string for CSV file from DB Table
     * 
	 * @param Array $result Data to populate translated values
	 * @param Zend_Db_Rows $countries Top side of CSV file
	 * @param Zend_Db_Rows $keys Left side of CSV file
     * @param boolean $empty if we need to return widhouth translated values
	 * 
	 * @result array in format array(key=> array(country=>value), key=>...)
     * @return long string represent CSV file
     */
    public static function getCsv($result, $countries, $keys, $empty){
        $terminate  = "\n";
        $separator  = ";";
        $close      = '"';
        $escape     = "\\";
        $out        = "";
		
        if(count($countries) > 0 && count($keys) > 0){
			//Set header - all countries
			$out .= $close.str_replace($close, $escape.$close, "Key/Country").$close.$separator;
			foreach($countries as $country){
				$out .= $close.str_replace($close, $escape.$close, $country->name).$close.$separator;
			}
			
			//Trim last ',' and set new line
            $out  = substr($out, 0, -1);
            $out .= $terminate;
			
           
			foreach($keys as $key){				
				//Set left side - all keys
				$out .= $close.str_replace($close, $escape.$close, $key->key).$close.$separator;
				
				//Set translated values
				if(!$empty){
					foreach($countries as $country){
						if(isset($result[$key->key][$country->name]['value'])){
							$out .= $close.str_replace($close, $escape.$close, $result[$key->key][$country->name]).$close.$separator;
						}
						else{
							$out .= $close.str_replace($close, $escape.$close, "").$close.$separator;
						}
					}
				}
				
				//Trim last ',' and set new line
				$out  = substr($out, 0, -1);
				$out .= $terminate;
			}
        }
        return $out;
    }
	
	/**
	 * Reading data grom CSV file and prepare array for inserting into DB
	 * 
	 * @param string $file_path Temporary path to CSV file
	 * @return array data prepared to populate DB
	 */
	public static function readCsv($file_path){
		$datas		= array();
		$row		= 1;
		$countries	= array();
		
		if(($handle = fopen($file_path, "r")) !== FALSE){
			$data = fgetcsv($handle, 1000, ",");

			//if file has empty row on the top, ignore and continue
			if(!$data[0]){
				$data = fgetcsv($handle, 1000, ",");	
			}

			do{
				if($row == 1){//get countries from first row, unset first dummy record
					unset($data[0]);
					foreach($data as $one_head){
						$countries[] = $one_head;
					}
					$row++;
					continue;
				}
				
				foreach($countries as $no => $country){
					$datas[$row] = array();
					$datas[$row]['key']			 = $data[0];
					$datas[$row]['country_name'] = $country;
					$datas[$row]['value']		 = $data[$no+1];
					$row++;
				}
			}while(($data = fgetcsv($handle, 1000, ",")) !== FALSE);
		}

		return $datas;
	}
}