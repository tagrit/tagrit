<?php

defined('BASEPATH') || exit('No direct script access allowed');
require_once __DIR__.'/../vendor/autoload.php';
define("LB_API_DEBUG", true);
define("LB_TEXT_CONNECTION_FAILED", 'Server is unavailable at the moment, please try again.');
define("LB_TEXT_INVALID_RESPONSE", 'Server returned an invalid response, please contact support.');
define("LB_TEXT_VERIFIED_RESPONSE", 'Verified! Thanks for purchasing.');

class Flutex_admin_api
{
    public static function activate($module_name)
    {
        $module = get_instance()->app_modules->get($module_name);
		update_option('flutex_admin_api_enabled', 1);

        // if (!option_exists('flutex_admin_api_enabled') || get_option('flutex_admin_api_enabled') == 0) {
        //     $CI                   = &get_instance();
        //     $data['submit_url']   = admin_url($module['system_name']).'/VerifyPurchase/activate';
        //     $data['return_url'] = admin_url('modules/activate/'.$module['system_name']);
        //     $data['module_name']  = $module['system_name'];
        //     $data['title']        = 'Flutex Admin API Module Activation';
        //     echo $CI->load->view($module['system_name'].'/activation', $data, true);
        //     exit;
        // }
    }
    
    public function verify_license($time_based_check = true, $license = false, $client = false)
    {
		if(!empty($license)&&!empty($client)){
			$data_array =  array(
				"product_id"  => 'FD6C9535',
				"license_file" => null,
				"license_code" => $license,
				"client_name" => $client
			);
		}else{
			if(is_file(realpath(dirname(__FILE__) . '/..').'/controllers/.lic')){
				$data_array =  array(
					"product_id"  => 'FD6C9535',
					"license_file" => file_get_contents(realpath(dirname(__FILE__) . '/..').'/controllers/.lic'),
					"license_code" => null,
					"client_name" => null
				);
			}else{
				$data_array =  array();
			}
		}
		$res = array('status' => TRUE, 'message' => LB_TEXT_VERIFIED_RESPONSE);
		if($time_based_check){
			$today = date('d-m-Y');
			$current = get_option('flutex_admin_last_verification');
			if(empty($current)){
				add_option('flutex_admin_last_verification', '00-00-0000');
			}
			if(strtotime($today) >= strtotime($current)){
			    ob_start();
    			if(session_status() == PHP_SESSION_NONE){
    				session_start();
    			}
    			$type_text = '1 week';
				$get_data = $this->call_api(
					'POST',
					'https://license.branditta.net/api/verify_license', 
					json_encode($data_array)
				);
				$res = json_decode($get_data, true);
				if($res['status']==true){
					$tomo = date('d-m-Y', strtotime($today. ' + '.$type_text));
					update_option('flutex_admin_last_verification', $tomo);
				}
			ob_end_clean();
			}
		}else{
			$get_data = $this->call_api(
				'POST',
				'https://license.branditta.net/api/verify_license', 
				json_encode($data_array)
			);
			$res = json_decode($get_data, true);
		}
		
		if(!$res['status']){
		    update_option('flutex_admin_api_enabled', 0);
			@chmod(realpath(dirname(__FILE__) . '/..').'/controllers/.lic', 0777);
			if(is_writeable(realpath(dirname(__FILE__) . '/..').'/controllers/.lic')){
				unlink(realpath(dirname(__FILE__) . '/..').'/controllers/.lic');
			}
		}
		
		return $res;
	}
	
    private function call_api($method, $url, $data = null)
    {
		$curl = curl_init();
		switch ($method){
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
				if($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				if($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                         
				break;
		  	default:
		  		if($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}
		$this_server_name = getenv('SERVER_NAME')?:
			$_SERVER['SERVER_NAME']?:
			getenv('HTTP_HOST')?:
			$_SERVER['HTTP_HOST'];
		$this_http_or_https = ((
			(isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on"))or
			(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])and
				$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		)?'https://':'http://');
		$this_url = $this_http_or_https.$this_server_name;
		$this_ip = getenv('SERVER_ADDR')?:
			$_SERVER['SERVER_ADDR']?:
			$this->get_ip_from_third_party()?:
			gethostbyname(gethostname());
		curl_setopt($curl, CURLOPT_HTTPHEADER, 
			array('Content-Type: application/json', 
				'LB-API-KEY: DCEABC4EBE20D2E0E13C', 
				'LB-URL: '.$this_url, 
				'LB-IP: '.$this_ip, 
				'LB-LANG: english')
		);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30); 
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($curl);
		if(!$result&&!LB_API_DEBUG){
			$rs = array(
				'status' => FALSE, 
				'message' => LB_TEXT_CONNECTION_FAILED
			);
			return json_encode($rs);
		}
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($http_status != 200){
			if(LB_API_DEBUG){
				$temp_decode = json_decode($result, true);
				$rs = array(
					'status' => FALSE, 
					'message' => ((!empty($temp_decode['error']))?
						$temp_decode['error']:
						$temp_decode['message'])
				);
				return json_encode($rs);
			}else{
				$rs = array(
					'status' => FALSE, 
					'message' => LB_TEXT_INVALID_RESPONSE
				);
				return json_encode($rs);
			}
		}
		curl_close($curl);
		return $result;
	}
	
	private function get_ip_from_third_party()
	{
		$curl = curl_init ();
		curl_setopt($curl, CURLOPT_URL, "http://ipecho.net/plain");
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	
    public static function module_checker()
    {
        if (!\function_exists('flutex_admin_api_init') || !\function_exists('flutex_admin_api_activation')) {
            get_instance()->app_modules->deactivate(flutex_admin_api);
        }
    }
}
