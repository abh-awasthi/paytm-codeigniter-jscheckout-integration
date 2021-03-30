<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
  <title>Paytm JS Checkout Sample Codeigniter</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   <script type="application/javascript" crossorigin="anonymous" src="<?php echo (PAYTM_ENVIRONMENT!=0) ? (PAYTM_PROD_HOST) : (PAYTM_STAGE_HOST)?>/merchantpgpui/checkoutjs/merchants/<?php echo PAYTM_MID; ?>.js"></script>
</head>
<body>

<div class="jumbotron text-center">
  <h1>Paytm JS Checkout </h1>
</div>
  
<div class="container">
  <div class="row">
     <center><button type="button" id="blinkCheckoutPayment"  class="btn  btn-lg btn-success">Pay <?php echo $response['amount'];?></button>
      </center>
     
  </div>
</div>
<script>
  

           document.getElementById("blinkCheckoutPayment").addEventListener("click", function(){
            openBlinkCheckoutPopup("<?php echo $response['orderId'];?>","<?php echo $response['txnToken']; ?>","<?php echo $response['amount'];?>");
          }
         );

        function openBlinkCheckoutPopup(orderId, txnToken, amount)
         {
          // console.log(orderId, txnToken, amount);
          var config = {
            "root": "",
            "flow": "DEFAULT",
            "merchant": {

                       "redirect": true
                        },
            "data": {
              "orderId": orderId, 
              "token": txnToken, 
              "tokenType": "TXN_TOKEN",
              "amount": amount 
         },
            "handler": {
            
            "notifyMerchant":function notifyMerchant(eventName,data){
      console.log("notify"+data);
    } ,
            "transactionStatus":function transactionStatus(paymentStatus){
      console.log("paymentStatus => ",paymentStatus);              
    } ,
            "notifyMerchant": function(eventName,data){
              //console.log("notifyMerchant handler function called");
              //console.log("eventName => ",eventName);
              console.log("data => ",data);
              location.reload();
            } 
            }
          };
           if(window.Paytm && window.Paytm.CheckoutJS){
              // initialze configuration using init method 
              window.Paytm.CheckoutJS.init(config).then(function onSuccess() {
                // after successfully updating configuration, invoke checkoutjs
                window.Paytm.CheckoutJS.invoke();
              }).catch(function onError(error){
                //console.log("error => ",error);
              });
          }
        }


</script>





</body>
</html>