<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */


public function __construct()
{
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    parent::__construct();
    $this->load->library('paytm');
}


	public function index()
	{

		//$this->load->library('paytm');
		$request = array(
			'AMOUNT'=>10,
			'CUST_ID'=>'118181',
			'ORDERID'=>time()  /// Any order ID but must be unique in 15 min interval

		);
		$response = $this->paytm->getTransactionToken($request);
		$data['response']=$response;
		$this->load->view('payment/pay2',$data);

	}


	public function paytmcallback(){

	  if(!empty($_POST)){
      $checksum = (!empty($_POST['CHECKSUMHASH'])) ? $_POST['CHECKSUMHASH'] : '';
      $verifySignature = $this->paytm->verifySignature($_POST, PAYTM_MERCHANT_KEY, $checksum);
      $data['post']=$_POST;
      $data['verify'] = $verifySignature;
      }else{
      $data['post']= '';    
      }
      $this->load->view('payment/callback',$data);


	}




}
