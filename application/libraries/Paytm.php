<?php
/**
 * Name:    Paytm
 * Author:  Abhishek Awasthi
 *           abh.awasthi@gmail.com
 *           @abh-awasthi
 *
 * Added Awesomeness: Phil Sturgeon
 *
 * Created:  27.03.2021
 *
 * Description:  Modified auth system based on redux_auth with extensive customization. This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5.6 or above
 *
 * @package    CodeIgniter-Paytm
 * @author     Abhishek Awasthi
 * @link       
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Paytm
 */

class Paytm{



public function __construct()
{
    //$this->_CI =& get_instance();
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *'); 
    include("PaytmChecksum.php");
    $this->PaytmChecksum = new PaytmChecksum();

}

public function getTransactionToken($request){

 

	$paytmParams["body"] = array(
								"requestType" 	=> "Payment",
								"mid" 			=> PAYTM_MID,
								"websiteName" 	=> PAYTM_WEBSITE,
								"orderId" 		=> $request['ORDERID'],
								"callbackUrl" 	=> CALLBACK_URL,
								"txnAmount" 	=> array(
														"value" => $request['AMOUNT'],
														"currency" => "INR",
													),
								"userInfo" 		=> array(
													"custId" => $request['CUST_ID'],
												),
							);


		$generateSignature = $this->PaytmChecksum->generateSignature(json_encode($paytmParams['body'], JSON_UNESCAPED_SLASHES), PAYTM_MERCHANT_KEY);

		$paytmParams["head"] = array(
								"signature"	=> $generateSignature
							);

		$url = (PAYTM_ENVIRONMENT!=0) ? (PAYTM_PROD_HOST) : (PAYTM_STAGE_HOST)."/theia/api/v1/initiateTransaction?mid=". PAYTM_MID ."&orderId=". $request['ORDERID'];

		$getcURLResponse = $this->getcURLRequest($url, $paytmParams);

		if(!empty($getcURLResponse['body']['resultInfo']['resultStatus']) && $getcURLResponse['body']['resultInfo']['resultStatus'] == 'S'){
			$result = array('success' => true, 'orderId' => $request['ORDERID'], 'txnToken' => $getcURLResponse['body']['txnToken'], 'amount' => $request['AMOUNT'], 'message' => $getcURLResponse['body']['resultInfo']['resultMsg']);
		}else{
			$result = array('success' => false, 'orderId' => $request['ORDERID'], 'txnToken' => '', 'amount' => $request['AMOUNT'], 'message' => $getcURLResponse['body']['resultInfo']['resultMsg']);
		}
		return $result;
	
	}



	function getcURLRequest($url , $postData = array(), $headers = array("Content-Type: application/json")){

		$post_data_string = json_encode($postData, JSON_UNESCAPED_SLASHES);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		$response = curl_exec($ch);
		return json_decode($response,true);
	}



	function verifySignature($post,$merchant_key, $checksum){

		return $this->PaytmChecksum->verifySignature($post, $merchant_key, $checksum);

	}





}