<?php
require_once( "../../../../../wp-load.php" );
require_once(ABSPATH. "wp-content/plugins/buymeabeer/public/paypal.php");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descriptionId = $_REQUEST['bmabDescriptionId'];
    $selectedPQ = $_REQUEST['bmabPQId'];
    //open connection
    try{
        $paypal = new BuyMeABeerPaypal();
        $token = $paypal->createPayment($descriptionId, $selectedPQ);
    }
    catch(Exception $e){
        error_log("The 'Buy Me A Beer' Wordpress plugin has encountered an exception while creating a payment with Paypal: ");
        error_log($e);
    }
}
?>